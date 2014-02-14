<?php

/**
 * generate dbtable class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class Table extends AbstractCommon {

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $className = "Application_Model_DbTable";
        $classFile = APPLICATION_PATH . "/models/DbTable";
        if ($this->_dbName) {
            $className .= "_{$this->_dbClassName}";
            $classFile .= "/{$this->_dbClassName}";
        }
        $className .= "_{$this->_dbTableClassName}";
        $classFile .= "/{$this->_dbTableClassName}.php";
        $this->getClass()->setName($className);
        $this->setFilename($classFile);
        $this->getClass()
                ->setExtendedClass('Zend_Db_Table_Abstract')
                ->setDocblock($this->getClassDocblock())
                ->setProperties($this->getClassProperties())
                ->setMethods($this->getClassMethods());
    }

    /**
     * class docblock
     * @return array 
     */
    public function getClassDocblock() {
        return array(
            "shortDescription" => "Class for SQL table {$this->_dbTableName} interface.",
            "longDescription" => $this->_dbTableComment,
            "tags" => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => "Application_Model"),
                array("name" => "subpackage", "description" => "DbTable")
            )
        );
    }

    /**
     * properties
     * @return array
     */
    public function getClassProperties() {
        return array(
            array(
                "name" => "_name",
                "visibility" => "protected",
                "defaultValue" => $this->_dbTableName,
                "docblock" => array(
                    "shortDescription" => "The table name.",
                    "tag" => array("name" => "var", "description" => "string")
                )
            )
        );
    }

    /**
     * class methods
     * @return array 
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array();
        if ($this->_dbName) {
            $longDescription = "Supported params for \$config are:";
            $longDescription .= "{$eol}Supported params for \$config are:";
            $longDescription .= "{$eol}- db              = user-supplied instance of database connector,";
            $longDescription .= "{$eol}                    or key name of registry instance.";
            $longDescription .= "{$eol}- name            = table name.";
            $longDescription .= "{$eol}- primary         = string or array of primary key(s).";
            $longDescription .= "{$eol}- rowClass        = row class name.";
            $longDescription .= "{$eol}- rowsetClass     = rowset class name.";
            $longDescription .= "{$eol}- referenceMap    = array structure to declare relationship";
            $longDescription .= "{$eol}                    to parent tables.";
            $longDescription .= "{$eol}- dependentTables = array of child tables.";
            $longDescription .= "{$eol}- metadataCache   = cache for information from adapter describeTable().";
            array_push($methods, array(
                "name" => "__construct",
                "visibility" => "public",
                "parameter" => array(
                    "name" => "config",
                    "defaultValue" => array()
                ),
                "body" => "\$this->_db = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getPluginResource('multidb')->getDb('{$this->_dbName}');{$eol}parent::__construct(\$config);",
                "docblock" => array(
                    "shortDescription" => "Constructor.",
                    "LongDescription" => $longDescription,
                    "tags" => array(
                        array(
                            "name" => "param",
                            "description" => "mixed \$config Array of user-specified config options, or just the Db Adapter."
                        ),
                        array(
                            "name" => "return",
                            "description" => "void"
                        )
                    )
                )
            ));
        }
        return $methods;
    }

}