<?php

/**
 * gernerate common tookit
 * @author shizhuolin
 */
abstract class AbstractCommon extends Zend_CodeGenerator_Php_File {

    /**
     * db name
     * @var string 
     */
    protected $_dbName = null;

    /**
     * db class name
     * @var string 
     */
    protected $_dbClassName = null;

    /**
     * dbtable object
     * @var YouJia_Db_Table
     */
    protected $_dbTable = null;

    /**
     * table name
     * @var string 
     */
    protected $_dbTableName = null;

    /**
     * table name for class
     * @var string 
     */
    protected $_dbTableClassName = null;

    /**
     * table comment
     * @var string 
     */
    protected $_dbTableComment = null;

    /**
     * columns
     * @var array 
     */
    protected $_dbTableColumns = null;

    /**
     * __construct()
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if (isset($options['dbName'])) {
            $this->_dbName = $options['dbName'];
            $this->_dbClassName = $this->underscoreToCamelCase($this->_dbName);
        }
        if (isset($options['dbTable'])) {
            $this->_dbTable = $options['dbTable'];
            $this->_dbTableName = $this->_dbTable->info(YouJia_Db_Table::NAME);
            $this->_dbTableClassName = $this->underscoreToCamelCase($this->_dbTableName);
            $this->_dbTableColumns = $this->_dbTable->info(YouJia_Db_Table::METADATA);
            foreach ($this->_dbTableColumns as $column) {
                $this->_dbTableComment = $column[YouJia_Db_Table::EXT_TABLE_COMMENT];
                break;
            }
        }
    }

    /**
     * conversion table name to class name
     * @param string $name 
     * @return string
     */
    public function underscoreToCamelCase($name) {
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_Word_UnderscoreToCamelCase());
        return $filter->filter($name);
    }

}
