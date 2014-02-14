<?php

/**
 * generate controller class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BackenViewEdit extends AbstractCommon {

    /**
     *  Controller file
     * @var BackenController 
     */
    protected $_controller = null;

    /**
     * controller name
     * @var string 
     */
    protected $_controllerName = null;

    /**
     * business model class name prefix
     * @var string 
     */
    protected $_businessModelPrefix = null;

    /**
     * business mapper class name prefix
     * @var string
     */
    protected $_businessMapperPrefix = null;

    /**
     *  model file
     * @var Model 
     */
    protected $_model;

    /**
     * table file
     * @var Table 
     */
    protected $_table;

    /**
     * mapper file
     * @var Mapper
     */
    protected $_mapper;

    /**
     * BackenForm
     * @var BackenForm 
     */
    protected $_form;

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->_model = $options['model'];
        $this->_table = $options['table'];
        $this->_mapper = $options['mapper'];
        $this->_form = $options['form'];
        $this->_controller = $options['controller'];
        $this->_businessModelPrefix = "Application_Model_Business";
        $this->_businessMapperPrefix = "Application_Model_Business_Mapper";
        $filename = APPLICATION_PATH . "/modules/admin/views/scripts";
        if ($this->_dbName) {
            $this->_businessModelPrefix .= "_{$this->_dbClassName}";
            $this->_businessMapperPrefix .= "_{$this->_dbClassName}";
            $this->_controllerName = strtolower($this->_dbClassName . $this->_dbTableClassName);
        } else {
            $this->_controllerName = strtolower($this->_dbTableClassName);
        }
        $filename .= "/{$this->_controllerName}/edit.phtml";
        $this->setFilename($filename);
        $this->setBody('<?php echo $this->form;');
    }

}