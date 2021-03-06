<?php
/**
 * Fill in a template with the contents of the dataobject
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Dbdocallback.php 311 2010-04-23 05:56:44Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * Fill in a template with data from the dataobject
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
 * @version  Release: SVN: $Id: Inflector.php 311 2010-04-23 05:56:44Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Inflector
    extends DB_DataObject_FormBuilder_Frontend_Column
{

    protected $allowOrderby = true;
    protected $neededOptions = array('template');

    /**
     * Get a column that uses a method of the DataObject as a formatting-function
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
        $col->setFormatter(array($this, 'inflect'), $this->options);

        return $col;

    }

    /**
     * This is the actual function called.
     *
     * This will then in turn call the needed method on the DataObject.
     *
     * @param array $params Parameters passed from Structures_DataGrid.
     * @param array $extra  Extra options needed - needs an array with a key called
     *                      'template' which is the string to inflect data in
     *
     * @return string The template with the data inflected
     */
    public function inflect($params, $extra=null)
    {
        $record = $params['record'];

        if ($record instanceof DB_DataObject_FormBuilder_Frontend_Column_OptionsInterface) {
            $record->setColumnCallbackOptions($extra);
        }

        if ($record instanceof DB_DataObject) {
            $data = $record->toArray();
        } else {
            $data = $record;
        }

        foreach ($data as $name => $content) {
            $search[]  = "{" . $name . "}";
            $replace[] = $content;
        }

        return str_replace($search, $replace, $this->options['template']);
    }

}
