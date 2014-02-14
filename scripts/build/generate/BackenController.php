<?php

/**
 * generate controller class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BackenController extends AbstractCommon {

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
        $this->setClass(new Zend_CodeGenerator_Php_Class);
        $this->_businessModelPrefix = "Application_Model_Business";
        $this->_businessMapperPrefix = "Application_Model_Business_Mapper";
        $className = "Admin";
        $classFile = APPLICATION_PATH . "/modules/admin/controllers";
        if ($this->_dbName) {
            $this->_businessModelPrefix .= "_{$this->_dbClassName}";
            $this->_businessMapperPrefix .= "_{$this->_dbClassName}";
            $this->_controllerName = strtolower($this->_dbClassName . $this->_dbTableClassName);
        } else {
            $this->_controllerName = strtolower($this->_dbTableClassName);
        }

        $className .= "_" . ucfirst($this->_controllerName) . "Controller";
        $classFile .= "/" . ucfirst($this->_controllerName) . "Controller.php";
        $this->getClass()->setName($className);
        $this->setFilename($classFile);
        $this->getClass()
                ->setExtendedClass('Zend_Controller_Action')
                ->setDocblock($this->getClassDocblock())
                ->setProperties($this->getClassProperties())
                ->setMethods($this->getClassMethods());
    }

    /**
     * class docblock
     * @return array
     */
    public function getClassDocblock() {
        return array(
            'shortDescription' => "the controller for {$this->_dbTableName}",
            'longDescription' => $this->_dbTableComment,
            "tags" => array(
                array("name" => "category", "description" => "YouJia"),
                array("name" => "package", "description" => 'Admin')
            )
        );
    }

    /**
     * class properties
     * @return array
     */
    public function getClassProperties() {
        return array(
            array(
                "name" => "_form",
                "visibility" => "protected",
                'docblock' => array(
                    "shortDescription" => "form for {$this->_dbTableName}",
                    "tag" => array("name" => "var", "description" => $this->_form->getClass()->getName())
                )
            )
        );
    }

    /**
     * class methods
     * @return array 
     */
    public function getClassMethods() {
        $eol = PHP_EOL;
        $methods = array();
        $additem = array();
        $edititem = array();
        $forms = array();
        $primaries = array();
        $never_insert = array();
        $never_update = array();
        array_push($methods, array(
            "name" => "init",
            "visibility" => "public",
            "body" => "/* Initialize action controller here */",
            "docblock" => array(
                "shortDescription" => "Initialization."
            )
        ));

        $view = array();
        $params = array();
        $where = array();
        $order = array();
        $primary_names = array();
        foreach ($this->_dbTableColumns as $column) {
            $col_name = $column[YouJia_Db_Table::EXT_COLUMN_NAME];
            $col_primary = $column[YouJia_Db_Table::EXT_COLUMN_PRIMARY];
            $col_php = $column[YouJia_Db_Table::EXT_COLUMN_PHP];
            $col_length = $column[YouJia_Db_Table::EXT_COLUMN_LENGTH];
            $col_scale = $column[YouJia_Db_Table::EXT_COLUMN_SCALE];
            $col_precision = $column[YouJia_Db_Table::EXT_COLUMN_PRECISION];
            $col_insert = $column[YouJia_Db_Table::EXT_COLUMN_INSERT];
            $col_update = $column[YouJia_Db_Table::EXT_COLUMN_UPDATE];
            $val_length = max($col_length, $col_scale, $col_precision);
            $col_name_val = ucfirst($col_name);
            if (in_array($col_php, array('int', 'string', 'boolean', 'bool', 'real', 'float')) && $val_length <= 255) {

                $params[] = "\$s_$col_name = \$this->_getParam('s_$col_name');";
                $view[] = "\$this->view->s_$col_name = \$s_$col_name;";

                if (in_array($col_php, array('int'))) {
                    $where[] = "if(\$s_$col_name) \$select->where('$col_name = ?', \$s_$col_name);";
                } else {
                    $where[] = "if(\$s_$col_name) \$select->where('$col_name LIKE ?', \"%\$s_$col_name%\");";
                }
            }


            if ($col_primary) {
                $order[] = "\$select->order('$col_name ' . Zend_Db_Select::SQL_DESC);";
                $primary_names[] = "\$p_$col_name";
                $primaries[] = "\$p_$col_name = \$this->_getParam('p_$col_name');";
            }

            $form_method = $this->_form->getMappingByColName($col_name);
            $form_method = $form_method['method'];

            if ($col_insert) {
                $model_setter = "set$col_name_val";
                $additem[] = "\$model->{$model_setter}(\$form->{$form_method}()->getValue());";
            }

            if ($col_update) {
                $edititem[] = "\$model->{$model_setter}(\$form->{$form_method}()->getValue());";
            }

            $forms[] = "\$form->$form_method()->setValue(\$model->get$col_name_val());";

            if (!$col_insert) {
                $never_insert[] = "\$form->$form_method()->setAttrib('readonly', 'readonly');";
            }
            if (!$col_update) {
                $never_update[] = "\$form->$form_method()->setAttrib('readonly', 'readonly');";
            }
        }

        $view[] = "\$this->view->paginator = \$paginator;";

        array_push($methods, array(
            "name" => "indexAction",
            "visibility" => "public",
            "body" => implode($eol, $params) . "$eol$eol\$dbTable = new {$this->_table->getClass()->getName()};$eol\$select = \$dbTable->select();$eol$eol" . implode($eol, $where) . "$eol$eol" . implode($eol, $order) . "$eol$eol\$paginator = YouJia_Paginator::factory(\$select);$eol\$paginator->setCurrentPageNumber(\$this->_getParam('page', 1));$eol\$paginator->setDefaultItemCountPerPage(20);$eol$eol" . implode($eol, $view),
            "docblock" => array(
                "shortDescription" => "data search and list."
            )
        ));

        array_push($methods, array(
            "name" => "addAction",
            "visibility" => "public",
            "body" => "\$form = \$this->_getForm();$eol" . implode($eol, $never_insert) . "$eol{$eol}if (\$this->_request->isPost()) { $eol    if (\$form->isValid(\$this->_request->getPost())) { $eol        \$mapper = new {$this->_mapper->getClass()->getName()};$eol        \$model = new {$this->_model->getClass()->getName()};$eol$eol        " . implode("$eol        ", $additem) . "$eol$eol        \$mapper->insert(\$model);$eol        return \$this->_redirect(\$this->view->url(array('action' => 'index', 'controller' => '{$this->_controllerName}', 'module' => 'admin'), null, true));$eol    }$eol}$eol\$this->view->form = \$form;$eol\$this->renderScript(\$this->_getParam('controller') . '/edit.phtml');",
            "docblock" => array(
                "shortDescription" => "add data to {$this->_dbTableName}."
            )
        ));

        array_push($methods, array(
            'name' => 'editAction',
            "visibility" => "public",
            "body" => implode($eol, $primaries) . "$eol\$mapper = new {$this->_mapper->getClass()->getName()};$eol\$model = \$mapper->find(" . implode(", ", $primary_names) . ");$eol$eol\$form = \$this->_getForm();$eol" . implode($eol, $never_update) . "$eol$eol" . implode($eol, $forms) . "$eol{$eol}if (\$this->_request->isPost()) { $eol    if (\$form->isValid(\$this->_request->getPost())) { $eol$eol        " . implode("$eol        ", $edititem) . "$eol$eol        \$mapper->update(\$model);$eol        return \$this->_redirect(\$this->view->url());$eol    }$eol}$eol\$this->view->form = \$form;",
            "docblock" => array("shortDescription" => "edit data to {$this->_dbTableName}.")
        ));

        array_push($methods, array(
            "name" => "delAction",
            "visibility" => "public",
            "body" => "\$this->_helper->viewRenderer->setNoRender();$eol\$this->view->layout()->disableLayout();$eol" . implode($eol, $primaries) . "{$eol}\$mapper = new {$this->_mapper->getClass()->getName()};$eol\$model = \$mapper->find(" . implode(", ", $primary_names) . ");$eol\$mapper->remove(\$model);{$eol}return \$this->_redirect(\$this->view->url(array('action' => 'index')));",
            "docblock" => array(
                "shortDescription" => "delete a item."
            )
        ));

        array_push($methods, array(
            "name" => "_getForm",
            "visibility" => "public",
            "body" => "if (!\$this->_form) { $eol    \$form = new {$this->_form->getClass()->getName()};$eol    \$form->setAction(\$this->view->url());$eol    \$this->_form = \$form;$eol}{$eol}return \$this->_form;",
            "docblock" => array(
                "shortDescription" => "form for $this->_dbTableName.",
                'tag' => array('name' => 'return', 'description' => $this->_form->getClass()->getName())
            )
        ));

        return $methods;
    }

}
