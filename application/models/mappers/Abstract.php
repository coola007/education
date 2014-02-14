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
 * mapper of Abstract
 *
 * @author coola
 */
abstract class Application_Model_Mapper_Abstract {

    /**
     * the entity of SQL table interface Class.
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable = null;

    /**
     * Class name for SQL table interface
     *
     * @var string
     */
    protected $_dbTable_class = null;

    /**
     * Class name for SQL table data model
     *
     * @var string
     */
    protected $_dbModel_class = null;

    /**
     * Set Class name for data model interface
     *
     * @param string $className class name
     * @return Application_Model_Mapper_Abstract
     */
    public function setDbModelClass($className) {
        $this->_dbModel_class = $className;
        return $this;
    }

    /**
     * Get Class name for data model interface
     *
     * @return string
     */
    public function getDbModelClass() {
        return $this->_dbModel_class;
    }

    /**
     * Set Class for SQL table interface
     *
     * @param string|Zend_Db_Table_Abstract $dbTable
     * @return Application_Model_Mapper_Abstract
     * @throws Exception
     */
    public function setDbTable($dbTable) {
        if (is_string($dbTable))
            $dbTable = new $dbTable();
        if (!($dbTable instanceof Zend_Db_Table_Abstract))
            throw new Exception('invalid Table interface');
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get Class for SQL table interface
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable() {
        if (!$this->_dbTable)
            $this->setDbTable($this->_dbTable_class);
        return $this->_dbTable;
    }

    /**
     * Fetches rows by primary key.  The argument specifies one or more primary
     * key value(s).  To find multiple rows by primary key, the argument must
     * be an array.
     *
     * This method accepts a variable number of arguments.  If the table has a
     * multi-column primary key, the number of arguments must be the same as
     * the number of columns in the primary key.  To find multiple rows in a
     * table with a multi-column primary key, each argument must be an array
     * with the same number of elements.
     *
     * The find() method always returns a Rowset object, even if only one row
     * was found.
     *
     * @param mixed $key The value(s) of the primary keys.
     * @return Application_Model_Abstract Row(s) matching the criteria.
     * @throws Zend_Db_Table_Exception
     */
    public function find() {
        $result = call_user_func_array(array($this->getDbTable(), "find"), func_get_args());
        return count($result) ? new $this->_dbModel_class($result->current()) : null;
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
     * @return array|Application_Model_Abstract The row results per the Zend_Db_Adapter
     * fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $rows = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($rows as $row) {
            array_push($entries, new $this->_dbModel_class($row));
        }
        return $entries;
    }

    /**
     * Updates existing model.
     *
     * @param Application_Model_Abstract $model  Sql table data model
     * @return int          The number of rows updated.
     */
    abstract public function update($model);

    /**
     * remove existing model.
     *
     * @param Application_Model_Abstract $model  Sql table data model
     * @return int          The number of rows deleted.
     */
    abstract public function remove($model);

    /**
     * Inserts a new model.
     *
     * @param Application_Model_Abstract $model  Sql table data model
     * @return mixed         The primary key of the row inserted.
     */
    abstract public function insert($model);
}
