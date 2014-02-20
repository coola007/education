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
 * sql mapper of Administrator_logs
 *
 * @author coola
 */
class Application_Model_Mapper_Administrator_Logs extends Application_Model_Mapper_Abstract {

    /**
     * Class name for SQL table interface
     *
     * @var string
     */
    protected $_dbTable_class = 'Application_Model_DbTable_Administrator_Logs';

    /**
     * Class name for SQL table data model
     *
     * @var string
     */
    protected $_dbModel_class = 'Application_Model_AdministratorLogs';

    /**
     * Fetches rows by primary key
     *
     * @param int $id
     * @return Application_Model_AdministratorLogs
     */
    public function find() {
        return parent::find(func_get_arg(0));
    }

    /**
     * Inserts a new model.
     *
     * @param Application_Model_AdministratorLogs $model  Sql table data model
     * @return int The primary key of the row inserted.
     */
    public function insert($model) {
        $data = $model->toArray();
        unset($data['id']);
        return $this->getDbTable()->insert($data);
    }

    /**
     * Updates existing model.
     *
     * @param Application_Model_AdministratorLogs $model  Sql table data model
     * @return int The number of rows updated.
     */
    public function remove($model) {
        $data = $model->toArray();
        unset($data['id']);
        $where = array("id = ?" => $model->getId());
        return $this->getDbTable()->update($data, $where);
    }

    /**
     * remove existing model.
     *
     * @param Application_Model_AdministratorLogs $model  Sql table data model
     * @return int The number of rows deleted.
     */
    public function update($model) {
        $where = array("id = ?" => $model->getId());
        return $this->getDbTable()->delete($where);
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or
     * Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return array|Application_Model_AdministratorLogs
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
