<?php
/**
* Set sensible default html-attributes for misc. field-types:
* input:    lenght, maxlenght
* textarea: cols, rows
 *
 * PHP version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: HtmlDefaultAttributes.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
* Set sensible default html-attributes for misc. field-types:
* input:    lenght, maxlenght
* textarea: cols, rows
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: HtmlDefaultAttributes.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_HtmlDefaultAttributes
   extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * Default length for input-fields
     *
     * @var int
     */
    public $inputLength = 80;

    /**
     * Default number of rows in a textarea
     *
     * @var int
     */
    public $textareaRows = 12;

    /**
     * Default number of columns in a textarea
     *
     * @var int
     */
    public $textareaCols = 70;

    /**
     * Prefix for the contents of the id-attribute
     *
     * @var string
     */
    public $idPrefix = '';

    /**
     * Postfix for the contents of the id-attribute
     *
     * @var string
     */
    public $idPostfix = '';

    /**
     * Get info from the DB-table and add these to the Form
     *
     * Uses the Util_DB-class to get info from the database-table, like size of
     * fields, type and the like. Then sets attributes on the form, according to
     * these.
     *
     * @param DB_DataObject $do The DataObject in use.
     *
     * @return void
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#preGenerateForm()
     */
    public function preGenerateForm(DB_DataObject $do)
    {
        include_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery.php';
        $d = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::factory($do);
        $columns = $d->getColumnInfo();

        foreach ($columns as $key => $val) {

            switch($val['itype']) {
            case DB_DataObject_FormBuilder_Frontend_Util_DbQuery::CHAR:
                // Set Maxlength og char/varchar-fields
                if (!isset($do->fb_fieldAttributes[$val['column']]['maxlength'])) {
                    $do->fb_fieldAttributes[$val['column']]['maxlength']
                        = $val['length'];
                }
                // Set length of char/varchar-fields
                if (!isset($do->fb_fieldAttributes[$val['column']]['length'])) {
                    $do->fb_fieldAttributes[$val['column']]['size']
                        = min($val['length'], $this->inputLength);
                }
                break;
            case DB_DataObject_FormBuilder_Frontend_Util_DbQuery::TEXT:
                // Set rows of text-fields
                if (!isset($do->fb_fieldAttributes[$val['column']]['rows'])) {
                    $do->fb_fieldAttributes[$val['column']]['rows']
                        = $this->textareaRows;
                }
                // Set columns of text-fields
                if (!isset($do->fb_fieldAttributes[$val['column']]['cols'])) {
                    $do->fb_fieldAttributes[$val['column']]['cols']
                        = $this->textareaCols;
                }
                break;
            }

            // Set an id-attribute
            $columnId = $this->idPrefix . $val['column'] . $this->idPostfix;
            $do->fb_fieldAttributes[$val['column']]['id'] = $columnId;

        }

    }

    /**
     * Set options for this plugin
     *
     * @param array $options Options recognized:
     *						 'inputLength'   => Input-fields will be shown in this size.
     * 						 'textareaRows'  => Number of rows in a textarea
     *						 'textareaCols'  => Number of columns in textareas.
     *						 'idPrefix'		 => Prefix for the id-attr.
     *						 'idPostfix'	 => Postfix for the id-attr.
     *
     * @return self
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#setOptions($options)
     */
    public function setOptions(array $options)
    {
        if (isset($options['inputLength'])) {
            $this->inputLength = $options['inputLength'];
        }
        if (isset($options['textareaRows'])) {
            $this->textareaRows = $options['textareaRows'];
        }
        if (isset($options['textareaCols'])) {
            $this->textareaCols = $options['textareaCols'];
        }

        if (isset($options['idPrefix'])) {
            $this->idPrefix = $options['idPrefix'];
        }
        if (isset($options['idPostfix'])) {
            $this->idPostfix = $options['idPostfix'];
        }

        return $this;
    }
}
