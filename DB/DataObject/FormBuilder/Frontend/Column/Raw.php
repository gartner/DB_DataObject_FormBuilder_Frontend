<?php
/**
 * Make a column for un-escaped data
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Raw.php 353 2011-02-05 08:25:51Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * This column-type will display the content of a column without
 * escaping the output.
 *
 * This is usefull if you need to display some html inside the datagrid.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Raw.php 353 2011-02-05 08:25:51Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Raw
    extends DB_DataObject_FormBuilder_Frontend_Column
{
    /**
     * This column support ordering
     *
     * @var boolean
     */
    protected $allowOrderby = true;

    /**
     * Get the column
     *
     * @param array $options Options for this column
     *                       (Currently not used)
     *
     * @return Structures_DateGrid_Column
     *
     * @see DB_DataObject_FormBuilder_Frontend_Column::getColumn()
     */
    public function getColumn(array $options=array())
    {
        $col = $this->createColumn();
        $col->setFormatter(array($this, 'format'), $this->field);

        return $col;

    }

    /**
     * Output a record without it being htmlspecialchars()'ed.
     *
     * @param array  $params Parameters passed by Structures_DataGrid
     *                       'record' => An instance of DB_DataObject, with the
     *                       record to display.
     * @param string $field  The field to use (from the DataObject)
     *
     * @return string
     */
    public function format(array $params, $field)
    {
        $getter = 'get'.$field;
        if (method_exists($params['record'], $getter)) {
            return call_user_func(array($params['record'], $getter));
        } else {
            return $params['record']->$field;
        }
    }

}
