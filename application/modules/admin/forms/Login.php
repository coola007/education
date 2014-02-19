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
 * user for Logining
 *
 * @author coola
 */
class Admin_Form_Login extends Zend_Form {

    /**
     * id
     * 
     * @var int
     */
    protected $_id;

    /**
     * password
     *
     * @var string
     */
    protected $_pass;

    /**
     * admin_name
     *
     * @var string
     */
    protected $_name;

    public function init() {
        parent::init();
        $this->addElement($this->getId())
                ->addElement($this->getPass())
                ->addElement($this->getName());
    }

    /**
     * 管理员编号
     * 
     * @return Zend_Form_Element_Text
     */
    public function getId() {
        if (!$this->_id) {
            $this->_id = new Zend_Form_Element_Text('id', array('label' => 'ID: '));
            $validate = new Zend_Validate_Int();
            $validate->setMessage('编号必须是数字');
            $this->_id->addValidator($validate);
        }
        return $this->_id;
    }

    /**
     * 管理员编号
     *
     * @return Zend_Form_Element_Text
     */
    public function getName() {
        if (!$this->_name) {
            $this->_name = new Zend_Form_Element_Text('name', array('label' => '名字: '));
//            $validate = new Zend_Validate_Int();
//            $validate->setMessage('编号必须是数字');
//            $this->_id->addValidator($validate);
        }
        return $this->_name;
    }

    /**
     * 管理员密码
     *
     * @return Zend_Form_Element_Password
     */
    public function getPass() {
        if (!$this->_pass) {
            $this->_pass = new Zend_Form_Element_Password('pass', array('label' => '密码: '));
            $validate = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20));
            $validate->setMessage('密码长度不正确');
            $this->_pass->addValidator($validate);
        }
        return $this->_pass;
    }

    /**
     * 提交按钮
     * @return Zend_Form_Element_Image
     */
    public function getButton() {
        if (!$this->_button) {
            $this->_button = new Zend_Form_Element_Submit('提交');
        }
        return $this->_button;
    }

}
