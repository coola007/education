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
        echo 'login';exit;
    }

}
