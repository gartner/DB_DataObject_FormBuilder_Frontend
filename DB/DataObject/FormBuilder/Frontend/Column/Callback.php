<?php
/**
 * Callback-column
 *
 * This column will use a callback-method to retreive what should be shown in
 * the datagrid.
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Callback.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * Callback-column
 *
 * This will setup a column for Structures_DataGrid which uses a function-callback.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Callback.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Callback
    extends DB_DataObject_FormBuilder_Frontend_Column
{

    protected $allowOrderby = true;
    protected $neededOptions = array('callbackName', 'params');

    /**
     * Get a column which uses a callbackfunction.
     *
     * @param array $options Options for this column to use.
     *                       (Not used ...)
     *
     * @return Structures_DataGrid_Column
     *
     * @see DB_DataObject_FormBuilder_Frontend_Column::getColumn()
     */
    public function getColumn(array $options = array())
    {
        $col = $this->createColumn();
        $col->setFormatter(
            $this->options['callbackName'],
            $this->options['params']
        );

        return $col;

    }

}
