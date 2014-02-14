<?php

/**
 * generate controller class
 *
 * @author shizhuolin
 */
require_once 'AbstractCommon.php';

class BackenViewIndex extends AbstractCommon {

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
        $filename .= "/{$this->_controllerName}/index.phtml";
        $this->setFilename($filename);
        $this->setBody($this->getFileBody());
    }

    public function getFileBody() {
        $eol = PHP_EOL;
        $html = "";
        $search_label_input = array();
        $thead = "";
        $tbody = "<tr>";
        $col_primary_names = array();
        $col_primary_parames = array();
        $colnum = 0;
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
            $col_comment = $column[YouJia_Db_Table::EXT_COLUMN_COMMENT];
            $col_name_val = ucfirst($col_name);
            $col_description = $col_comment ? $col_comment : $col_name;
            if (in_array($col_php, array('int', 'string', 'boolean', 'bool', 'real', 'float')) && $val_length <= 255) {
                $search_label_input[] = "<label for=\"s_$col_name\">$col_description:</label>$eol    <input type=\"text\" name=\"s_$col_name\" id=\"s_$col_name\" value=\"<?php echo \$this->s_$col_name ?>\" maxlength=\"$val_length\" />";

                $tbody .= "$eol            <td><?php echo \$model->$col_name ?></td>";
                $thead .= "$eol            <th>$col_description</th>";
                $colnum++;
            }
            if ($col_primary) {
                $col_primary_names[] = $col_name;
                $col_primary_parames[] = "'p_$col_name' => \$model->$col_name";
            }
        }

        $colnum+=2;
        $thead .= "$eol            <th colspan=\"2\"><a href=\"/admin/{$this->_controllerName}/add\">add</a></th>";

        $tbody .= "$eol            <td><a href=\"<?php echo \$this->url(array(" . implode(", ", $col_primary_parames) . ", 'action' => 'edit')) ?>\">edit</a></td>
            <td><a href=\"<?php echo \$this->url(array(" . implode(", ", $col_primary_parames) . ", 'action' => 'del')) ?>\">del</a></td>";

        $tbody .= "$eol        </tr>";

        $html .= "<form action=\"/admin/{$this->_controllerName}\" method=\"get\">
    " . implode("$eol$eol    ", $search_label_input) . "
    <input type=\"submit\" name=\"\" value=\"search\" />
</form>
<table>
    <thead>
        <tr>$thead
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan=\"$colnum\">
                total:<?php echo \$this->paginator->getTotalItemCount() ?>
                item:<?php echo \$this->paginator->getCurrentItemCount() ?>
                page:<?php echo \$this->paginator->getCurrentPageNumber() ?>/<?php echo \$this->paginator->count() ?>
                <?php if (\$this->paginator->getCurrentPageNumber() > 1): ?>
                    <a href=\"<?php echo \$this->url(array('page' => 1)) ?>\">first</a>
                    <a href=\"<?php echo \$this->url(array('page' => max(\$this->paginator->getCurrentPageNumber() - 1, 1))) ?>\">pre</a>
                <?php endif; ?>
                <?php if (\$this->paginator->getCurrentPageNumber() < \$this->paginator->count()): ?>
                    <a href=\"<?php echo \$this->url(array('page' => min(\$this->paginator->getCurrentPageNumber() + 1, \$this->paginator->count()))) ?>\">next</a>
                    <a href=\"<?php echo \$this->url(array('page' => \$this->paginator->count())) ?>\">end</a>
                <?php endif; ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach (\$this->paginator as \$row): \$model = new {$this->_model->getClass()->getName()}(\$row); ?>
        $tbody
        <?php endforeach; ?>
    </tbody>
</table>";
        return $html;
    }

}