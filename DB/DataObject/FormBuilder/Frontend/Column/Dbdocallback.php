<?php
/**
 * Use a method on the dataobject as a formatter
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Dbdocallback.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * Use a method of the dataobject as formatter
 *
 * The method cannot take any parameters ...
 * Nothing is done to the output of this method - it is displayed
 * in the datagrid 'as-is'. If data needs escaping, you need to do it in
 * the method, before returning the data.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Dbdocallback.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Dbdocallback
    extends DB_DataObject_FormBuilder_Frontend_Column
{

    protected $allowOrderby = true;

    protected $neededOptions = array('callbackName');

    /**
     * Get a column that uses a method of the DataObject as a formatter-function
     *
     * @param array $options Options to use here.
     *                       (Currently not used ....)
     *
     * @return Structures_DataGrid_Column
     *
     * @see DB_DataObject_FormBuilder_Frontend_Column::getColumn()
     */
    public function getColumn(array $options = array())
    {
        $col = $this->createColumn();
        $col->setFormatter(array($this, 'dbdoCallback'), $this->options);

        return $col;

    }

    /**
     * This is the actual function called.
     *
     * This will then in turn call the needed method on the DataObject.
     *
     * @param array $params Parameters passed from Structures_DataGrid.
     * @param array $extra  Extra options needed - needs an array with a key called
     *                      'callbackName' which is the name of the method to call
     *
     * @return string|bool|array|object Returns whatever the called method returns...
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception
     *         - if the method does not exist in the dataobject
     */
    public function dbdoCallback($params, $extra=null)
    {
        $record = $params['record'];
        $method = $extra['callbackName'];

        if ($record instanceof DB_DataObject_FormBuilder_Frontend_Column_OptionsInterface) {
            $record->setColumnCallbackOptions($extra);
        }
        if (method_exists($record, $method)) {
            // Hack'ish: Getting options passed from configfile, then removing the callbackname,
            // before passing it to the callback.
            $options = $extra['__options'];
            unset($options['callbackName']);

            return call_user_func_array(array($record, $method), $options);

        } else {
            include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                "Method {$method} does not exists in " . get_class($record)
            );
        }
    }

}
