<?php
/**
 * Base class for plugins
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Plugin.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

/**
 * This is the base of plugins used in this package.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Plugin.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
abstract class DB_DataObject_FormBuilder_Frontend_Plugin
{

    protected $options = array();

    /**
     * The Formbuilder used in the process.
     *
     * @var DB_DataObject_FormBuilder_Frontend
     */
    protected $frontend;

    /**
     * Constructor
     *
     * @param DB_DataObject_FormBuilder_Frontend $frontend Make the
     *      frontend-instance accessible to all plugins.
     */
    public function __construct(DB_DataObject_FormBuilder_Frontend $frontend)
    {
        $this->frontend = $frontend;

    }

    /**
     * Set options for this instance.
     *
     * @param array $options Array of options
     *
     * @return DB_DataObject_FormBuilder_Frontend_Plugin
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
    * Will be called before the form is generated
    *
    * @param DB_DataObject $do The DataObject we are working on.
    *
    * @return bool
    */
    public function preGenerateForm(DB_DataObject $do)
    {
        return true;
    }

    /**
    * Will be called after form is generated
    *
    * @param DB_DataObject             $do   The DB_DataObject being worked on
    * @param DB_DataObject_FormBuilder $fb   The formbuilder instance being worked on
    * @param HTML_QuickForm            $form The QuickForm to be worked on
    *
    * @return bool
    */
    public function postGenerateForm(
        DB_DataObject $do,
        DB_DataObject_FormBuilder $fb,
        HTML_QuickForm $form
    ) {
        return true;
    }

    /**
    * Called when outputting the (html-) page header
    *
    * @param DB_DataObject             $do DB_DataObject that is being worked on
    * @param DB_DataObject_FormBuilder $fb The formbuilder-object, if any, else null
    *
    * @return string Stuff to add inside the <head></head> of the page.
    */
    public function pageHeader(DB_DataObject $do, $fb)
    {
        return "";
    }


    /**
     * This is called before the datagrid is filled with data.
     *
     * @param DB_DataObject       $do The dataobject used to fill the grid
     * @param Structures_DataGrid $dg The datagrid
     *
     * @return string
     */
    public function beforeDataGridFill(DB_DataObject $do, Structures_DataGrid $dg)
    {
        return "";
    }

    /**
     * Called before the datagrid-output.
     *
     * The output from this is placed before the datagrid-output.
     *
     * @param DB_DataObject       $do The dataobject used to fill the grid
     * @param Structures_DataGrid $dg The datagrid
     *
     * @return string
     */
    public function beforeDataGridOutput(DB_DataObject $do, Structures_DataGrid $dg)
    {
        return "";
    }


    /**
     * Called after the datagrid-output.
     *
     * The output from this is placed after the datagrid-output.
     *
     * @param DB_DataObject       $do The dataobject that was used to fill the grid
     * @param Structures_DataGrid $dg The datagrid
     *
     * @return string
     */
    public function afterDataGridOutput(DB_DataObject $do, Structures_DataGrid $dg)
    {
        return "";
    }

    /**
     * Called after an edit
     *
     * @param DB_DataObject $do      The dataobject used in the process
     * @param array         $options 'url'   => The original url that will be
     *                                          redirected to
     *                               'saved' => Was the edit-operation done
     *                                          (== true) og cancelled?
     *
     * @return string   The new url to redirect to
     */
    public function afterEdit(DB_DataObject $do, array $options)
    {
        return "";
    }
}
