<?php
/**
 * Add a column of checkboxes to the table-list-display, and a submit-button
 * that, if clicked, will do a delete on all the checked rows.
 *
 * PHP version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 211 2009-08-14 07:17:39Z mlj $
 * @link     http://www.gartneriet.dk/
 */
require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * Add a column of checkboxes to the table-list-display, and a submit-button
 * that, if clicked, will do a delete on all the checked rows.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 211 2009-08-14 07:17:39Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_DeleteFromListview
   extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * Default options
     *
     * @var array
     */
    protected $defaultOptions = array(
        'deleteName' => '__DeleteFromListView_submit__',
        'columnLabel'     => 'Delete?',
        'submitLabel'   => 'Delete checked records',
    );

    /**
     * Set options for this plugin
     *
     * This will make sure that if some options are not set, the defaults are
     * used.
     *
     * @param array $options Array of options to set.
     *
     * @return self
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin::setOptions()
     */
    public function setOptions(array $options)
    {
        foreach ($this->defaultOptions as $name => $value) {
            if (!isset($options[$name])) {
                $options[$name] = $value;
            }
        }

        $this->options = $options;
        return $this;
    }

    /**
     * Add a column to the datagrid.
     *
     * This is called before the datagrid is filled.
     * The column is using the function columnContent() in this class to
     * write the data in the column.
     *
     * @param DB_DataObject       $do The dataobject from which the grid is made
     * @param Structures_DataGrid $dg The datagrid to fill
     *
     * @return void
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin::beforeDataGridFill()
     */
    public function beforeDataGridFill(
        DB_DataObject $do,
        Structures_DataGrid $dg
    ) {
        $attr = array(
            'type'        => 'callback',
            'displayName' => $this->options['columnLabel'],
            '__options'   => array(
                'callbackName' => array($this, 'columnContent'),
                'params'       => array(),
            ),
        );

        // Create the column and add it to the grid
        $col = $this->frontend->getColumn('__del__', $attr);
        $dg->addColumn($col->getColumn(), 'first');

    }

    /**
     * Format data in the column.
     *
     * Adds a checkbox.
     *
     * @param array $data The current record as sent by Structures_DataGrid
     *
     * @return string
     */
    public function columnContent($data)
    {
        // TODO: Do not use method marked as private to get primary key
        $pk = DB_DataObject_FormBuilder::_getPrimaryKey($data['record']);
        return sprintf(
            '<input type="checkbox" name="record[%d]" />',
            $data['record']->$pk
        );
    }

    /**
     * Add a form-tag before the datagrid output.
     *
     * @param DB_DataObject       $do DataObject used to build the grid
     * @param Structures_DataGrid $dg The datagrid that will be output
     *
     * @return string String ment to be output before the grid
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin
     *      ::beforeDataGridOutput()
     */
    public function beforeDataGridOutput(
        DB_DataObject $do,
        Structures_DataGrid $dg
    ) {
        $this->frontend->addDataGridFormField('delete', 'delete', 'hidden');
        $this->frontend->addDataGridFormSubmit(
            $this->options['deleteName'],
            $this->options['submitLabel']
        );

        return '';
    }

    /**
     * Add a submit-button that calls the delete on checked records, and close
     * the form.
     *
     * @param DB_DataObject       $do DataObject used to build the grid
     * @param Structures_DataGrid $dg The datagrid that will be output
     *
     * @return string String ment to be output after the grid
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin
     *      ::afterDataGridOutput()
     */
    public function afterDataGridOutput(
        DB_DataObject $do,
        Structures_DataGrid $dg
    ) {
        return;

        $rv = '<input type="submit" name="' . $this->options['deleteName'];
        $rv .= '" value="' . $this->options['submitLabel'] . '" />';
        $rv .= '<input type="hidden" name="delete" value="delete" />';
        $rv .= '</form>';
        return $rv;
    }

    /**
     * This is called before the form is generated.
     *
     * If "our" delete-button has been clicked, then 'record' is an array of
     * id-numbers of the rows, with the value 'on'. The delete-function needs
     * the keys as the values, so they are  extracted and injected into the
     * request-variables.
     *
     * Not a nice solution, but it works....
     *
     * @param DB_DataObject $do DataObject to delete from.
     *
     * @return void
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin::preGenerateForm()
     */
    public function preGenerateForm(DB_DataObject $do)
    {
        /*
         * If "our" deletebutton is set, records is an array of:
         *     recordId => 'on'.
         * Its the keys that are needed as values in record, so get the keys
         * and inject them into request-variables.
         */
        if (isset($_REQUEST[$this->options['deleteName']])
            && is_array($_REQUEST['record'])
        ) {
            $record = $_REQUEST['record'];
            $_REQUEST['record'] = $_POST['record'] = array_keys($record);
        }

    }
}
