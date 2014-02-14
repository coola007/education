<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * constat holding reserved name for data cache
     */
    const CACHE_DATABASE = 'database';

    /**
     * Layout object
     * defined by Zend_Application_Resource_Resource
     * @return Zend_Layout
     */
    public function _initLayout() {
        $this->bootstrap('FrontController');
        $layout = Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . '/layouts/scripts/'));
        if (Zend_Controller_Action_HelperBroker::hasHelper('layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('layout');
        }
//        Zend_Controller_Action_HelperBroker::getStack()->offsetGet(-90, new )
        return $layout;
    }

    public function _initRequest() {
        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $baseUrl = $front->getBaseUrl();
        $front->SetBaseurl($baseUrl); //->setRequest('')
    }

}
