<?php
/**
 * Write javascript-code which enables the tinyMCE editor in textarea-fields
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: tinyMCE.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */
require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * Write javascript-code which enables the tinyMCE editor in textarea-fields
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: tinyMCE.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_TinyMCE
    extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * Options that this plugin can use:
     *
     * 'mergeOptions'   => (bool) Should options be merged with the default options?
     * 'jsBasePath'     => (string) Path to where your tiny_mce-folder is placed.
     * 'options'        => (array) Options for the initialization of tinyMCE.
     *      'tinyMCE_init_option' => value
     *           If value is a boolean, it will be set as a boolean
     *           If value is a string starting with 'function(', it will be set
     *           as a javascript-function, enabling you to use callback-hooks.
     *           Else, the value is set as a string.
     *
     * @var array
     */
    public $options = array(
        'mergeOptions'  => true,
        'jsBasePath'    => '/jscripts',
        //'options'   => array(),
    );

    /**
     * Default options for tinyMCE
     * @var array
     */
    protected $mceOptions = array(
        'mode'                    => 'textareas',
        'theme_advanced_toolbar_location'   => 'top',
        'theme'                   => 'advanced',
        'language'                => 'en',
        'theme_advanced_buttons1' =>
            'removeformat,separator,cut,copy,paste,undo,redo,formatselect,separator,bold,italic,underline,justifyleft,justifycenter,justifyright,separator,link,separator,code',
        'theme_advanced_buttons2' => 'fullscreen',
        'theme_advanced_buttons3' => '',
        'theme_advanced_resizing' => true,
        'theme_advanced_statusbar_location' => 'bottom',
        'content_css'             => '/css/main.css',
        'plugins'                 => 'advimage,advlink,fullscreen',
        'fullscreen_new_window'   => false,
        'extended_valid_elements' => 'a[name|href|target|title|onclick|class]',
        'relative_urls'           => false,
        'convert_urls'            => true,
        'debug'                   => false,
    );

    /**
     * Add javascript to the pageheader.
     *
     * @param DB_DataObject             $do The DataObject we are working on.
     * @param DB_DataObject_FormBuilder $fb The formBuilder making the form
     *
     * @return string This should be inserted into the header of the html-page.
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#pageHeader()
     */
    public function pageHeader (DB_DataObject $do, $fb)
    {
        $mode = $this->frontend->getMode();
        if ($mode == DB_DataObject_FormBuilder_Frontend::EDIT
            || $mode == DB_DataObject_FormBuilder_Frontend::ADD
        ) {
            return $this->writeMCE($do);
        }
    }

    /**
     * TODO: Remove, parents setOptions does this now.
     * Set the options this plugin uses.
     *
     * Options are merged with the defaults set in the plugin,
     * to keep sane settings if an option is not given.
     *
     * @param array $options Options passed to the plugin
     *
     * @return self
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#setOptions($options)
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Write javascript to include the tinyMCE-editor
     *
     * This will only enable the editor if it is set to on inside the dataobject.
     *
     * @param DB_DataObject $do DataObject for which to enable the editor - if on.
     *
     * @return string Text to include in the HTML-page.
     */
    public function writeMce($do)
    {
        if (!isset($this->options['options'])) {
            $options = $this->mceOptions;
        } else if (true == $this->options['mergeOptions']) {
            $options = array_merge($this->mceOptions, $this->options['options']);
        } else {
            $options = $this->options['options'];
        }
        ob_start();
        ?>
        <script type="text/javascript"
        src="<?php
        echo $this->options['jsBasePath'];
        ?>/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
        tinyMCE.init({ <?php
        $separator = "\n\t";
        foreach ($options as $option => $value) {
            echo $separator;
            if ($value === true || $value == '{true}') {
                echo "{$option} : true";
            } else if ($value === false || $value == '{false}') {
                echo "{$option} : false";
            } else if (substr($value, 0, 9) == 'function(') {
                echo "{$option} : {$value}";
            } else {
                echo "{$option} : \"{$value}\"";
            }
            $separator = ",\n\t";
        }
        ?>

        }); </script>
        <?php
        return ob_get_clean();

    }

}
