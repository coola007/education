<?php

/**
 * generate abstract model class
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BackenForm extends AbstractCommon {

    /**
     * col maps to field table
     * @var array 
     */
    protected $_colMapping = array();

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
        $className = "Admin_Form";
        $classFile = APPLICATION_PATH . "/modules/admin/forms";
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
                ->setExtendedClass('Zend_Form')
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
            'shortDescription' => "the form for {$this->_dbTableName}",
            'longDescription' => $this->_dbTableComment,
            "tags" => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => 'Admin_Form')
            )
        );
    }

    /**
     * generate properties
     * @return array
     */
    public function getClassProperties() {
        $properties = array();
        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $map = $this->generateColMapsField($column);
            $propertyName = $map['property'];
            $fieldClassName = $map['field'];

            array_push($properties, array(
                "name" => $propertyName,
                "visibility" => "protected",
                'docblock' => array(
                    "shortDescription" => $col_name,
                    "longDescription" => $col_comment,
                    "tag" => array("name" => "var", "description" => $fieldClassName)
                )
            ));
        }
        return $properties;
    }

    /**
     * generate properties
     * @return array
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array();
        $allElementName = array();
        $initElements = array();
        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_nullable = $column[YouJia_Db_Table::EXT_COLUMN_NULLABLE];
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_length = $column[YouJia_Db_Table::EXT_COLUMN_LENGTH];
            $col_scale = $column[YouJia_Db_Table::EXT_COLUMN_SCALE];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            $col_insert = $column[YouJia_Db_Table::EXT_COLUMN_INSERT];
            $col_update = $column[YouJia_Db_Table::EXT_COLUMN_UPDATE];
            $col_precision = $column[YouJia_Db_Table::EXT_COLUMN_PRECISION];
            $map = $this->generateColMapsField($column);
            $propertyName = $map['property'];
            $methodName = $map['method'];
            $fieldClassName = $map['field'];
            $col_description = $col_comment ? $col_comment : $col_name;
            $max_length = max($col_length, $col_scale, $col_precision);
            $options = array(
                "   'name'=>'$col_name'",
                "   'label'=>'" . str_replace('\'', '\\\'', $col_description) . ":'"
            );
            if (!$col_nullable && $col_insert && $col_update)
                $options[] = "   'required'=>true";
            if ($col_php == 'string') {
                if($max_length) $options[] = "   'validators' => array($eol     array('stringLength',false, array('max'=>$max_length))$eol   )";
                $options[] = "   'filters' => array($eol     array('stringTrim')$eol   )";
            }
            if ($col_php == 'int') {
                $options[] = "   'validators' => array($eol     array('int',false)$eol   )";
            }

            array_push($methods, array(
                "name" => $methodName,
                "visibility" => "public",
                'docblock' => array(
                    'shortDescription' => $col_name,
                    'longDescription' => $col_comment,
                    "tag" => array(
                        "name" => "return",
                        "description" => $fieldClassName
                    )
                ),
                'body' => "if(!\$this->{$propertyName}){{$eol}  \$this->{$propertyName} = new $fieldClassName(array({$eol}" . implode(",$eol", $options) . "{$eol}  ));{$eol}}{$eol}return \$this->{$propertyName};"
            ));
            $allElementName[] = $col_name;
            $initElements[] = "\$this->addElement(\$this->{$methodName}());";
        }

        $resetName = 'reset';
        $submitName = 'submit';
        while (in_array($resetName, $allElementName))
            $resetName .= $this->_dbTableClassName;

        $allElementName[] = $resetName;
        while (in_array($submitName, $allElementName))
            $submitName .= $this->_dbTableClassName;
        $allElementName[] = $submitName;

        $initElements[] = "\$this->addElement(new Zend_Form_Element_Reset('$resetName'));";
        $initElements[] = "\$this->addElement(new Zend_Form_Element_Submit('$submitName'));";

        array_unshift($methods, array(
            "name" => 'init',
            "visibility" => "public",
            'docblock' => array(
                'shortDescription' => 'Initialize form'
            ),
            'body' => implode(PHP_EOL, $initElements)
        ));

        return $methods;
    }

    /**
     * create and return col maps propertyName, fieldClassName, methodName
     * @param array $column
     * @return array 
     */
    private function generateColMapsField($column) {
        $ZendFormClass = new Zend_Reflection_Class("Zend_Form");
        $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
        $col_length = $column[YouJia_Db_Table::EXT_COLUMN_LENGTH];
        $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
        $map = $this->getMappingByColName($col_name);
        if ($map)
            return $map;

        $propertyName = "_$col_name";
        while ($ZendFormClass->hasProperty($propertyName) || $this->getMappingByProperty($propertyName))
            $propertyName = "_$propertyName";

        $methodName = $this->underscoreToCamelCase($col_name);
        while ($ZendFormClass->hasMethod("get$methodName") || $this->getMappingBYMethod("get$methodName"))
            $methodName = $this->_dbTableClassName . $methodName;

        $methodName = "get$methodName";

        $fieldClassName = "Zend_Form_Element_Text";
        if ($col_length > 320) {
            $fieldClassName = "Zend_Form_Element_Textarea";
        }
        if ($col_php == 'bool') {
            $fieldClassName = "Zend_Form_Element_Checkbox";
        }

        $map = array(
            'name' => $col_name,
            'property' => $propertyName,
            'method' => $methodName,
            'field' => $fieldClassName
        );

        $this->_colMapping[] = $map;
        return $map;
    }

    /**
     * query map by propertyName
     * @param string $property
     * @return null|array 
     */
    public function getMappingByProperty($property) {
        foreach ($this->_colMapping as $map) {
            if ($map['property'] === $property) {
                return $map;
            }
        }
        return null;
    }

    /**
     *  query map by methodName
     * @param string $method
     * @return null|array 
     */
    public function getMappingBYMethod($method) {
        foreach ($this->_colMapping as $map) {
            if ($map['method'] === $method) {
                return $map;
            }
        }
        return null;
    }

    /**
     * query map by column name
     * @param string $name
     * @return null|array
     */
    public function getMappingByColName($name) {
        foreach ($this->_colMapping as $map) {
            if ($map['name'] === $name) {
                return $map;
            }
        }
        return null;
    }

}