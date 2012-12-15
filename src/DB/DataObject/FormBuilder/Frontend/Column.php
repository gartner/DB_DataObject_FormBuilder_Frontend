<?php
/**
 * All column-types the frontend can use, has to be based on this
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Column.php 461 2012-11-16 20:09:01Z mlj $
 * @link      http://www.gartneriet.dk/
 */

/**
 * This is the base of all column-types for the datagrid.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Column.php 461 2012-11-16 20:09:01Z mlj $
 * @link     http://www.gartneriet.dk/
 */
abstract class DB_DataObject_FormBuilder_Frontend_Column
{

    public $field;
    public $label;
    public $orderby = false;
    public $attributes = array();

    protected $allowOrderby = true;
    protected $frontend;
    protected $options = array();

    /**
     * These options are required for this column-type.
     *
     * @var array
     */
    protected $neededOptions = array();

    /**
     * Constructor
     *
     * @param string                                 $field    Field of the
     *                                                         DataObject to
     *                                                         build column for
     * @param string                                 $label    Label to use for
     *                                                         the column
     * @param DB_DataObject_FormBuilder_Frontend $frontend This is the
     *                                                         Frontend for
     *                                                         which to generate
     *                                                         the column
     * @param array                                  $options  Options for this
     *                                                         specific column
     *
     * @return DB_DataObject_FormBuilder_Frontend_Column
     */
    public function __construct($field, $label,
        DB_DataObject_FormBuilder_Frontend $frontend,
        array $options=array()
    ) {
        $this->field = $field;
        $this->label = $label;

        $this->frontend = $frontend;

        $this->setOptions($options, array());
        return $this;
    }

    /**
     * Get a column to use in the datagrid
     *
     * @param array $options Options for this column
     *
     * @return Structures_DataGrid_Column
     */
    abstract public function getColumn(array $options=array());

    /**
     * Set options for the column
     *
     * Here, the options for this column is specified.
     *
     * @param array $options  'option_name' => 'value'
     *                        Standard options are:
     *                        allowOrderBy (bool) Can the column be ordered?
     * @param array $required Array of required option-keys. If null (default) no
     *                        check on the required options is done. Pass an empty
     *                        array if you need to check that the options required
     *                        by the column is set, are actually there.
     *
     * @return DB_DataObject_FormBuilder_Frontend_Column
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If required options
     *                                                          not given
     */
    public function setOptions(array $options, array $required=null)
    {
        if (null !== $required) {
            // Check both on the options given here, and the one set for the class
            $required = array_merge($this->neededOptions, $required);

            foreach ($required as $keyRequired) {
                if (!array_key_exists($keyRequired, $options)
                    && !array_key_exists($keyRequired, $this->options)
                ) {
                    include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                    throw new DB_DataObject_FormBuilder_Frontend_Exception(
                        "Required option '$keyRequired' for the column "
                        . "labeled '{$this->label}' is not given"
                    );
                }
            }
        }
        if (isset($options['allowOrderBy'])
            && ($this->allowOrderby == true)
            && $options['allowOrderBy'] == true
        ) {

            $this->orderby = $this->field;
        } else {
            // Error: Doesn't support ordering
            $this->orderby = null;
        }

        if (isset($options['attributes'])) {
            $this->attributes = $options['attributes'];
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Create a column with the label/field/orderby/attributes set by the constructor
     *
     * @return Structures_DataGrid_Column
     */
    protected function createColumn()
    {
        return new Structures_DataGrid_Column(
            $this->label,
            $this->field,
            $this->orderby,
            $this->attributes,
            null,
            null,
            null
        );
    }

}
