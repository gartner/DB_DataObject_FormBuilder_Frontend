<?php
/**
 * Database utilities
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Interface.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

/**
 * Interface of classes to query the database
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Interface.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Util_DbQuery_Interface
{

    /**
     * Get information about the columns in the database
     *
     * @return array With this structure:
     *	'columnName' => array(
     *		'type' 		=> column type as reported by database,
     *		'length'	=> length of storage in the column, only for
     *                      char/varchar, if unknown, null
     *		)
     *	)
     */
    public function getColumnInfo(DB_DataObject $do);

}

