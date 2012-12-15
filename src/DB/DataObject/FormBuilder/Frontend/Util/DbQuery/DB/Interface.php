<?php
/**
 * Query database for information
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Pgsql.php 252 2009-12-07 21:05:15Z mlj $
 * @link      http://www.gartneriet.dk/
 */

/**
 * Query a database using PEAR_DB about its column properties
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Pgsql.php 252 2009-12-07 21:05:15Z mlj $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB_Interface
{
    /**
     * Query database for info on columns in a table.
     *
     * @param DB_DataObject $do The dataobject to query
     *
     * @return array
     *
     * @see DB_DataObject_FormBuilder_Frontend_Util_DbQuery::getColumnInfo()
     */
    public function getColumnInfo(DB_DataObject $do);
}
