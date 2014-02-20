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
 * The model of Certificates type
 *
 * @author coola
 */
class Application_Model_CertificateType extends Application_Model_Abstract {

    /**
     * primary key
     *
     * @var int
     */
    protected $_id = NULL;

    /**
     * length:24
     *
     * @var string
     */
    protected $_name = NULL;

    /**
     * id
     * @return int
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * 中文名
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * 设置中文名
     * @param string $name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * 返回数组数据
     * @return array
     */
    public function toArray() {
        return array(
            'id'   => $this->_id,
            'name' => $this->_name,
        );
    }

}
