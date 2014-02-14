<?php

/*
 *  Copyright (c) <2014> <霖默醉语>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  @version 1.0
 */

/**
 * 权限控制
 * @category admin
 * @package  Admin_Plugin_helper
 * @author coola
 */
class Admin_Plugin_Helper_Acl extends Zend_Controller_Action_Helper_Abstract {

    public function preDispatch() {

        $moduleName = $this->getRequest()->getModuleName();
        if ($moduleName == 'admin') {
            $controllerName = $this->getRequest()->getControllerName();
            if ($controllerName != 'login') {
                $storage = Zend_Auth::getInstance()->getStorage();
                $loginInfo = $storage->read();
                if (!$loginInfo) {
                    $this->_actionController->getHelper('redirector')->gotoUrl($this->_actionController->view->url(array('module'=>'admin','controller'=>'login'),null,TRUE));
                }
                session_write_close();
            }
        }
        parent::preDispatch();
    }

}
