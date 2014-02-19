<?php

/*
 *  Copyright (c) <2014> <霖默醉语>
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  version 1.0
 */

/**
 * 登录控制器
 *
 * @author coola
 */
class Admin_LoginController extends Zend_Controller_Action {

    public function init() {
        $this->view->layout()->disableLayout();
    }

    public function indexAction() {
        $this->view->headTitle('管理员登陆');
        $message = '';
        $form = new Admin_Form_Login(array('action' => $this->view->url()));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {//表单验证
                try {
                    $this->process($form->getId()->getValue(), $form->getPassword()->getValue(), $form->getSerial1()->getValue(), $form->getSerial2()->getValue(), $form->getSerial3()->getValue(), $form->getSerial4()->getValue(), $form->getSerial5()->getValue());
                    return $this->_redirect($this->view->url(array('controller' => 'index')));
                } catch (Exception $exc) {
                    //$message = "登录失败，错误代码：" . $exc->getCode();
                    $message = "登录失败";
                }
            } else {
                //$validMessage = $form->getMessages();
                $message = "登录失败";
            }
        }
        $this->view->message = $message;
        $this->view->form = $form;
    }

}
