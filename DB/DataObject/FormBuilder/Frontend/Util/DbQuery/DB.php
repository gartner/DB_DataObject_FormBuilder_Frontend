<?php
/**
 * Database query utilities
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Pgsql.php 184 2009-05-21 06:52:44Z mads $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery.php';

/**
 * Query database,using PEAR_DB, for its column properties
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Pgsql.php 184 2009-05-21 06:52:44Z mads $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB
    extends DB_DataObject_FormBuilder_Frontend_Util_DbQuery
{

    protected $do;

    protected $driver = null;

    /**
     * Constructor
     *
     * @param DB_DataObject $do The DataObject to work on.
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If class not
     *                                                          found
     */
    public function __construct(DB_DataObject $do)
    {
        $this->do = $do;

        $dbConn = $do->getDatabaseConnection();
        $dbStyle = ucfirst($dbConn->dbsyntax);
        $class = 'DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB_' . $dbStyle;
        $file = dirname(__FILE__) . '/DB/' . $dbStyle . '.php';
        if (!class_exists($class)) {
            // Autoload class
            //debug("factory: File: " . __FILE__ . '/' . $file);
            if (!file_exists($file)) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "DB_DataObject_FormBuilder_Frontend_Util_DbQuery::"
                    . "factory: class $class not found "
                    . "(tried to autoload $file)"
                );
            } else {
                include_once $file;
            }
        }

        $this->driver = new $class;
    }

    /**
    * Get info about all columns
    *
    * This info is the type of column, the max lenght and the name.
    *
    * @return array
    */
    public function getColumnInfo()
    {
        return $this->driver->getColumnInfo($this->do);
    }

}

