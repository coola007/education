<?php

/**
 * generate business model class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BusinessModel extends AbstractCommon {

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

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->_model = $options['model'];
        $this->_table = $options['table'];
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $this->_businessModelPrefix = "Application_Model_Business";
        $this->_businessMapperPrefix = "Application_Model_Business_Mapper";
        $className = "Application_Model_Business";
        $classFile = APPLICATION_PATH . "/models/Business";
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
                ->setExtendedClass($this->_model->getClass()->getName())
                ->setDocblock($this->getClassDocblock())
                ->setMethods($this->getClassMethods());
    }

    /**
     * generate docblock
     * @return array 
     */
    public function getClassDocblock() {
        return array(
            "shortDescription" => "Class for SQL table {$this->_dbTableName} model.",
            "longDescription" => $this->_dbTableComment,
            'tags' => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => "Application_Model_Business"),
            )
        );
    }

    /**
     * generate methods
     * @return array
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array();
        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_pk = $column[YouJia_Db_Table::EXT_COLUMN_PK];
            if ($col_pk) {
                array_push($methods, array(
                    "name" => "query{$this->underscoreToCamelCase($col_name)}",
                    "visibility" => "public",
                    "body" => "\$mapper = new {$this->_businessMapperPrefix}_{$this->underscoreToCamelCase($col_pk['table'])};{$eol}return \$mapper->find(\$this->_$col_name);",
                    "docblock" => array(
                        "shortDescription" => "query table {$col_pk['table']}.",
                        "longDescription" => $col_comment,
                        "tags" => array(
                            array("name" => "return", "description" => "{$this->_businessModelPrefix}_{$this->underscoreToCamelCase($col_pk['table'])}")
                        )
                    )
                ));
            }
        }

        $dbTableClassName = explode('_', $this->_table->getClass()->getName());
        $tableNameIndex = count($dbTableClassName) - 1;

        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_fk = $column[YouJia_Db_Table::EXT_COLUMN_FK];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            if ($col_primary && $col_fk) {
                foreach ($col_fk as $fk) {
                    $tableName = $fk['table'];
                    $columnName = $fk['column'];
                    $tableClassName = $this->underscoreToCamelCase($tableName);
                    $columnClassName = $this->underscoreToCamelCase($columnName);
                    $dbTableClassName[$tableNameIndex] = $tableClassName;
                    array_push($methods, array(
                        "name" => "count{$tableClassName}By{$columnClassName}",
                        "visibility" => "public",
                        "body" => "\$table = new " . implode('_', $dbTableClassName) . ";{$eol}\$select = \$table->select()->from(\$table, new Zend_Db_Expr(\"COUNT(1) AS num\"))->where('$columnName = ?', \$this->_{$col_name});{$eol}\$stmt = \$select->query();{$eol}\$result = \$stmt->fetchColumn();{$eol}\$stmt->closeCursor();{$eol}return \$result;",
                        "docblock" => array(
                            "shortDescription" => "count table with $tableName.$columnName eq $this->_dbTableName.$col_name",
                            "tag" => array("name" => "return", "description" => "int")
                        )
                    ));
                }
            }
        }
        return $methods;
    }

}
