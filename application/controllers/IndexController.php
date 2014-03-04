<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction() {
        $captcha = new Coola_Captcha(100,40);
        $captcha->image();
        if($_SESSION['coola_captcha'] != $captcha->getCode()){
            echo 'error';
        }
        exit;
    }

}
