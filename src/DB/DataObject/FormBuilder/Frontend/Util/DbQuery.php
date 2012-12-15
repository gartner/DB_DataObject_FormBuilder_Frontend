<?php
/**
 * Database utilities-class
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: DbQuery.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery/Interface.php';

/**
 * DB-utils must implement this interface.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DbQuery.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Util_DbQuery
{

    const UNKNOWN=0;
    const CHAR=1;
    const TEXT=2;

    /**
     * Get an instance of the Util_DB-class that corresponds to the database used
     *
     * This can then in turn be used by plugins, or whatever, to query the database
     * about column-info.
     *
     * @param DB_DataObject $do The DataObject to query
     *
     * @return DB_DataObject_FormBuilder_Frontend_Util_DbQuery_Interface
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception if a class can-
     *                                                          not be loaded.
     */
    static public function factory(DB_DataObject $do)
    {
        $dbConn = $do->getDatabaseConnection();

        $dbDriver = substr(
            get_class($dbConn), 0, strpos(get_class($dbConn), '_')
        );

        $class = 'DB_DataObject_FormBuilder_Frontend_Util_DbQuery_' . $dbDriver;
        if (!class_exists($class)) {
            // Autoload class
            $file = dirname(__FILE__) . '/' . $dbDriver . '/' . $dbStyle . '.php';
            if (!file_exists($file)) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "DB_DataObject_FormBuilder_Frontend_Util_DbQuery::"
                    . "factory: class $class not found"
                    . "(tried to autoload $file)"
                );
            } else {
                include_once $file;
            }
        }

        $rv = new $class($do);
        // TODO: Check that $rv is actually a DB_DataObject_FormBuilder_Frontend_Util_DbQuery_Interface

        return $rv;

    }

}

