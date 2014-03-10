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
        header('Cache-control: private, must-revalidate');
        $this->view->headTitle('管理员登陆');
        $message = '';
        $form = new Admin_Form_Login(array('action' => $this->view->url()));
        if ($this->getRequest()->isPost()) {
            $captcha = new Coola_Captcha();
            if ($captcha->isValid(trim($_POST['captcha']))) {
                echo '验证通过';
            } else {
                echo $_SESSION['coola_captcha'];
            }
        }
        $this->view->message = $message;
        $this->view->form = $form;
    }

}
