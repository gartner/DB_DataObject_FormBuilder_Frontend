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
 * @version  Release: SVN: $Id: Access.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Hooks_Access
{
    /**
     * Is it ok to delete the current record?
     * This could check if a logged-in user is allowed to delete the current record
     * If this returns false, the Frontend will not render a link to
     * delete the current record.
     *
     * @return bool
     */
    public function isDeleteable();

    /**
     * Is it ok to edit the current record?
     * See above ...
     *
     * @return bool
     */
    public function isEditable();

}
