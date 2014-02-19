<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Admin of Index
 *
 * @author coola
 */
class Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        echo 'admin';
    }

}
