<?php
/**
 * DataObjects that implements this interface will provide extra functionality to
 * the Frontend.
 *
 * PHP Version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Access.php 175 2009-05-06 07:24:32Z mads $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Hooks_Edit
{
    /**
     * This method is called when the frontend has generated the form.
     *
     * @param DB_DataObject_FormBuilder_Frontend $frontend The frontend
     *
     * @return void
     */
    public function frontendPostGenerateForm(
        DB_DataObject_FormBuilder_Frontend $frontend
    );
}
