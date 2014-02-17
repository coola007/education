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
 * dbTable of Abstract
 *
 * @author coola
 */
class Application_Model_DbTable_Abstract extends Zend_Db_Table_Abstract {

    /**
     * the prefix of db table
     * @var string 
     */
    static $_prefix;

    public function __construct($config = array()) {
        $this->_name = $this->getPrefix() . $this->_name;
        parent::__construct($config);
    }

    /**
     * the prefix of table
     * @return string
     */
    public function getPrefix() {
        if (!self::$_prefix) {
            self::$_prefix = Bootstrap::getGlobalSettings('resources.db.params.prefix');
        }

        return self::$_prefix;
    }

}
