<?php
/**
 * Query mysql for information
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Mysql.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery.php';
require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery/Interface.php';

/**
 * Query mysql using PEAR_DB, about its column-properties.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Mysql.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB_Mysql
   implements DB_DataObject_FormBuilder_Frontend_Util_DbQuery_Interface
{

    /**
     * Get info about all columns
     *
     * This info is the type of column, the max lenght and the name.
     *
     * @param DB_DataObject $do Dataobject to get info about
     *
     * @return array
     */
    public function getColumnInfo(DB_DataObject $do)
    {
        $db = $do->getDatabaseConnection();
        $database = $db->dsn['database'];
        $sql = "
          SELECT
             COLUMN_NAME as 'column',
             DATA_TYPE as type,
             CHARACTER_MAXIMUM_LENGTH as length
          FROM
             information_schema.columns
          WHERE
             TABLE_NAME = '" . $do->__table . "'
             AND TABLE_SCHEMA ='". $database."'
        ";
        $rv = $db->getAll($sql, array(), DB_FETCHMODE_ASSOC);

        if (PEAR::isError($rv)) {
            // error-handling
        } else {
            foreach ($rv as $key => &$val) {
                switch ($val['type']) {
                case 'varchar':
                case 'char':
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::CHAR;
                    break;
                case 'text':
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::TEXT;
                    break;
                default:
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::UNKNOWN;
                }
            }
        }
        return $rv;

    }

}
