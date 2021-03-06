<?php
/**
 * Use any defined validate-methods on the dataobject to validate the content
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Validator.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * Use any defined validate-methods on the dataobject to validate the content
 *
 * For this to work, the validate-methods have to accept one parameter: The value to
 * validate. This is not how DB_DataObject needs it to be - but doing like this:
 * function validateField($param=null) {
 *     if (null === $param) {
 *         $param = $this->field;
 *     }
 *     ... do the actual validation here, testing on $param
 * }
 * .. and the method will still work both here, and from DB_DataObject.
 *
 * Validate-methods should return true if valid, and a PEAR::Error on invalid data
 * If a PEAR::Error is returned, its message will be displayed as the errormessage
 * for the field.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Validator.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_Validator
   extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * The dataobject behind the form.
     * @var DB_DataObject
     */
    protected $dataObject = null;

    /**
     * This is called by the Frontend after generating the form.
     *
     * @param DB_DataObject             $do   DataObject to work on.
     * @param DB_DataObject_FormBuilder $fb   The formBuilder generating the form
     * @param HTML_QuickForm            $form The instance of QuickForm holding the
     *                                        form.
     *
     * @return bool|void
     */
    public function postGenerateForm(
        DB_DataObject $do,
        DB_DataObject_FormBuilder $fb,
        HTML_QuickForm $form
    ) {
        $this->dataObject = $do;

        $form->addFormRule(array($this, 'dataObjectValidate'));

        return;
    }

    /**
     * Form-rule for Quickform.
     *
     * This is the method called by HTML_QuickForm to validate the entire form
     *
     * @param array $fields Array('fieldname' => 'posted value', ...)
     *
     * @return bool|array True if everything is valid, else an
     * 					  array('fieldname' => 'error message')
     */
    public function dataObjectValidate($fields)
    {
        $result = array();
        foreach ($fields as $name => $value) {
            $method = 'validate' . ucfirst($name);
            if (method_exists($this->dataObject, $method)) {
                $reflector = new ReflectionMethod($this->dataObject, $method);
                if ($reflector->getNumberOfParameters() < 1) {
                    $this->dataObject->debug(
                        "$method needs to take one parameter to work "
                        . "with this plugin.",
                        5,
                        'flaf'
                    );
                } else {
                    $rv = $this->dataObject->$method($value);
                    if (true !== $rv) {
                        if (PEAR::isError($rv)) {
                            $result[$name] = $rv->getMessage(); 
                            //$message = $rv->getMessage();
                        } //else {
                          //  $message = $this->defaultMessage;
                        //}
                        //$result[$name] = $message;
                    }
                }
            }
        }

        return $result;
    }

}
