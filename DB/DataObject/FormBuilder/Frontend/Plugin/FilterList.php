<?php
/**
 * Add a form before the listview, from where to search/filter for
 * specific records.
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
 * Add a form before the listview, from where to search/filter for
 * specific records.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 211 2009-08-14 07:17:39Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_FilterList
    extends DB_DataObject_FormBuilder_Frontend_Plugin
{

    /**
     * Default options
     *
     * @var array
     */
    protected $defaultOptions = array(
        'fields'            => array(),
        'filterName'        => '__FilterListView_submit__',
        'removeFilterName'  => '__RemoveFilter_submit',
        'filterLabel'       => 'Filtrer!',
        'removeFilterLabel' => 'Fjern filter',
        'statusOn'          => 'On',
        'statusOnLabel'     => 'Filter er: Slået til',
        'statusOff'         => 'Off',
        'statusOffLabel'    => 'Filter er: Slået fra',
        'columnLabel'       => 'Filtrer!',
        'submitLabel'       => 'Anvend filter',
        'isXhtml'           => true,
        'template'          => <<<HTML
<div id="filterFormDiv" class="filterFormDiv{status}">
    <h5>Søg/filtrer listen</h5>
    <div>{statusText}</div><br/>
    {form}
</div>
HTML
        ,
    );

    /**
     * The form-object used to create the searchform.
     *
     * @var HTML_QuickForm
     */
    protected $form;

    /**
     * Status of the filter - on or off
     * @var int
     */
    protected $status = self::FILTER_STATUS_OFF;

    const FILTER_STATUS_OFF = 0;
    const FILTER_STATUS_ON  = 1;

    /**
     * @var array Fields to display in the form
     */
    protected $fields = array();
    /**
     * Set options for this plugin
     *
     * This will make sure that if some options are not set, the defaults are
     * used.
     *
     * @param array $options Array of options to set.
     *
     * @return DB_DataObject_FormBuilder_Frontend_Plugin_FilterList
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
     * Hook that is run before the datagrid is filled
     * Here we create the form for the filter, and check if the filter should
     * be applied to the list
     *
     * @param DB_DataObject       $do Used for the selection
     * @param Structures_DataGrid $dg Is to be filled, using the DataObject
     *
     * @return string '' (the empty string)
     */
    public function beforeDataGridFill(
        DB_DataObject $do,
        Structures_DataGrid $dg
    ) {
        $this->createForm();

        if ($this->form->validate()) {
            if ($this->form->getSubmitValue(
                $this->options['removeFilterName']
            )) {
                $this->status = self::FILTER_STATUS_OFF;
            } else if ($this->form->getSubmitValue(
                $this->options['filterName']
            )) {
                $this->status = self::FILTER_STATUS_ON;
                $this->applyFilter();
            }

        }

        return '';
    }

    /**
     * Add a form-tag before the datagrid output.
     *
     * @param DB_DataObject       $do DataObject used to build the grid
     * @param Structures_DataGrid $dg The datagrid that will be output
     *
     * @return string String ment to be output before the grid
     *
     * @see DB_DataObject_FormBuilder_Frontend_Plugin::beforeDataGridOutput
     */
    public function beforeDataGridOutput(
        DB_DataObject $do,
        Structures_DataGrid $dg
    ) {
        $renderer = $this->frontend->getQuickFormRenderer();
        if (null !== $renderer) {
            $this->form->accept($renderer);
            $renderMethod = $this->frontend->getQuickFormRendererMethod();
            $html = $renderer->$renderMethod();
        } else {
            $html = $this->form->toHtml();
        }

        $search  = array('{statusText}', '{status}', '{form}',);
        $replace = array(
            ($this->status ?
                $this->options['statusOnLabel'] :
                $this->options['statusOffLabel']),
            ($this->status ?
                $this->options['statusOn'] :
                $this->options['statusOff']),
            $html,
        );

        return str_replace($search, $replace, $this->options['template']);
    }

    /**
     * Apply the filter to the dataobject.
     *
     * @return DB_DataObject_FormBuilder_Frontend_Plugin_FilterList
     */
    protected function applyFilter()
    {
        $do = $this->frontend->getDataObject();
        foreach ($this->getFields() as $field => $settings) {
            $submit = $this->form->getSubmitValue($field);
            if (!empty($submit)) {
                if (false === strpos($submit, '*')) {
                    // No wildcards
                    $do->whereAdd(
                        "$field = '" . $do->escape($submit, true) . "'"
                    );
                    //$do->$field = $submit;
                } else {
                    $submit = str_replace('*', '%', $submit);
                    $rv = $do->whereAdd(
                        "$field LIKE '" . $do->escape($submit) . "'"
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Create the HTML_QuickForm, store it in $this->_form
     *
     * @return DB_DataObject_FormBuilder_Frontend_Plugin_FilterList
     */
    protected function createForm()
    {
        $this->form = new HTML_QuickForm(
            'filterForm', 'get',
            $this->frontend->getScriptUrl(),
            '', null, true
        );

        /*
         * Add all the current request-variables as hidden fields to the grid
         * to preserve the state it is in now.
         * However, since this filter is using get-request, remeber to remove
         * the state of the two submits in this form.
         * If not, you could end up with both remove filter and set filter
         * being on at the same time. (In which case the Remove Filter-action is
         * the one to be invoked.
         */
        $remove = array_keys($this->getFields());
        $remove = array_merge(
            $remove,
            array(
                 $this->options['filterName'],
                 $this->options['removeFilterName'],
            )
        );
        $params = $this->frontend->getQueryParamsArray(
            array(),
            false,
            $remove
        );

        foreach ($params as $name => $value) {
            $this->form->addElement('hidden', $name, $value);
        }

        if ($this->defaultOptions['isXhtml']) {
            $this->form->removeAttribute('name');
        }

        $this->addFilterFields();

        $filter = $this->form->createElement(
            'submit',
            $this->options['filterName'],
            $this->options['filterLabel']
        );
        $removeFilter = $this->form->createElement(
            'submit',
            $this->options['removeFilterName'],
            $this->options['removeFilterLabel']
        );
        $this->form->addGroup(array($removeFilter, $filter));

        return $this;
    }

    /**
     * Get the fields to put in the searchform.
     *
     * These can be set as options in the xml-file configuring FormBuilder,
     * or if not set, all fields used in the listview will be used.
     *
     * @return array Key is the name of the property from the dataobject, value
     *               is an array with the keys
     *                'label' => Label to display for the formfield
     *                'type' => Type of field to generate.
     */
    protected function getFields()
    {
        if (empty($this->fields)) {

            $fields = $this->options['fields'];

            if (count($fields) === 0) {
                $fields = array();
                $table = $this->frontend->getDataObject()->table();
                //var_dump($table);
                foreach ($this->frontend->getListColumns() as $field => $value) {
                    if (isset($table[$field])) {
                        $this->fields[$field] = array(
                            'label' => ucfirst($value['displayName']),
                            'type'  => $value['type'],
                        );
                    }
                }
            } else {
                foreach ($fields as $field => $value) {
                    if (!is_array($value)) {
                        $this->fields[$field] = array(
                            'label' => ucfirst($value),
                            'type'  => 'text',
                        );
                    }
                }
            }
        }

        return $this->fields;
    }

    /**
     * Add the fields to the searchform.
     *
     * @return DB_DataObject_FormBuilder_Frontend_Plugin_FilterList
     */
    protected function addFilterFields()
    {
        $fields = $this->getFields();

        foreach ($fields as $column => $info) {
            $label = $info['label'];

            switch ($info['type']) {
            case 'text':
            case '':
                $this->form->addElement(
                    'text',
                    $column,
                    $label,
                    array('size' => 50)
                );
                // Add a text-fields
                break;
            default:
                // Not defined, add a label
                $this->form->addElement(
                    'static',
                    $column,
                    $label,
                    "&lt;{$info['type']}: Not implemented&gt;"
                );
            }
        }

        return $this;
    }

}
