<?php
/**
 * Redirect to something else than the Frontend
 *
 * Using this plugin, it is possible to redirect to another action than the default
 * (showing the content of the table), when an edit is finished.
 * This can be either when data is actually saved, but also when the user cancel
 * an edit.
 *
 * PHP version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 211 2009-08-14 07:17:39Z mlj $
 * @link     http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * Redirect to something else than the Frontend
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: DateToDateselect.php 211 2009-08-14 07:17:39Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_Redirect
   extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * Default options
     *
     * @var array 'afterEditUrl'    => The url to go to. This is passed through
     *                                 sprintf(), with the arguments
     *                                 primary-key of the record, original_url:
     *                                 Ie: 'Primary key: %s, original_url: %s'
     *            'onlyOnSavedData' => If true and the abort-button was pressed
     *                                 in the form, it will redirect to the
     *                                 original url
     *            'debug'           => If true, just exits. This is done before
     *                                 the frontend forwards, thus making the
     *                                 browser show any debug-output from
     *                                 DB_DataObject, that is created in this
     *                                 request.
     */
    protected $defaultOptions = array(
        'afterEditUrl'   => null,
        'onlyOnSavedData'=> false,
        'debug'          => false,
    );

    /**
     * Set options for this plugin
     *
     * This will make sure that if some options are not set, the defaults are used.
     *
     * @param array $options Array of options to set.
     *
     * @return self
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#setOptions($options)
     */
    public function setOptions(array $options)
    {
        foreach ($this->defaultOptions as $name => $value) {
            if (!isset($options[$name])) {
                $options[$name] = $value;
            }
        }

        // If not set, use whatever is set in the frontend.
        // this avoids redirects to the frontpage if no afterEditUrl is set and the ScriptUrl is.
        //if ($options['afterEditUrl'] === null) {
        //    $options['afterEditUrl'] = $this->frontend->getScriptUrl();
        //}

        $this->options = $options;
        return $this;
    }

    /**
     * Redirect after the form has been edited
     * (or an edit has been aborted from the form)
     *
     * @param DB_DataObject $do      The dataobject being edited.
     * @param array         $options Options to use. Needed keys are:
     *                                  url   => The url that the frontend would
     *                                  		 original forward to
     *                                  saved => True if the record was saved,
     *                                  		 false if aborted
     *
     * @return void Will redirect and return nothing, unless the edit was aborted
     * 				and the global option 'onlyOnSavedData' is true, in which case
     * 				nothing is done or returned.
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If no primary
     *                                                          key exists
     */
    public function afterEdit(DB_DataObject $do, array $options)
    {
        $options = $this->options + $options;

        if ($options['onlyOnSavedData'] == true && $options['saved'] == false) {
            return;
        }

        if ($this->options['debug'] == true) {
            exit;
        }


        // If no url to go to after an edit, just return
        if ($this->options['afterEditUrl'] == null) {
            return;
        }

        /*
        if (empty($this->options['afterEditUrl'])) {
            require_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception("Plugin Redirect: Option 'afterEditUrl' not set.");
        }
        */

        if ($options['saved'] == true
            && !empty($this->options['afterEditUrl'])
        ) {
            if (!$key = DB_DataObject_FormBuilder::_getPrimaryKey($do)) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    'Plugin Redirect: Primary key not found in DataObject'
                );
            }
            //Encode the original url
            $url = urlencode($options['url']);

            $newUrl = sprintf($this->options['afterEditUrl'], $do->$key, $url);
        }

        header('Location: http://' . $_SERVER['HTTP_HOST'] . $newUrl);
        header('Connection: close');
        exit;

    }
}
