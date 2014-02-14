<?php

/**
 * generate abstract mapper class
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class AbstractMapper extends AbstractCommon {

    /**
     * __construct()
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->setFilename(APPLICATION_PATH . "/models/mappers/Abstract.php");
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $this->getClass()->setName('Application_Model_Mapper_Abstract');
        $this->getClass()
                ->setAbstract(true)
                ->setDocblock($this->getClassDocblock())
                ->setProperties($this->getClassProperties())
                ->setMethods($this->getClassMethods());
    }

    /**
     * properties
     * @return array
     */
    public function getClassProperties() {
        return array(
            array(
                "name" => "_dbTable",
                "visibility" => "protected",
                "docblock" => array(
                    "shortDescription" => "the entity of SQL table interface Class.",
                    "tag" => array("name" => "var", "description" => "Zend_Db_Table_Abstract")
                )
            ),
            array(
                "name" => "_dbTable_class",
                "visibility" => "protected",
                "docblock" => array(
                    "shortDescription" => "Class name for SQL table interface",
                    "tag" => array("name" => "var", "description" => "string")
                )
            ),
            array(
                "name" => "_dbModel_class",
                "visibility" => "protected",
                "docblock" => array(
                    "shortDescription" => "Class name for SQL table data model",
                    "tag" => array("name" => "var", "description" => "string")
                )
            )
        );
    }

    /**
     * class docblock
     * @return array 
     */
    public function getClassDocblock() {
        return array(
            "shortDescription" => "abstract model mapper",
            "tags" => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => "Application_Model"),
                array("name" => "subpackage", "description" => "Mapper")
            )
        );
    }

    /**
     * class methods
     * @return array 
     */
    public function getClassMethods() {
        $class_name = $this->getClass()->getName();
        $eol = PHP_EOL;

        $find_shortDescription = "Fetches rows by primary key.  The argument specifies one or more primary";
        $find_shortDescription .= "{$eol}key value(s).  To find multiple rows by primary key, the argument must";
        $find_shortDescription .= "{$eol}be an array.";

        $find_longDescription = "This method accepts a variable number of arguments.  If the table has a";
        $find_longDescription .= "{$eol}multi-column primary key, the number of arguments must be the same as";
        $find_longDescription .= "{$eol}the number of columns in the primary key.  To find multiple rows in a";
        $find_longDescription .= "{$eol}table with a multi-column primary key, each argument must be an array";
        $find_longDescription .= "{$eol}with the same number of elements.";
        $find_longDescription .= "{$eol}{$eol}The find() method always returns a Rowset object, even if only one row";
        $find_longDescription .= "{$eol}was found.";

        return array(
            array(
                "name" => "setDbModelClass",
                "visibility" => "public",
                "parameter" => array("name" => "className"),
                "body" => "\$this->_dbModel_class = \$className;{$eol}return \$this;",
                "docblock" => array(
                    "shortDescription" => "Set Class name for data model interface",
                    "tags" => array(
                        array("name" => "param", "description" => "string \$className class name"),
                        array("name" => "return", "description" => $class_name)
                    )
                )
            ),
            array(
                "name" => "getDbModelClass",
                "visibility" => "public",
                "body" => "return \$this->_dbModel_class;",
                "docblock" => array(
                    "shortDescription" => "Get Class name for data model interface",
                    "tags" => array(
                        array("name" => "return", "description" => 'string')
                    )
                )
            ),
            array(
                "name" => "setDbTable",
                "visibility" => "public",
                "parameter" => array("name" => "dbTable"),
                "body" => "if (is_string(\$dbTable))$eol    \$dbTable = new \$dbTable();{$eol}if (!(\$dbTable instanceof Zend_Db_Table_Abstract))$eol    throw new Exception('invalid Table interface');{$eol}\$this->_dbTable = \$dbTable;{$eol}return \$this;",
                "docblock" => array(
                    "shortDescription" => "Set Class for SQL table interface",
                    "tags" => array(
                        array("name" => "param", "description" => "string|Zend_Db_Table_Abstract \$dbTable"),
                        array("name" => "return", "description" => $class_name),
                        array("name" => "throws", "description" => "Exception")
                    )
                )
            ),
            array(
                "name" => "getDbTable",
                "visibility" => "public",
                "body" => "if (!\$this->_dbTable)$eol    \$this->setDbTable(\$this->_dbTable_class);{$eol}return \$this->_dbTable;",
                "docblock" => array(
                    "shortDescription" => "Get Class for SQL table interface",
                    "tag" => array("name" => "return", "description" => "Zend_Db_Table_Abstract")
                )
            ),
            array(
                "name" => "find",
                "visibility" => "public",
                "body" => "\$result = call_user_func_array(array(\$this->getDbTable(), \"find\"), func_get_args());{$eol}return count(\$result) ? new \$this->_dbModel_class(\$result->current()) : null;",
                "docblock" => array(
                    "shortDescription" => $find_shortDescription,
                    "longDescription" => $find_longDescription,
                    "tags" => array(
                        array("name" => "param", "description" => "mixed \$key The value(s) of the primary keys."),
                        array("name" => "return", "description" => "Application_Model_Abstract Row(s) matching the criteria."),
                        array("name" => "throws", "description" => "Zend_Db_Table_Exception")
                    )
                )
            ),
            array(
                "name" => "fetchAll",
                "visibility" => "public",
                "parameters" => array(
                    array("name" => "where", "defaultValue" => null),
                    array("name" => "order", "defaultValue" => null),
                    array("name" => "count", "defaultValue" => null),
                    array("name" => "offset", "defaultValue" => null)
                ),
                "body" => "\$rows = \$this->getDbTable()->fetchAll(\$where, \$order, \$count, \$offset);{$eol}\$entries = array();{$eol}foreach (\$rows as \$row)$eol    array_push(\$entries, new \$this->_dbModel_class(\$row));{$eol}return \$entries;",
                "docblock" => array(
                    "shortDescription" => "Fetches all rows.",
                    "longDescription" => "Honors the Zend_Db_Adapter fetch mode.",
                    "tags" => array(
                        array("name" => "param", "description" => "string|array|Zend_Db_Table_Select \$where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object."),
                        array("name" => "param", "description" => "string|array                      \$order  OPTIONAL An SQL ORDER clause."),
                        array("name" => "param", "description" => "int                               \$count  OPTIONAL An SQL LIMIT count."),
                        array("name" => "param", "description" => "int                               \$offset OPTIONAL An SQL LIMIT offset."),
                        array("name" => "return", "description" => "array|Application_Model_Abstract The row results per the Zend_Db_Adapter fetch mode.")
                    )
                )
            ),
            new YouJia_CodeGenerator_Php_Method(array(
                "name" => "update",
                "visibility" => "public",
                'abstract' => true,
                "parameter" => array("name" => "model"),
                "docblock" => array(
                    "shortDescription" => "Updates existing model.",
                    "tags" => array(
                        array("name" => "param", "description" => "Application_Model_Abstract \$model  Sql table data model"),
                        array("name" => "return", "description" => "int          The number of rows updated.")
                    )
                )
            )),
            new YouJia_CodeGenerator_Php_Method(array(
                "name" => "remove",
                "visibility" => "public",
                'abstract' => true,
                "parameter" => array("name" => "model"),
                "docblock" => array(
                    "shortDescription" => "remove existing model.",
                    "tags" => array(
                        array("name" => "param", "description" => "Application_Model_Abstract \$model  Sql table data model"),
                        array("name" => "return", "description" => "int          The number of rows deleted.")
                    )
                )
            )),
            new YouJia_CodeGenerator_Php_Method(array(
                "name" => "insert",
                "visibility" => "public",
                'abstract' => true,
                "parameter" => array("name" => "model"),
                "docblock" => array(
                    "shortDescription" => "Inserts a new model.",
                    "tags" => array(
                        array("name" => "param", "description" => "Application_Model_Abstract \$model  Sql table data model"),
                        array("name" => "return", "description" => "mixed         The primary key of the row inserted.")
                    )
                )
            ))
        );
    }

}
