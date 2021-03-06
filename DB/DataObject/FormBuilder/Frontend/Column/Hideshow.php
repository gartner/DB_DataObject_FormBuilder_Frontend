<?php
/**
 * This column will optionally hide the contents in it
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Hideshow.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Column.php';

/**
 * This column-type will make it possible to show or hide the contents of a
 * cell using javascript.
 * Default is hidden.
 *
 * Requires that you have jQuery on your pages.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Hideshow.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Column_Hideshow
    extends DB_DataObject_FormBuilder_Frontend_Column
{
    /**
     * This column support ordering
     */
    protected $allowOrderby = true;

    protected $neededOptions = array(
        'visible',
    );

    /**
     * Default options for this column
     * visibility  is hidden as default.
     *
     * @var array
     */
    protected $options = array(
        'visible'   => false,
    );

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
     * The actual callback of the column
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
        static $jsIsAppended = false;
        $rv = '';
        if (method_exists($params['record'], 'get'.ucfirst($field))) {
            $value = $params['record']->{'get'.ucfirst($field)}();
        } else {
            $value = $params['record']->$field;
        }
        if (!empty($value)) {
            if (false === $jsIsAppended) {
                $this->frontend->addAppend(
                    '<script type="text/javascript">
                    $("a.w").click(
                    function(){
                        $(this).next().toggle();
                        if ($(this).next().is(":hidden")) {
                            $(this).html("Vis &darr;");
                        } else {
                            $(this).html("Skjul &uarr;");
                        }
                        return false;
                    }
                    );
                    </script>'
                );
                $jsIsAppended = true;
            }

            if (true == $this->options['visible']) {
                $label = 'Skjul &uarr;';
                $style = '';
            } else {
                $label = 'Vis &darr;';
                $style = 'display: none;';
            }
            $rv .= '<a class="w" href="#">' . $label . '</a> ';
            $rv .= '<span style="' . $style . '">' . $value . '</span>';
        }
        return $rv;
    }

}
