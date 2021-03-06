<?php
/**
 * Column to display dates
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Boolean.php 311 2010-04-23 05:56:44Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';
require_once 'Date.php';

/**
 * Columns to display a date
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Boolean.php 311 2010-04-23 05:56:44Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Date
    extends DB_DataObject_FormBuilder_Frontend_Column
{
    /**
     * This column support ordering
     */
    protected $allowOrderby = true;

    protected $neededOptions = array(
        'format',
    );

    protected $options = array(
        'useAccessors'  => false,
    );

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
        $col->setFormatter(array($this, 'format'), $this->options);

        return $col;

    }

    /**
     * Format the value for this column as a date
     *
     * @param array $params As passed by Structures_DataGrid
     * @param null  $extra  Extra options passed. ($this->options is passed,
     *                      but not used as $extra)
     *
     * @return string Column content formatted
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception
     */
    public function format($params, $extra=null)
    {
        if (! $params['record'] instanceof DB_DataObject) {
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                'Column_Date: Record must be DB_DataObject'
            );
        }

        $fieldName = $params['fieldName'];

        if (isset($this->options['useAccessors'])
            && $this->options['useAccessors'] == true
        ) {
            $accessor = 'get' . ucfirst($fieldName);
            $value = $params['record']->$accessor();
        } else {
            $value = $params['record']->$fieldName;
        }

        $date = new Date($value);

        return $date->format($this->options['format']);

    }

}
