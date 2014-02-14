<?php

/**
 * generate Model class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class Model extends AbstractCommon {

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $className = "Application_Model";
        $classFile = APPLICATION_PATH . "/models";
        if ($this->_dbName) {
            $className .= "_{$this->_dbClassName}";
            $classFile .= "/{$this->_dbClassName}";
        }
        $className .= "_{$this->_dbTableClassName}";
        $classFile .= "/{$this->_dbTableClassName}.php";
        $this->getClass()->setName($className);
        $this->setFilename($classFile);
        $this->getClass()
                ->setExtendedClass('Application_Model_Abstract')
                ->setDocblock($this->getClassDocblock())
                ->setProperties($this->getClassProperties())
                ->setMethods($this->getClassMethods());
    }

    /**
     * class docblock
     * @return array 
     */
    public function getClassDocblock() {
        $tags = array(
            array(
                "name" => "category",
                "description" => "YouJia"
            ),
            array(
                "name" => "package",
                "description" => "Application_Model"
            )
        );
        foreach ($this->_dbTableColumns as $column) {
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            array_push($tags, array(
                "name" => "property",
                "description" => "$col_php \$$col_name $col_comment"
            ));
        }
        return array(
            "shortDescription" => "Class for SQL table {$this->_dbTableName} model.",
            "longDescription" => $this->_dbTableComment,
            "tags" => $tags
        );
    }

    /**
     * properties
     * @return array
     */
    public function getClassProperties() {
        $eol = PHP_EOL;
        $properties = array();
        foreach ($this->_dbTableColumns as $column) {
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_default = $column[YouJia_Db_Table::EXT_COLUMN_DEFAULT];
            $col_type = $column[YouJia_Db_Table::EXT_COLUMN_TYPE];
            $col_length = $column[YouJia_Db_Table::EXT_COLUMN_LENGTH];
            $col_scale = $column[YouJia_Db_Table::EXT_COLUMN_SCALE];
            $col_precision = $column[YouJia_Db_Table::EXT_COLUMN_PRECISION];
            $col_unsigned = $column[YouJia_Db_Table::EXT_COLUMN_UNSIGNED];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            $col_identity = $column[YouJia_Db_Table::EXT_COLUMN_IDENTITY];
            $col_unique = $column[YouJia_Db_Table::EXT_COLUMN_UNIQUE];
            $col_nullable = $column[YouJia_Db_Table::EXT_COLUMN_NULLABLE];
            /* push protected property to array of protected properties */
            $longDescription = "sql type: $col_type";
            if ($col_default)
                $longDescription .= "{$eol}default: $col_default";
            if ($col_length)
                $longDescription .= "{$eol}length: $col_length";
            if ($col_scale)
                $longDescription .= "{$eol}scale: $col_scale";
            if ($col_precision)
                $longDescription .= "{$eol}precision: $col_precision";
            if ($col_unsigned)
                $longDescription .= "{$eol}unsigned";
            if ($col_primary)
                $longDescription .= "{$eol}primary";
            if ($col_identity)
                $longDescription .= "{$eol}identity";
            if ($col_unique)
                $longDescription .= "{$eol}unique";
            if ($col_nullable)
                $longDescription .= "{$eol}nullable";

            array_push($properties, array(
                "name" => "_$col_name",
                "visibility" => "protected",
                "docblock" => array(
                    "shortDescription" => $col_comment,
                    "longDescription" => $longDescription,
                    "tag" => array(
                        "name" => "var",
                        "description" => $col_php
                    )
                )
            ));
        }
        return $properties;
    }

    /**
     * class methods
     * @return array 
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array(
//            array(
//                "name" => "__construct",
//                "visibility" => "public",
//                "parameter" => array(
//                    "name" => "options",
//                    "defaultValue" => null
//                ),
//                "body" => "parent::__construct(\$options);",
//                "docblock" => array(
//                    "shortDescription" => "onstruct.",
//                    "tags" => array(
//                        array(
//                            "name" => "param",
//                            "description" => "Zend_Db_Table_Row_Abstract|{$this->getClass()->getName()}|array \$options optional initialization values "
//                        ),
//                        array(
//                            "name" => "return",
//                            "description" => "void"
//                        )
//                    )
//                )
//            )
        );
        $toArray_items = array();

        foreach ($this->_dbTableColumns as $column) {
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_default = $column[YouJia_Db_Table::EXT_COLUMN_DEFAULT];
            $col_type = $column[YouJia_Db_Table::EXT_COLUMN_TYPE];
            $col_length = $column[YouJia_Db_Table::EXT_COLUMN_LENGTH];
            $col_scale = $column[YouJia_Db_Table::EXT_COLUMN_SCALE];
            $col_precision = $column[YouJia_Db_Table::EXT_COLUMN_PRECISION];
            $col_unsigned = $column[YouJia_Db_Table::EXT_COLUMN_UNSIGNED];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            $col_identity = $column[YouJia_Db_Table::EXT_COLUMN_IDENTITY];
            $col_unique = $column[YouJia_Db_Table::EXT_COLUMN_UNIQUE];
            $col_nullable = $column[YouJia_Db_Table::EXT_COLUMN_NULLABLE];
            $col_method_suffix = ucfirst($col_name);

            $longDescription = "sql type: $col_type";
            if ($col_default)
                $longDescription .= "{$eol}default: $col_default";
            if ($col_length)
                $longDescription .= "{$eol}length: $col_length";
            if ($col_scale)
                $longDescription .= "{$eol}scale: $col_scale";
            if ($col_precision)
                $longDescription .= "{$eol}precision: $col_precision";
            if ($col_unsigned)
                $longDescription .= "{$eol}unsigned";
            if ($col_primary)
                $longDescription .= "{$eol}primary";
            if ($col_identity)
                $longDescription .= "{$eol}identity";
            if ($col_unique)
                $longDescription .= "{$eol}unique";
            if ($col_nullable)
                $longDescription .= "{$eol}nullable";


            $cast_code = "($col_php)";
            if (!in_array($col_php, array("int", "bool", "float", "double", "real", "string", "object")))
                $cast_code = "";
            /* set model property value */
            array_push($methods, array(
                "name" => "set$col_method_suffix",
                "visibility" => "public",
                "parameter" => array("name" => $col_name),
                "body" => "\$this->_$col_name = $cast_code \$$col_name;{$eol}return \$this;",
                "docblock" => array(
                    "shortDescription" => $col_comment,
                    "longDescription" => $longDescription,
                    "tags" => array(
                        array(
                            "name" => "param",
                            "description" => "$col_php \$$col_name"
                        ),
                        array(
                            "name" => "return",
                            "description" => $this->getClass()->getName()
                        )
                    )
                )
            ));
            /* get Model property value */
            array_push($methods, array(
                "name" => "get$col_method_suffix",
                "visibility" => "public",
                "body" => "return \$this->_$col_name;",
                "docblock" => array(
                    "shortDescription" => $col_comment,
                    "longDescription" => $longDescription,
                    "tag" => array(
                        "name" => "return",
                        "description" => $col_php
                    )
                )
            ));

            /* push item to toArray items */
            array_push($toArray_items, "'$col_name' => \$this->_$col_name");
        }

        /* function of toArray() */
        array_push($methods, array(
            "name" => "toArray",
            "visibility" => "public",
            "body" => "return array($eol" . implode(",$eol", $toArray_items) . "$eol);",
            "docblock" => array(
                "shortDescription" => "Returns the {$this->getClass()->getName()} properties of the entity",
                "tag" => array(
                    "name" => "return",
                    "description" => "array"
                )
            )
        ));

        /* function of setOptions($options) */
//        array_push($methods, array(
//            "name" => "setOptions",
//            "visibility" => "public",
//            "parameter" => array("name" => 'options'),
//            "body" => "return parent::setOptions(\$options);",
//            "docblock" => array(
//                "shortDescription" => "set properties",
//                "tags" => array(
//                    array(
//                        "name" => "param",
//                        "description" => "Zend_Db_Table_Row_Abstract|{$this->getClass()->getName()}|array \$options properties"
//                    ),
//                    array(
//                        "name" => "return",
//                        "description" => $this->getClass()->getName()
//                    )
//                )
//            )
//        ));

        return $methods;
    }

}
