<?php
/**
 * DataObjects that implements this interface will provide filtering functionality to
 * the Frontend.
 *
 * PHP Version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Access.php 321 2010-07-06 04:49:06Z mlj $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Hooks_Filter
{
    /**
     * Called when listing content of a table
     *
     * This is used to setup some sort of filtering to the dataObject, before
     * filling the datagrid.
     *
     * @return void
     */
    public function filter();
}
