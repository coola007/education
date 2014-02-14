<?php

/*
 *  Copyright (c) <2014> <霖默醉语>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  version 1.0
 */

/**
 * Model of Abstract
 *
 * @author coola
 */
abstract class Application_Model_Abstract {

    /**
     * onstruct.
     *
     * @param Zend_Db_Table_Row_Abstract|Application_Model_Abstract|array $options
     * optional initialization values
     * @return void
     */
    public function __construct($options = null) {
        if ($options)
            $this->setOptions($options);
    }

    /**
     * set property
     *
     * @param string $name property name
     * @param mixed $value property value
     * @return void
     */
    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        if (!method_exists($this, $method))
            throw new Exception('Invalid model property');
        $this->$method($value);
    }

    /**
     * get property
     *
     * @param string $name property name
     * @return mixed property value
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (!method_exists($this, $method))
            throw new Exception('Invalid model property');
        return $this->$method();
    }

    /**
     * set properties
     *
     * @param Zend_Db_Table_Row_Abstract|Application_Model_Abstract|array $options
     * properties
     * @return Application_Model_Abstract
     */
    public function setOptions($options) {
        if (is_object($options) && ($options instanceof Zend_Db_Table_Row_Abstract || $options instanceof Application_Model_Abstract))
            $options = $options->toArray();
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods))
                $this->$method($value);
        }
        return $this;
    }

    /**
     * Returna a array of model properties
     *
     * @return array
     */
    abstract public function toArray();
}
