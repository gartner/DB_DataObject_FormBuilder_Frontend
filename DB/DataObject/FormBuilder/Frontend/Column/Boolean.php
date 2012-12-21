<?php
/**
 * Boolean column for DB_DataObject_FormBuilder_Frontend
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Boolean.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * Treat a column as boolean
 *
 * Options for this is:
 *  'true'  => What to display if value is true
 *  'false' => What to display if value is false
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Boolean.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Boolean
    extends DB_DataObject_FormBuilder_Frontend_Column
{
    /**
     * This column support ordering
     */
    protected $allowOrderby = true;

    /**
     * Options that is required for this column-type.
     *
     * @var array 'true' => Display this if column is truethy
     *            'false => Display this if column is not truethy
     */
    protected $neededOptions = array(
        'true', 'false',
    );

    /**
     * @var array Default value for options
     *            'useAccessors' => Use getter-functions on the dataobject to
     *            get the values from it. If they exists.
     */
    protected $options = array(
        'useAccessors'  => false,
    );

    /**
     * Get the column
     *
     * This will get you a standard column with no extra magic.
     *
     * @param array $options Options for this column
     *
     * @return Structures_DateGrid_Column
     *
     * @see DB_DataObject_FormBuilder_Frontend_Column#getColumn()
     */
    public function getColumn(array $options=array())
    {
        $col = $this->createColumn();
        $col->setFormatter(array($this, 'format'), $this->options);

        return $col;

    }

    /**
     * Return a string, according to the truthyness of the column...
     *
     * @param array $params Parameters passed from Structures_DataGrid
     * @param null  $extra  Extra parametes for the formatter. The content of
     *                      $this->options is passed, but not actually used ...
     *
     * @return string
     */
    public function format($params, $extra=null)
    {
        $fieldName = $params['fieldName'];

        if (isset($this->options['useAccessors'])
            && $this->options['useAccessors'] == true
        ) {
            $accessor = 'get' . ucfirst($fieldName);
            if ($params['record']->$accessor()) {
                return $this->options['true'];
            } else {
                return $this->options['false'];
            }
        }

        if ($params['record']->$fieldName) {
            return $this->options['true'];
        } else {
            return $this->options['false'];
        }

    }

}