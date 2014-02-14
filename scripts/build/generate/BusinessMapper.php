<?php

/**
 * generate business mapper class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BusinessMapper extends AbstractCommon {

    /**
     * business model class name prefix
     * @var string 
     */
    protected $_businessModelPrefix = null;

    /**
     * business mapper class name prefix
     * @var string
     */
    protected $_businessMapperPrefix = null;

    /**
     *  model file
     * @var AbstractCommon 
     */
    protected $_model;

    /**
     * table file
     * @var AbstractCommon 
     */
    protected $_table;

    /**
     * mapper file
     * @var AbstractCommon
     */
    protected $_mapper;

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->_model = $options['model'];
        $this->_table = $options['table'];
        $this->_mapper = $options['mapper'];
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $this->_businessModelPrefix = "Application_Model_Business";
        $this->_businessMapperPrefix = "Application_Model_Business_Mapper";
        $className = "Application_Model_Business_Mapper";
        $classFile = APPLICATION_PATH . "/models/Business/Mapper";
        if ($this->_dbName) {
            $this->_businessModelPrefix .= "_{$this->_dbClassName}";
            $this->_businessMapperPrefix .= "_{$this->_dbClassName}";
            $className .= "_{$this->_dbClassName}";
            $classFile .= "/{$this->_dbClassName}";
        }
        $className .= "_{$this->_dbTableClassName}";
        $classFile .= "/{$this->_dbTableClassName}.php";
        $this->getClass()->setName($className);
        $this->setFilename($classFile);
        $this->getClass()
                ->setExtendedClass($this->_mapper->getClass()->getName())
                ->setDocblock($this->getClassDocblock())
                ->setProperties($this->getClassProperties())
                ->setMethods($this->getClassMethods());
    }

    /**
     * generate dockblock
     * @return array  
     */
    public function getClassDocblock() {
        return array(
            "shortDescription" => "business model mapper.",
            "longDescription" => $this->_dbTableComment,
            "tags" => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => "Application_Model_Business"),
                array("name" => "subpackage", "description" => "Mapper")
            )
        );
    }

    /**
     * generate properties
     * @return array
     */
    public function getClassProperties() {
        return array(
            array(
                "name" => "_dbModel_class",
                "visibility" => "protected",
                "defaultValue" => $this->_model->getClass()->getName(),
                "docblock" => array(
                    "shortDescription" => "Class name for SQL table data model",
                    "tag" => array("name" => "var", "description" => "string")
                )
            )
        );
    }

    /**
     * generate properties
     * @return array
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array();
        $insert_unset_items = array();
        $update_unset_items = array();
        $primary_col_names = array();
        $primary_col_names_vars = array();
        $primary_col_phps = array();
        $find_tags = array();
        $find_condition = array();
        $primary_condition = array();

        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            $col_unique = $column[YouJia_Db_Table::EXT_COLUMN_UNIQUE];
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_insert = $column[YouJia_Db_Table::EXT_COLUMN_INSERT];
            $col_update = $column[YouJia_Db_Table::EXT_COLUMN_UPDATE];
            $col_name_var = ucfirst($col_name);
            $col_primary_position = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY_POSITION];

            if (!$col_insert)
                array_push($insert_unset_items, "unset(\$data['$col_name']);");
            if (!$col_update)
                array_push($update_unset_items, "unset(\$data['$col_name']);");

            if ($col_primary) {
                $primary_col_names[] = $col_name;
                $primary_col_names_vars[] = $col_name_var;
                $primary_col_phps[] = $col_php;
                $find_condition[] = "func_get_arg(" . ($col_primary_position - 1) . ")";
                $find_tags[] = array("name" => "param", "description" => "$col_php \${$col_name}");
                $primary_condition[] = "\"$col_name = ?\" => \$model->get$col_name_var()";
            }

            if ($col_unique && !$col_primary) {
                array_push($methods, array(
                    "name" => "findBy$col_name_var",
                    "visibiliy" => "public",
                    "parameter" => array("name" => $col_name),
                    "body" => "\$result = \$this->fetchAll(array('$col_name = ?' => \$$col_name), null, 1);{$eol}return count(\$result) ? reset(\$result) : null;",
                    "docblock" => array(
                        "shortDescription" => "query by $col_name with unique values",
                        'longDescription' => $col_comment,
                        "tags" => array(
                            array("name" => "param", "description" => "$col_php \$$col_name"),
                            array("name" => "return", "description" => $this->_model->getClass()->getName()),
                            array("name" => "throws", "description" => "Zend_Db_Table_Exception")
                        )
                    )
                ));
            }
        }

        $find_tags[] = array("name" => "return", "description" => $this->_model->getClass()->getName());
        array_push($methods, array(
            "name" => "find",
            "visibility" => "public",
            "body" => "return parent::find(" . implode(", ", $find_condition) . ");",
            "docblock" => array(
                "shortDescription" => "Fetches rows by primary key",
                "tags" => $find_tags
            )
        ));

        array_push($methods, array(
            "name" => "fetchAll",
            "visibility" => "public",
            "parameters" => array(
                array("name" => "where", "defaultValue" => null),
                array("name" => "order", "defaultValue" => null),
                array("name" => "count", "defaultValue" => null),
                array("name" => "offset", "defaultValue" => null)
            ),
            "body" => "return parent::fetchAll(\$where, \$order, \$count, \$offset);",
            "docblock" => array(
                "shortDescription" => "Fetches all rows.",
                "longDescription" => "Honors the Zend_Db_Adapter fetch mode.",
                "tags" => array(
                    array("name" => "param", "description" => "string|array|Zend_Db_Table_Select \$where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object."),
                    array("name" => "param", "description" => "string|array                      \$order  OPTIONAL An SQL ORDER clause."),
                    array("name" => "param", "description" => "int                               \$count  OPTIONAL An SQL LIMIT count."),
                    array("name" => "param", "description" => "int                               \$offset OPTIONAL An SQL LIMIT offset."),
                    array("name" => "return", "description" => "array|{$this->_model->getClass()->getName()}")
                )
            )
        ));

//        array_push($methods, array(
//            "name" => "setDbModelClass",
//            "visibility" => "public",
//            "parameter" => array("name" => "className"),
//            "body" => "return parent::setDbModelClass(\$className);",
//            "docblock" => array(
//                "shortDescription" => "Set Class name for data model interface",
//                "tags" => array(
//                    array("name" => "param", "description" => "string \$className class name"),
//                    array("name" => "return", "description" => $this->getClass()->getName())
//                )
//            )
//        ));
//        array_push($methods, array(
//            "name" => "setDbTable",
//            "visibility" => "public",
//            "parameter" => array("name" => "dbTable"),
//            "body" => "return parent::setDbTable(\$dbTable);",
//            "docblock" => array(
//                "shortDescription" => "Set Class for SQL table interface",
//                "tags" => array(
//                    array("name" => "param", "description" => "string|{$this->_table->getClass()->getName()} \$dbTable"),
//                    array("name" => "return", "description" => $this->getClass()->getName())
//                )
//            )
//        ));
        return $methods;
    }

}
