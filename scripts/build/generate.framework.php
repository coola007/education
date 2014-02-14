#!/usr/bin/php
<?php
/**
 * fix mysql enum type
 * Script for creating database model and business and backen and form model
 * @todo php create.model.business.backen.once
 * @author shizhuolin@hotmail.com
 * @version 1.0.5
 */
/*
 * working at application/model
 */
// Define path to application directory
defined("APPLICATION_PATH") || define("APPLICATION_PATH", realpath(dirname(__FILE__) . "/../../application"));

// Define application environment
defined("APPLICATION_ENV") || define("APPLICATION_ENV", (getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : "development"));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . "/../library"), get_include_path(),)));

require_once "Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance();

// Initialize Zend_Application
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . "/configs/application.ini");

try {
    $opts = new Zend_Console_Getopt(
                    array(
                        'model|m' => 'enable generate and overwrite data model class',
                        'mapper|a' => 'enable generate and overwrite data mapper class',
                        'dbtable|t' => 'enable generate and overwrite data dbtable class',
                        'business-model|d' => "enable generate and overwrite business model class",
                        'business-mapper|p' => "enable generate and overwrite business mapper class",
                        'form|f' => "enable generate and overwrite backen form class",
                        'controller|c' => "enable generate and overwrite backen controller class",
                        'index|i' => "enable generate and overwrite backen view index file",
                        'edit|e' => "enable generate and overwrite backen view edit file",
                        'all|l' => "all/set generate all file or set file"
                    )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}

$overwrite_model = $opts->getOption('model');
$overwrite_mapper = $opts->getOption('mapper');
$overwrite_dbtable = $opts->getOption('dbtable');
$overwrite_business_model = $opts->getOption('business-model');
$overwrite_business_mapper = $opts->getOption('business-mapper');
$overwrite_backen_form = $opts->getOption('form');
$overwrite_backen_controller = $opts->getOption('controller');
$overwrite_backen_view_index = $opts->getOption('index');
$overwrite_backen_view_edit = $opts->getOption('edit');
$overwrite_backen_view_all = $opts->getOption('all');


$bootstrap = $application->getBootstrap();
$options = $bootstrap->getOption("resources");
$bootstrap->bootstrap("db");
$dbAdapter = $bootstrap->getResource("db");
$dbAdapters = array();

if (isset($options["multidb"])) {
    $bootstrap->bootstrap("multidb");
    $resource = $bootstrap->getPluginResource("multidb");
    foreach ($options["multidb"] as $name => $value) {
        if ($name !== "defaultMetadataCache")
            $dbAdapters[$name] = $resource->getDb($name);
    }
}

//process default db
$tables = $dbAdapter->listTables();
foreach ($tables as $table) {
    process('', new YouJia_Db_Table(array(YouJia_Db_Table::ADAPTER => $dbAdapter, YouJia_Db_Table::NAME => $table)));
}

//process multidb
foreach ($dbAdapters as $name => $adapter) {
    $tables = $adapter->listTables();
    foreach ($tables as $table) {
        process($name, new YouJia_Db_Table(array(YouJia_Db_Table::ADAPTER => $adapter, YouJia_Db_Table::NAME => $table)));
    }
}



require_once 'generate/AbstractMapper.php';
require_once 'generate/AbstractModel.php';
$class = new AbstractMapper;
echo "generate: {$class->getClass()->getName()} " . generate($class, 1) . PHP_EOL;
$class = new AbstractModel;
echo "generate: {$class->getClass()->getName()} " . generate($class, 1) . PHP_EOL;

/**
 * code generate
 * @param string $dbName
 * @param YouJia_Db_Table $table 
 * @return void
 */
function process($dbName, YouJia_Db_Table $dbTable) {
    try {
        $dbTable->info(YouJia_Db_Table::PRIMARY);
    } catch (Exception $e) {
        return;
    }
    global $overwrite_model, $overwrite_mapper, $overwrite_dbtable, $overwrite_business_model, $overwrite_business_mapper, $overwrite_backen_form, $overwrite_backen_controller, $overwrite_backen_view_index, $overwrite_backen_view_edit, $overwrite_backen_view_all;
    require_once 'generate/Model.php';
    require_once 'generate/Table.php';
    require_once 'generate/Mapper.php';
    require_once 'generate/BusinessModel.php';
    require_once 'generate/BusinessMapper.php';
    require_once 'generate/BackenForm.php';
    require_once 'generate/BackenController.php';
    require_once 'generate/BackenViewEdit.php';
    require_once 'generate/BackenViewIndex.php';
    $model = new Model(array('dbName' => $dbName, 'dbTable' => $dbTable));
    $table = new Table(array('dbName' => $dbName, 'dbTable' => $dbTable));
    $mapper = new Mapper(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $model, 'table' => $table));
    $businessModel = new BusinessModel(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $model, 'table' => $table));
    $businessMapper = new BusinessMapper(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $businessModel, 'table' => $table, 'mapper' => $mapper));
    $backenForm = new BackenForm(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $businessModel, 'table' => $table, 'mapper' => $businessMapper));
    $backenController = new BackenController(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $businessModel, 'table' => $table, 'mapper' => $businessMapper, 'form' => $backenForm));
    $backenViewEdit = new BackenViewEdit(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $businessModel, 'table' => $table, 'mapper' => $businessMapper, 'form' => $backenForm, 'controller' => $backenController));
    $backenViewIndex = new BackenViewIndex(array('dbName' => $dbName, 'dbTable' => $dbTable, 'model' => $businessModel, 'table' => $table, 'mapper' => $businessMapper, 'form' => $backenForm, 'controller' => $backenController));
    if ($overwrite_backen_view_all || $overwrite_model)
        echo "generate: {$model->getClass()->getName()} " . generate($model, $overwrite_model) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_dbtable)
        echo "generate: {$table->getClass()->getName()} " . generate($table, $overwrite_dbtable) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_mapper)
        echo "generate: {$mapper->getClass()->getName()} " . generate($mapper, $overwrite_mapper) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_business_model)
        echo "generate: {$businessModel->getClass()->getName()} " . generate($businessModel, $overwrite_business_model) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_business_mapper)
        echo "generate: {$businessMapper->getClass()->getName()} " . generate($businessMapper, $overwrite_business_mapper) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_backen_form)
        echo "generate: {$backenForm->getClass()->getName()} " . generate($backenForm, $overwrite_backen_form) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_backen_controller)
        echo "generate: {$backenController->getClass()->getName()} " . generate($backenController, $overwrite_backen_controller) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_backen_view_edit)
        echo "generate: {$backenViewEdit->getFilename()} " . generate($backenViewEdit, $overwrite_backen_view_edit) . PHP_EOL;
    if ($overwrite_backen_view_all || $overwrite_backen_view_index)
        echo "generate: {$backenViewIndex->getFilename()} " . generate($backenViewIndex, $overwrite_backen_view_index) . PHP_EOL;
}

/**
 * write file and echo information
 * @param Zend_CodeGenerator_Php_File $file
 * @param boolean $overwrite 
 * @return boolean
 */
function generate(Zend_CodeGenerator_Php_File $file, $overwrite = false) {
    $filename = $file->getFilename();
    $filedir = dirname($filename);
    if (!$overwrite && file_exists($filename)) {
        return false;
    }
    if (!file_exists($filedir)) {
        mkdir($filedir, 0777, 1);
    }
    $file->write();
    return true;
}
