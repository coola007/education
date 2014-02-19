<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function _initAclHelper() {
        Zend_Controller_Action_HelperBroker::addHelper(new Admin_Plugin_Helper_Acl());
    }

}
