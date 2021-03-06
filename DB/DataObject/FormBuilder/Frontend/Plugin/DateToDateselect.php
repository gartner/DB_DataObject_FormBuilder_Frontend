<?php
/**
 * Make all date-fields in the form into datejquery-fields.
 *
 * This field-type adds a javascript-dateselector to the left of the field.
 * This requires that you have jQuery in your pages!
 *
 * PHP version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * Make all date-fields in the form into datejquery-fields.
 *
 * This field-type adds a javascript-dateselector to the left of the field.
 * This requires that you have jQuery in your pages!
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 *
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_DateToDateselect
    extends DB_DataObject_FormBuilder_Frontend_Plugin
{

    /**
     * Map all date-fields into datejquery-fields.
     *
     * @param DB_DataObject $do The DataObject the form is based on.
     *
     * @return void
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#preGenerateForm()
     */
    public function preGenerateForm(DB_DataObject $do)
    {
        include_once 'MLJ/HTML/QuickForm/datejquery.php';

        if (class_exists('MLJ_HTML_QuickForm_datejquery')) {
            HTML_QuickForm::registerElementType(
                'datejquery',
                'MLJ/HTML/QuickForm/datejquery.php',
                'MLJ_HTML_QuickForm_datejquery'
            );
            $do->fb_elementTypeMap['date'] = 'datejquery';
        } else {
            // TODO: Throw exception
        }
    }

    /**
     * Add javascript to the pageheader.
     *
     * @param DB_DataObject             $do The DataObject we are working on.
     * @param DB_DataObject_FormBuilder $fb The formBuilder making the form
     *
     * @return string This should be inserted into the header of the html-page.
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#pageHeader()
     */
    public function pageHeader(
        DB_DataObject $do,
        $fb = null
    ) {
        if ($this->frontend->getMode() == DB_DataObject_FormBuilder_Frontend::EDIT) {
            $this->frontend->addJavascript('jquery.js');
            $this->frontend->addJavascript('date.js');
            $this->frontend->addJavascript('jquery.datePicker.js');
            $this->frontend->addCss('datePicker.css');
            $rv = '';

            return $rv;
        }
    }

}
