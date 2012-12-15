<?php
/**
 * Query postgresql for information
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Pgsql.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery.php';
require_once 'DB/DataObject/FormBuilder/Frontend/Util/DbQuery/Interface.php';

/**
 * Query postgreSQL using PEAR_DB about its column properties
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Pgsql.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB_Pgsql
    implements DB_DataObject_FormBuilder_Frontend_Util_DbQuery_DB_Interface
{

    /**
     * Get info about all columns
     *
     * This info is the type of column, the max lenght and the name.
     *
     * @param DB_DataObject $do Dataobject to query
     *
     * @return array
     */
    public function getColumnInfo(DB_DataObject $do)
    {
        $db = $do->getDatabaseConnection();
        $rv = $db->getAll(
            "SELECT
                a.attname AS column,
                pg_catalog.format_type(a.atttypid, a.atttypmod) AS type
            FROM
                pg_catalog.pg_attribute a, pg_catalog.pg_class c
            WHERE
                a.attnum > 0
                AND a.attrelid = c.oid
                AND c.relname = '" . $do->__table . "'
                AND NOT a.attisdropped
            ORDER BY
                a.attnum
            ", array(), DB_FETCHMODE_ASSOC
        );
        if (PEAR::isError($rv)) {
            // error-handling
        } else {
            foreach ($rv as $key => &$val) {
                if (substr($val['type'], 0, 9) == 'character') {
                    preg_match("/.*\((\d+)\)/", $val['type'], $match);
                    $val['length'] = $match[1] ;
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::CHAR;
                } else if (substr($val['type'], 0, 4) == 'text') {
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::TEXT;
                    //$val['length'] = null;

                } else {
                    $val['itype']
                        = DB_DataObject_FormBuilder_Frontend_Util_DbQuery::UNKNOWN;
                    $val['length'] = null;
                }
            }
        }
        return $rv;

    }

}
