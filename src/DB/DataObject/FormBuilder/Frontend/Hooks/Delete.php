<?php
/**
 * Hooks called when deleting.
 *
 * If the dataobject that the frontend uses, implements this interface,
 * then the functions here will be called according to comments below.
 *
 * PHP Version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Delete.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Hooks_Delete
{
    /**
     * Called before deleting a record
     *
     * @return boolean Only if true is returned, the record will be deleted
     */
    public function preDeleteHook();
    /**
     * Called before displaying the confirmation-page when deleting.
     *
     * @param HTML_QuickForm $form The form we are working with
     *
     * @return void
     */
    public function preAskDeleteHook(HTML_QuickForm $form);
    /**
     * Called after a record is deleted
     *
     * @return void
     */
    public function postDeleteHook();

}
