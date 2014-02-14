<?php

/**
 * generate abstract model class
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class AbstractModel extends AbstractCommon {

    /**
     * __construct()
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->setFilename(APPLICATION_PATH . "/models/Abstract.php");
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $this->getClass()->setName('Application_Model_Abstract');
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
        return array();
    }

    /**
     * class docblock
     * @return array 
     */
    public function getClassDocblock() {
        return array(
            "shortDescription" => "abstract class for SQL table model interface.",
            "tags" => array(
                array(
                    "name" => "category",
                    "description" => "YouJia"
                ),
                array(
                    "name" => "package",
                    "description" => "Application_Model"
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
        return array(
            /* the fun of construct initialization */
            array(
                "name" => "__construct",
                "visibility" => "public",
                "parameter" => array(
                    "name" => "options",
                    "defaultValue" => null
                ),
                "body" => "if (\$options)$eol    \$this->setOptions(\$options);",
                "docblock" => array(
                    "shortDescription" => "onstruct.",
                    "tags" => array(
                        array(
                            "name" => "param",
                            "description" => "Zend_Db_Table_Row_Abstract|Application_Model_Abstract|array \$options optional initialization values "
                        ),
                        array(
                            "name" => "return",
                            "description" => "void"
                        )
                    )
                )
            ),
            /* the function of set model property value */
            array(
                "name" => "__set",
                "visibility" => "public",
                "parameters" => array(
                    array("name" => "name"),
                    array("name" => "value")
                ),
                "body" => "\$method = 'set' . ucfirst(\$name);{$eol}if (!method_exists(\$this, \$method))$eol    throw new Exception('Invalid model property');{$eol}\$this->\$method(\$value);",
                "docblock" => array(
                    "shortDescription" => "set property",
                    "tags" => array(
                        array("name" => "param", "description" => "string \$name property name"),
                        array("name" => "param", "description" => "mixed \$value property value"),
                        array("name" => "return", "description" => "void")
                    )
                )
            ),
            /* the function of get model property value */
            array(
                "name" => "__get",
                "visibility" => "public",
                "parameter" => array("name" => "name"),
                "body" => "\$method = 'get' . ucfirst(\$name);{$eol}if (!method_exists(\$this, \$method))$eol    throw new Exception('Invalid model property');{$eol}return \$this->\$method();",
                "docblock" => array(
                    "shortDescription" => "get property",
                    "tags" => array(
                        array("name" => "param", "description" => "string \$name property name"),
                        array("name" => "return", "description" => "mixed property value")
                    )
                )
            ),
            /* the function of set model properties */
            array(
                "name" => "setOptions",
                "visibility" => "public",
                "parameter" => array("name" => "options"),
                "body" => "if (is_object(\$options) && (\$options instanceof Zend_Db_Table_Row_Abstract || \$options instanceof Application_Model_Abstract)){$eol}    \$options = \$options->toArray();{$eol}\$methods = get_class_methods(\$this);{$eol}foreach (\$options as \$key => \$value) {{$eol}    \$method = 'set' . ucfirst(\$key);{$eol}    if (in_array(\$method, \$methods)){$eol}        \$this->\$method(\$value);{$eol}}{$eol}return \$this;",
                "docblock" => array(
                    "shortDescription" => "set properties",
                    "tags" => array(
                        array("name" => "param", "description" => "Zend_Db_Table_Row_Abstract|Application_Model_Abstract|array \$options properties"),
                        array("name" => "return", "description" => "Application_Model_Abstract")
                    )
                )
            ),
            /* the abstract function of conversion the class of model to a array */
            new YouJia_CodeGenerator_Php_Method(array(
                "name" => "toArray",
                "abstract" => true,
                "visibility" => "public",
                "docblock" => array(
                    "shortDescription" => "Returna a array of model properties",
                    "tag" => array("name" => "return", "description" => "array")
                )
            ))
        );
    }

}
