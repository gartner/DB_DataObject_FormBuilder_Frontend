<?php
/**
 * Standard column to use in the frontend
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Standard.php 356 2011-02-06 08:09:13Z mlj $
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
 * @version  Release: SVN: $Id: Standard.php 356 2011-02-06 08:09:13Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Standard
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
     * This will get you a standard column with no extra magic.
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

        $options = $this->options + $options;

        if (isset($options['attributes'])) {
            $col->setAttributes($options['attributes']);
        }

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
        if (is_object($params['record'])) {
            $getter = 'get'.$field;
            if (method_exists($params['record'], $getter)) {
                $rv = call_user_func(array($params['record'], $getter));
            } else {
                $rv = $params['record']->$field;
            }
        } else {
            $rv = $params['record'][$field];
        }

        return htmlspecialchars($rv, ENT_COMPAT);

    }

}