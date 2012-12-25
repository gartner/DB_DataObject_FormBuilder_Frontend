<?php
/**
 * FAQ:
 * To get custom column-data in the table-list-view:
 * Create a 'virtual' field in the DataObject (See DBDO_formbuilder docs about it),
 * and use this virtual field as a column
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Frontend.php 465 2012-11-21 08:11:37Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder.php';

/**
 * This is the main class of the frontend.
 *
 * This handles generation of a list of editable tables, the editform
 * and the deletion of records.
 * Everything is configured using an XML-file, which specifies the tables that
 * can be edited, which plugins to use and so on.
 *
 * Some options can be set in the dataobject:
 *
 * $fe_pluginOptions[<pluginname>] = array(..., ...)
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Frontend.php 465 2012-11-21 08:11:37Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend
{
    /**
     * Instance of dataobject to work on
     * @var DB_DataObject
     */
    protected $do = null;

    /**
     * The content of the configfile(s)
     *
     * @var string
     */
    protected $configXML = null;

    /**
     * The configuration, as a DOMDocument
     *
     * @var DOMDocument
     */
    protected $configDOM = null;

    /**
     * The datagrid
     *
     * @var Structures_Datagrid
     */
    protected $dg = null;

    /**
     * The formbuilder
     *
     * @var DB_DataObject_FormBuilder
     */
    protected $fb = null;

    /**
     * The form-object we work on
     *
     * @var HTML_QuickForm
     */
    protected $form = null;

    /**
     * Use this renderer when outputting a form
     * If empty, use the toHtml-method of the formobject
     *
     * @var HTML_QuickForm_Renderer
     */
    protected $quickFormRenderer = null;
    /**
     * Use this method on the renderer, when outputting the form.
     *
     * @var string
     */
    protected $quickFormRendererMethod = null;

    /**
     * Templates for the QuickForm-renderer.
     * However, only if it is the HTML_QuickForm_Renderer_Default that is used.
     * Templates are always assigned to the renderer if it is the _Default-renderer,
     * even if you set the renderer using setQuickFormRenderer()!!
     *
     * @var array Templates to set for the quickformRenderer
     *            Only set for the default renderer (HTML_QuickForm_Renderer_Default)
     *            Templates used:
     *            'element'      => passed to setElementTemplate()
     *            'form'         => passed to setFormTemplate()
     *            'groupElement' => passed to setGroupElementTemplate()
     *            'group'        => passed to setGroupTemplate()
     *            'header'       => passed to setHeaderTemplate()
     *            'requiredNote' => passed to setRequiredNoteTemplate();
     */
    protected $quickFormTemplates = array(
        'form' => '
            <form{attributes}>
                <div>
                {hidden}
                    <table class="form">
                    {content}
                    </table>
                </div>
            </form>
        ',
    );

    /**
     * Loaded plugins.
     *
     * The plugin-name is used as key, the contents is an array:
     * The following keys in the content are used:
     *  __options   => Options for the plugin
     *  __policy    => PLUGIN_ENABLE|PLUGIN_DISABLE - should the plugin be used?
     *  __loader    => array(
     *                    'className' => Name of the plugin-class
     *                    'path'      => path/filename to the file where the class is
     *                 )
     *  __instance  => Instance of the plugin-class
     *  __className => Classname of this plugin.
     *
     * (All plugins inherits from DB_DataObject_FormBuilder_Frontend_Plugin)
     *
     * @var array
     */
    protected $plugins = array();

    // TODO: Should be defined in Frontend_Plugin
    const PLUGIN_DISABLE = 0;
    const PLUGIN_ENABLE  = 1;

    protected $pluginPolicy = self::PLUGIN_ENABLE;

    /**
     * Flag to tell if loadPlugins() has been called.
     *
     * @var boolean
     */
    protected $pluginsLoaded = false;

    /**
     * Columns to list in the datagrid for a given table
     *
     * @var array() array(
     *                  <columnName> => array(
     *                      'displayName' => Label to display
     *                      'type'        => Type of column to add to datagrid
     *                      'allowOrderBy'=> Can the column be sorted?
     *                  )
     *              );
     */
    protected $listColumns = array();

    /**
     * Order data in listview according to this array
     * colomnname => ASC|DESC
     *
     * @var array
     */
    protected $orderby = array();

    /**
     * Javascripts used by plugins - should be included in final page
     *
     * @var array
     * @deprecated
     */
    protected $javascripts = array();

    /**
     * External css-files that should be included in the final page

     * @var array
     * @deprecated
     */
    protected $externalCss = array();

    /**
     * Parameter this instance is called with (query_string)
     */
    protected $params = array();

    public $allowAdd       = true;
    public $allowDelete    = true;
    public $confirmDelete  = true;
    public $deleteMessage  = "";
    public $recordsPerPage = 25;
    public $displayName    = null;
    public $description    = null;

    /**
     * Name of table to list/edit
     *
     * @var string
     */
    public $tableName      = null;

    /**
     * Name of table to use when displaying the list in listview
     * If not set, use $this->tableName instead.
     *
     * @var string|null
     */
    public $listName       = null;

    /**
     * The css-file specified in the config.
     *
     * @deprecated - build a frontend_renderer to take care of this?
     */
    public $css            = "/main.css";

    /**
     * Path to where javascripts from $this->javascripts  is placed.
     *
     * @var string
     * @deprecated
     */
    protected $javascriptPath = '/jscripts/';

    /**
     * Path to where css from $this->externalCss is placed
     *
     * @var string
     * @deprecated
     */
    protected $cssPath        = '/';

    /**
     * Output to append after the list/form
     *
     * @var string
     */
    public $append;

    /**
     * Html/output to prepend to the output
     * 
     * @var string
     */
    public $prepend;
    
    /**
     * The calling script's url. Can be overridden
     * This is usefull if you display the list of tables on one page, but have
     * the actual editing on another.
     *
     * @var string
     */
    public $baseUrl = null;

    /**
     * Labels used in the frontend
     *
     * @var array
     */
    protected $labels = array(
        'addNew'              => 'Add new record',
        'edit'                => 'Edit',
        'delete'              => 'Delete',
        'deleteConfirmYes'    => 'Yes',
        'deleteConfirmNo'     => 'No',
        'tableColumn'         => 'Table',
        'tableDescription'    => 'Description',
        'cancelSubmit'        => 'Cancel',
        'recordsPrPage'       => 'Records shown pr. page',
        'recordsPrPageSubmit' => 'Change',
    );

    /**
     * These options can be read from config and is set on the DataObject
     * for configuring the FormBuilder
     *
     * @var array
     */
    protected $fbOptions = array(
        'requiredNote' => '
        <span style="font-size:80%;">Felter markeret med
         <span style="color:#ff0000;">*</span> skal udfyldes.</span>
        ');

    /**
     * Array of options passed to the Structures_DataGrid_Renderer_HTMLTable
     * These are read from the config-file.
     *
     * @var array
     */
    protected $dataGridRendererOptions = array();

    /**
     * Attributes for table produced by Structures_DataGrid_Renderer_HTMLTable
     * Stored as 'attribute_name' => 'value'
     *
     * @var array
     */
    protected $dataGridRendererAttributes = array();

    /**
     * The mode this instance is in, see const below
     *
     * @var int|const
     */
    protected $mode = null;

    /**
     * Url-format for the datagrid
     *
     * @var string|Net_URL_Mapper
     */
    protected $dataGridUrlFormat = null;

    /**
     * Prefix for this instance.
     *
     * Currently only used in the datagrid

     * @var string
     */
    protected $prefix = '';

    /**
     * Last error set on the formBuilder
     * - will get set if there is an error fraom the database 
     * when inserting/updating a record
     * 
     * @var boolean|string
     */
    public $formError = false;
    
    /**
     * How to display any formErrors.
     * It is always placed on top of the form itself (before the form)
     * - default in this template.
     *
     * Can take placeholders:
     * {message} - replaced with the error-message from the database
     * {details} - replaced with the error-details, as reported by the db-layer.
     * 
     * @var string
     */
    public $formErrorTemplate = '
        <div class="formError">
        <em>Database Error:</em>
        {message}<br/><br/>
        <em>Details:</em><br/>
        {details}
        </div>
        ';

    /**
     * Submit-buttons to add after the datagrid?
     *
     * @var array
     */
    protected $dataGridFormFields = array();

    /**
     * The mode that the frontend can run in.
     */
    const LISTTABLES = 0;
    const LISTTABLE  = 1;
    const EDIT       = 2;
    const DELETE     = 3;
    const ADD        = 4;

    /**
     * Class constructor.
     * Takes the path to a config-file as a paramter, and sets up the
     * frontend according to this.
     *
     * @param string $configFile /path/to/configfile
     * @param bool   $validate   Validate the XML? Default is false.
     */
    public function __construct($configFile, $validate=false)
    {
        $this->setConfig($configFile, $validate);

        // Load default config
        $this->readConfig(null);

        $this->parseQueryString();

    }

    /**
     * Set the configuration to use
     *
     * This has to be XML according to the DTD:
     * http://cms.palustris.dk/DB_DataObject_FormBuilder_Frontend-v1.dtd
     *
     * @param string $config   Either the XML itself or a file containing the
     *                         config-xml. If the data starts with <?xml it is
     *                         considered raw xml, else a filename
     * @param bool   $validate Should xml be validated?
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If file does not
     *                                                          exist
     *
     * @return DB_DataObject_FormBuilder_Frontend For fluent interface
     */
    public function setConfig($config, $validate = false)
    {
        if (strtolower(substr($config, 0, 5)) == '<?xml') {
            $configData = $config;
        } else {
            $configData = file_get_contents($config, FILE_USE_INCLUDE_PATH);
            if (false == $configData) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "Configfile $config not found"
                );
            }

        }

        $this->configDOM = new DOMDocument();
        $this->configDOM->formatOutput = true;

        $this->configDOM->loadXML($configData);

        // Validate XML
        if (true === $validate) {
            if (!$this->configDOM->validate()) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "Configfile $config does not validate"
                );
            }
        }

        return $this;

    }

    /**
     * Set the prefix used by this instance
     *
     * This prefix is currently used only on the datagrif
     * TODO: Use also on the QuickForm
     *
     * Call without arguments to reset the prefix
     *
     * @param string $prefix Prefix to add
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function setPrefix($prefix = '')
    {
        $this->prefix = (string) $prefix;

        return $this;
    }

    /**
     * Add a configuration.
     *
     * This adds to the current configuration, and has to take the same xml-
     * format as setConfig(). Any <defaults> in this config is ignored,
     * but all <table>-elements are added to the current configuration.
     *
     * @param string $config   Xml or filename to add. If string is starting
     *                         with <?xml its considered xml, else a filename
     * @param bool   $validate Should the xml be validated to the DTD?
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If file cannot
     *                                                          be read
     *
     * @return DB_DataObject_FormBuilder_Frontend For fluent interface.
     */
    public function addConfig($config, $validate = false)
    {
        if (strtolower(substr($config, 0, 5)) == '<?xml') {
            $configData = $config;
        } else {
            if (!file_exists($config)) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "Configfile $config not found"
                );
            }

            $configData = file_get_contents($config);
        }

        $dom = new DOMDocument();
        $dom->loadXML($configData);

        // Validate XML
        if (true === $validate) {
            if (!$dom->validate()) {
                include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "Configfile $config does not validate"
                );
            }
        }

        $xpath = new DOMXPath($dom);
        $xpathQuery = $xpath->query('/frontend/tables/*');
        $tablesNode = $this->configDOM->documentElement
            ->getElementsByTagName('tables')
            ->item(0);
        for ($i = 0; $i < $xpathQuery->length; $i++) {
            // and pump them into first one
            $tablesNode->appendChild(
                $this->configDOM->importNode($xpathQuery->item($i), true)
            );
        }

        return $this;
    }

    /**
     * Determine which mode we are running in
     *
     * @return int One of the mode-constants
     */
    public function getMode()
    {
        if (isset($_REQUEST['table'])) {
            if (isset($_REQUEST['delete'])) {
                $this->mode = self::DELETE;

            } else if (isset($_REQUEST['record'])) {
                $this->mode = self::EDIT;

            } else if (isset($_REQUEST['addRecord'])) {
                $this->mode = self::ADD;

            } else {
                // List contents of table
                $this->mode = self::LISTTABLE;
            }
        } else {
            // List all editable tables
            $this->mode = self::LISTTABLES;
        }

        return $this->mode;

    }

    /**
     * Return the instance of the DataObject we're working on.
     *
     * If no dataobject exists, a new one will be created, stored and returned.
     *
     * @return DB_DataObject
     */
    public function getDataObject()
    {
        if (!$this->do instanceof DB_DataObject) {
            if (!empty($this->tableName)) {
                $this->do = DB_DataObject::factory($this->tableName);
            } else {
                $this->do = new DB_DataObject;
            }
        }

        return $this->do;

    }

    /**
     * Inject the dataobject to use
     *
     * @param DB_DataObject $do Dataobject to use
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function setDataObject(DB_DataObject $do)
    {
        $this->do = $do;
        return $this;
    }

    /**
    * List all tables
    *
    * Uses Structures_DataGrid for the display
    * Since this uses the xml-configfile as data, the output of this function
    * is encoded in UTF-8.
    *
    * @return string The html-code to show.
    */
    public function listTables()
    {
        include_once 'Structures/DataGrid.php';
        $this->dg = new Structures_DataGrid();
        $this->dg->setRequestPrefix($this->prefix);

        // Bind the DataObject to the Datagrid
        $options = array(
            'natsort' => true,
        );

        /*
         * If the enabled-attribute of <table>-tag is not present, defaults
         * to 'yes'.
         * Cannot seem to do that using only xpath, so we loop through all
         * <table>-elements discards the disabled and now use the 'array'-
         * datasource of datagrid instead of xml.
         */
        $xml = simplexml_import_dom($this->configDOM);
        $xml = $xml->xpath('/frontend/tables/table');
        foreach ($xml as $index => $value) {
            $enabled = (string) $value['enabled'];
            if (!empty($enabled) && $enabled != 'yes' && $enabled != '1' ) {
                unset($xml[$index]);
            }
        }

        $this->dg->bind($xml, $options, 'Array');

        include_once 'DB/DataObject/FormBuilder/Frontend/Column/Callback.php';
        $col = new DB_DataObject_FormBuilder_Frontend_Column_Callback(
            'displayName',
            $this->labels['tableColumn'],
            $this,
            array(
                'callbackName' => array($this, 'linkToTableEdit'),
                'params'       => array(),
                'allowOrderBy' => true,
            )
        );
        $this->dg->addColumn($col->getColumn());

        include_once 'DB/DataObject/FormBuilder/Frontend/Column/Standard.php';
        $col = new DB_DataObject_FormBuilder_Frontend_Column_Standard(
            'description',
            $this->labels['tableDescription'],
            $this,
            array('allowOrderBy' => true)
        );
        $this->dg->addColumn($col->getColumn());

        return $this->getDataGridOutput();
    }

    /**
     * Make a link to a table-edit.
     *
     * Used as a callback for the datagrid, when displaying the list of tables.
     *
     * @param array $data As given by the Datagrid when using it as a callback
     *
     * @return string
     */
    public function linkToTableEdit($data)
    {
        if (is_array($data['record'])) {
            return sprintf(
                '<a href="%s?table=%s">%s</a>',
                $this->getScriptUrl(),
                $data['record']['name'],
                (empty($data['record']['displayName']) ?
                    $data['record']['name'] : $data['record']['displayName'])
            );
        } else {
            return sprintf(
                '<a href="%s?table=%s">%s</a>',
                $this->getScriptUrl(),
                (string) $data['record']->name,
                (!isset($data['record']->displayName) ?
                    (string) $data['record']->name :
                    (string) $data['record']->displayName)
            );
        }
    }

    /**
     * Set the url where the frontend lives
     *
     * @param string $url The base URL this script is running on.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Get the address of the url this instance runs on.
     *
     * @return string
     */
    public function getScriptUrl()
    {
        if ($this->baseUrl !== null) {
            return $this->baseUrl;
        } else if (isset($_SERVER['SCRIPT_URL'])) {
            return $_SERVER['SCRIPT_URL'];
        } else {
            return $_SERVER['PHP_SELF'];
        }
    }

    /**
     * Get the 'toolbar'
     *
     * The toolbar is a list of common links, when in table-content-list-mode
     * it will show an 'add new record'-link.
     *
     * @return string Html to display
     */
    public function getToolbar()
    {
        $formStart = '<form method="get" action="'
            . $this->getScriptUrl()
            . '">'
            . '<div>';
        $url = $this->getQueryParamsArray(array(), false, array('setPerPage'));
        foreach ($url as $name => $value) {
            $formStart .= '<input type="hidden" name="'
                . $name . '" value="'. $value . '" />';
        }
        $formEnd = '</div></form>';

        $out  = '';
        switch($this->getMode()) {
        case self::LISTTABLE:
            $out .= $this->getAddLink();
            // TODO: Should plugins be enabled here?
            // TODO: Labels should be configurable
            // TODO: Selectable records here should be configurable
            $out .= '&nbsp;|&nbsp;';
            $out .= $this->labels['recordsPrPage'] . ': ';
            $recordsPerPage = array(5,10,25,50,100);
            $out .= '<select name="recordsPerPage"'
                . ' onchange="this.form.submit();">';
            foreach ($recordsPerPage as $val) {
                $out .= '<option value="' . $val . '"';
                if ($val == $this->recordsPerPage) {
                    $out .= ' selected="selected"';
                }
                $out .= '>' . $val . '</option>';
            }
            $out .= '</select>';
            $out .= '<input type="submit" name="__submitSetPerPage__" ';
            $out .= 'value="' . $this->labels['recordsPrPageSubmit'] . '" />';

            break;

        }

        return $formStart . $out . $formEnd;
    }

    /**
     * Return the name of the table
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Return the friendlyname of a table (The name displayed to users)
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Return the description of the table
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * If in LISTTABLE-mode and it is allowed to add records,
     * return a link that takes you there
     *
     * @return string
     */
    public function getAddLink()
    {
        $rv = "";
        if ($this->mode == self::LISTTABLE) {
            if ($this->allowAdd == true) {
                $rv = sprintf(
                    '[ <a href="%s">%s</a> ]',
                    $this->getUrl(
                        array('addRecord' => 1), false, array('record')
                    ),
                    $this->labels['addNew']
                );
            }
        }
        return $rv;
    }

    /**
     * Handles the deletion.
     *
     * If deletes should be confirmed, first display a form with the record(s)
     * to delete, and an 'ok' and 'cancel'-button. Only if 'ok' is pressed
     * or deletes should not be confirmed, records are deleted.
     *
     * @return string
     */
    public function delete()
    {
        // Delete record
        $postedUrl  = array();
        $this->mode = self::DELETE;

        // TODO: use getDataObject() here
        $this->do = DB_DataObject::factory($this->tableName);
        $this->readConfig($this->tableName);

        $pk = DB_DataObject_FormBuilder::_getPrimaryKey($this->do);

        if (!($records = @unserialize($_REQUEST['record']))) {
            $records = & $_REQUEST['record'];
        }

        // If deletes should be confirmed
        if ($this->confirmDelete && !isset($_REQUEST['confirmed'])) {

            if (!empty($this->listName)) {
                $do = DB_DataObject::factory($this->listName);
                $this->setDataObject($do);
            }

            include_once 'HTML/QuickForm.php';
            $this->form = new HTML_Quickform(
                'delform', 'post', '', '', null, false
            );

            // plugins will be called by getFormBuilder(), when creating the
            // formBuilder
            //$this->callPlugins('preGenerateForm');

            $this->getFormBuilder()->useForm($this->form, false);

            // Make it post to the correct page (Ie. if using Zend Framework)
            $this->form->setAttribute('action', $this->getScriptUrl());

            // If not done like this, it will not use the serialized array ....
            // Don't know why it is like that .....
            $this->form->addElement('hidden', 'record');
            $this->form->getElement('record')->setValue(serialize($records));

            $this->form->addElement('hidden', 'table', @$_REQUEST['table']);
            $this->form->addElement('hidden', 'delete', 'confirmed');
            $this->form->addElement(
                'hidden', '__url',
                serialize($this->getQueryParamsArray())
            );

            // callback into the DBDO to alter form.
            if ($this->do instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Delete) {

                $this->do->preAskDeleteHook($this->form);
            }

            $yes = $this->form->createElement(
                'submit', 'not_confirmed',
                $this->labels['deleteConfirmNo']
            );
            $no  = $this->form->createElement(
                'submit', 'confirmed',
                $this->labels['deleteConfirmYes']
            );
            $this->form->addGroup(array($no, $yes));

            $this->callPlugins('postGenerateForm');

            if (!isset($_REQUEST['not_confirmed'])
                && !isset($_REQUEST['confirmed'])
            ) {
                // Fill the datagrid with records selected for deletion
                if (is_array($records)) {
                    foreach ($records as $v) {
                        $tmpid[] = $this->do->escape($v);
                    }
                    $this->do->whereAdd();
                    $this->do->whereAdd(
                        "$pk IN (" . implode(', ', $tmpid) . ")"
                    );
                } else {
                    $this->do->$pk = $records;
                }
                $this->setupDataGrid(false, false);

                $out  = '<p>' . $this->deleteMessage . '</p>';
                $out .= $this->getDataGridOutput();
                $_POST['record'] = serialize($_REQUEST['record']);
                $_REQUEST['record'] = $_POST['record'];
                $out .= $this->form->toHtml();
                $out .= $this->append;
                return $out;
            }

            $postedUrl = unserialize($this->form->getSubmitValue('__url'));
        }

        // Do the actual deletion
        if (!$this->confirmDelete
            || ($this->confirmDelete && isset($_REQUEST['confirmed']))
        ) {

            if (!empty($records)) {
                if (is_array($records)) {
                    $tmpid = array();
                    foreach ($records as $v) {
                        $tmpid[] = $this->do->escape($v);
                    }

                    $this->do->whereAdd();
                    $this->do->whereAdd(
                        "$pk IN (" . implode(', ', $tmpid) . ")"
                    );
                    $deleteMode = DB_DATAOBJECT_WHEREADD_ONLY;
                } else {
                    $this->do->$pk = $records;
                    $deleteMode     = false;
                }
                // Callback into DBDO for preDelete()
                $cancelDelete = false;
                if ($this->do instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Delete) {

                    $cancelDelete = $this->do->preDeleteHook();
                }

                if ($cancelDelete !== true) {
                    $this->do->delete($deleteMode);

                    // Callback into DBDO for postDelete()
                    if ($this->do instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Delete) {

                        $this->do->postDeleteHook();
                    }
                }
            }
        }

        $url = $this->getUrl(
            $postedUrl, false, array('record',
            'addRecord', 'delete', 'confirmed', 'not_confirmed'), '&'
        );
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url);
        header('Connection: close');
        exit;

    }

    /**
     * Inject an HTML_QuickForm to use
     *
     * @param HTML_QuickForm $form The QuickForm-object to use
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function useForm(HTML_QuickForm $form)
    {
        $this->getFormBuilder()->useForm($form, false);

        return $this;
    }

    /**
     * Handle the edit of a record
     *
     * If an edit is successfully saved, it will send a http-header that
     * redirects to the listtable-page.
     *
     * @param string $onFinishUrl Url to go to, when operation is finished.
     *                            This should be a full url, excluding the
     *                            protocol. If null, go to listtable-view.
     * @param bool   $noRedirect  If true, no http-redirect is made after
     *                            successfully saving a form
     *                            (mostly used in debug)
     *
     * @return string HTML-output of the form.
     */
    public function edit($onFinishUrl = null, $noRedirect = false)
    {
        $do = $this->getDataObject();

        $this->readConfig($do->tableName());

        // TODO: _getPrimaryKey() is marked as private/protected
        $pk = DB_DataObject_FormBuilder::_getPrimaryKey($do);

        // If a record is specified, load this record for editing.
        // If not, a new record will be added.
        if (isset($_REQUEST['record']) && strlen($_REQUEST['record']) > 0) {
            $do->$pk = $_REQUEST['record'];
            $do->find(true);
        }

        // TODO: If the dataobject implements
        // DB_DataObject_FormBuilder_Frontend_Hooks_Access
        // check that isEditable() returns true.

        // Set formbuilder-options from the configfile, on the DataObject.
        // This WILL override any options set inside the DataObject-class-file!
        foreach ($this->fbOptions as $key => $value) {
            $key             = 'fb_' . $key;
            $do->$key = $value;
        }

        // We have changed the dataObject, so a new formBuilder needs to be created
        $this->getFormBuilder(true);
        $form = $this->getForm();

        // Make it post to the correct page (Ie. if using Zend Framework)
        $form->setAttribute('action', $this->getScriptUrl());

        $form->setRequiredNote($this->fbOptions['requiredNote']);

        $form->addElement('hidden', 'record', @$_REQUEST['record']);
        $form->addElement('hidden', 'table', @$_REQUEST['table']);
        $form->addElement(
            'hidden', '__url', serialize($this->getQueryParamsArray())
        );

        // Create submit-buttons
        if (!isset($do->fb_createSubmit) || true == $do->fb_createSubmit) {
            $form = $this->createSubmitGroup($form, '__submitGroup__');
        }

        $this->callPlugins('postGenerateForm');

        if ($do instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Edit) {
            $do->frontendPostGenerateForm($this);
        }

        // Redirect later if:
        // Cancel-button is pressed
        // or Form validates and is saved.
        $redirect = false;
        // Flag to show if the record is saved.
        $saved = false;

        $submitGroup = $form->getSubmitValue('__submitGroup__');
        if (isset($submitGroup['__submit__'])) {
            if ($form->validate()) {
                //DB_DataObject::debugLevel(3);
                $result = $form->process(
                    array($this->getFormBuilder(), 'processForm'),
                    false
                );
                if (! $result || $do->_lastError) {
                    /*
                     * if formBuilders validateOnProcess-option is true,
                     * the DataObject might return errors. We catch those,
                     * and if they are for an editable field, attach error
                     * to th field, else show them as formErrors
                     */
                    $errors = $this->getFormBuilder()->getValidationErrors();

                    $editableFields = $this->getFormBuilder()
                        ->_getUserEditableFields();

                    if (is_array($errors)) {
                        foreach ($errors as $field => $error) {
                            if (true !== $error) {
                                // In case of a PEAR_Error, convert to string
                                if ($error instanceof PEAR_Error) {
                                    $error = $error->getMessage();
                                } else if (false === $error) {
                                    $error = 'false';
                                }

                                // Attach the error to the form-element
                                if (in_array($field, $editableFields)) {
                                    $elementName = $this->getFormBuilder()
                                        ->getFieldName($field);
                                    $form->setElementError(
                                        $elementName,
                                        $error
                                    );
                                } else {
                                    // DataObject returned validation errors
                                    // for fields not editable in the form
                                    // attach these as form-errors
                                    $this->addFormError(
                                        'DataObject validation error',
                                        "$field: $error"
                                    );
                                }
                            }
                        }
                    }

                    /*
                     * The database could also return an error if there is
                     * problems with the data we're trying to save.
                     * Attach this as a formError
                     */
                    if ($do->_lastError instanceof PEAR_Error) {
                        $this->addFormError(
                            $do->_lastError->getMessage(),
                            $do->_lastError->userinfo
                        );
                    }

                } else {
                    $saved         = true;
                    $redirect      = true;
                    $do->lastError = false;
                }
                //DB_DataObject::debugLevel(0);
            }
        } else if (isset($submitGroup['__cancelSubmit__'])) {
            $redirect = true;
        }

        if ($redirect === true) {
            $postedUrl = unserialize($form->getSubmitValue('__url'));
            $url = $this->getUrl(
                $postedUrl, false,
                array('record', 'addRecord'), '&'
            );

            $options = array(
                'url'   => $url,
                'saved' => $saved,
            );
            $this->callPlugins('afterEdit', $options);

            if (null === $onFinishUrl) {
                $onFinishUrl = $_SERVER['HTTP_HOST'] . $url;
            }

            if (!$noRedirect) {
                header('Location: http://' . $onFinishUrl);
                header('Connection: close');
            }
            exit;
        }

        return $this->getFormOutput();

    }

    /**
     * Create submitbuttons in a group
     *
     * Creates a group with two submitbuttons. One will submit the form
     * the other will cancel any change..
     *
     * @param HTML_QuickForm $form The form the group should be added to
     * @param string         $name The name of the submitgroup
     *
     * @return HTML_QuickForm The form with the groupd added
     */
    public function createSubmitGroup(
        HTML_QuickForm $form,
        $name = "__submitGroup__"
    ) {
        $yes = $form->createElement(
            'submit', '__submit__', $this->getFormBuilder()->submitText
        );
        $no  = $form->createElement(
            'submit', '__cancelSubmit__',
            $this->labels['cancelSubmit']
        );
        $form->addGroup(array($yes, $no), '__submitGroup__');

        return $form;

    }


    /**
     * Decide if to show a list of tables, a list of a tables contents or an
     * editable form.
     *
     * @param int $forceMode One of mode-constants. Force to process as this.
     *                       If null, its automatic.
     *                       Only LISTTABLES is recognised currently
     *
     * @return string The contents of the page - a form, a list of tables,
     *                a list of a tables contents ...
     */
    public function process($forceMode = null)
    {
        $mode = $this->getMode();

        if (self::LISTTABLES !== $mode && self::LISTTABLES !== $forceMode) {

            $this->tableName = $_REQUEST['table'];

            if (self::DELETE === $mode) {

                return $this->delete();

            } else if ((self::EDIT == $mode || self::EDIT == $forceMode)
                && true == $this->allowAdd
            ) {
                // Edit or add a record, checking its allowed
                return $this->edit();

            } else if ((self::ADD == $mode || self::ADD == $forceMode)
                && true == $this->allowAdd
            ) {
                return $this->edit();

            } else {
                // List contents of table
                $table = $_REQUEST['table'];

                $this->listTable($table);

                $out = '';
                $out  .= $this->getPager()->links;

                $out .= implode(
                    $this->callPlugins('beforeDataGridOutput'), "\n"
                );

                if (count($this->dataGridFormFields) > 0) {
                    $out .= '<form action="" method="post"><div>';
                }

                $out .= $this->getDataGridOutput();

                if (count($this->dataGridFormFields) > 0) {
                    $out .= implode("\n", $this->dataGridFormFields);
                    $out .= "\n";
                    $out .= "</div></form>";
                }

                $out .= implode(
                    $this->callPlugins('afterDataGridOutput'), "\n"
                );

                $out .= $this->getPager()->links;
                $out .= $this->append;

                return $out;
            }
        } else {
            // List all editable tables
            // TODO: Load plugins here too, but first when hooks are implemented
            // $this->loadPlugins();

            return $this->listTables();
        }
    }

    /**
     * Get the output of the form, using the quickForm-renderer, if set.
     * In case of anything in formError, this will be prepended to the output.
     *
     * @return string The HTML to make up the form.
     */
    public function getFormOutput()
    {
        $formError = ($this->formError ?: '');
        
        $form = $this->getForm();
        if (($renderer = $this->getQuickFormRenderer()) !== null) {
            // Use the renderer to output form
            $form->accept($renderer);
            $renderMethod = $this->quickFormRendererMethod;
            return $formError . $renderer->$renderMethod();
        }

        return $formError . $form->toHtml();

    }

    /**
     * Call plugins
     *
     * Run the plugins.
     *
     * @param string $method  The method of the plugin to call
     * @param array  $options Extra options sent to the plugin. Only
     *                        'afterEdit' uses these.
     *
     * @return array Pluginname is the key, value is whatever the plugin returns
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If plugin-
     *         method is not defined
     */
    public function callPlugins($method, $options=array())
    {
        $this->loadPlugins();

        $rv = array();
        
        foreach ($this->plugins as $pluginName => $plugin) {

            if ($plugin['__policy'] == self::PLUGIN_ENABLE) {

                $instance = $plugin['__instance'];
                if ($instance instanceof DB_DataObject_FormBuilder_Frontend_Plugin) {

                    switch ($method) {
                    // This is like FormBuilder::preGenerateForm
                    case 'preGenerateForm':
                        $rv[$pluginName] = $instance->$method($this->do);
                        break;

                    // If a plugin needs to place something in the header of the page
                    case 'pageHeader':
                        $do = $this->getDataObject();
                        // NOTO: Do not use getFormBuilder() to retrieve this.
                        // It will error when using only a generic DataObject.
                        // Which is what is used, if the mode is LISTTABLES
                        $rv[$pluginName] = $instance->$method(
                            $do,
                            $this->fb
                        );
                        break;

                    // This is like FormBuilder::postGenerateForm
                    case 'postGenerateForm':
                        $rv[$pluginName] = $instance->$method(
                            $this->do,
                            $this->getFormBuilder(),
                            $this->getForm()
                        );
                        break;

                    // Called before filling in the datagrid
                    case 'beforeDataGridFill':
                        $rv[$pluginName] = $instance->$method(
                            $this->do,
                            $this->dg
                        );
                        break;

                    // Before outputting the generated datagrid-output
                    case 'beforeDataGridOutput':
                        $rv[$pluginName] = $instance->$method(
                            $this->do,
                            $this->dg
                        );
                        break;

                    // After getting the output of the datagrid.
                    case 'afterDataGridOutput':
                        $rv[$pluginName] = $instance->$method(
                            $this->do,
                            $this->dg
                        );
                        break;

                    // After an edit, before redirecting
                    case 'afterEdit':
                        $rv[$pluginName] = $instance->$method(
                            $this->do,
                            $options
                        );
                        break;

                    default:
                        include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                        throw new DB_DataObject_FormBuilder_Frontend_Exception(
                            "Plugin-hook: $method not implemented<br>"
                        );
                    }
                } else {
                    include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
                    throw new DB_DataObject_FormBuilder_Frontend_Exception(
                        "Plugin: '{$pluginName}' is not a DB_DataObject_FormBuilder_Frontend_Plugin"
                    );
                }
            } else {
                // debug: Plugin $pluginName is disabled
            }
        }

        return $rv;

    }


    /**
     * Read in a configuration from the xml-file
     *
     * Either the default settings, or for a specific table
     *
     * @param string $table The name of the table to read config for.
     *                      Null for default settings
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If table is
     * not found in configuration or if it is disabled.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function readConfig($table=null)
    {
        $xml = simplexml_import_dom($this->configDOM);

        if ($table === null) {
            // Read default config
            $xpath = "/frontend/defaults";
        } else {
            $xpath = "/frontend/tables/table[name='{$table}']";
        }
        $config = $xml->xpath($xpath);
        if (empty($config)) {
            include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                "Table $table not found"
            );
        } else {
            // Is the table disabled?
            if (isset($config[0]['enabled'])) {
                $enabled = $config[0]['enabled'];
                if ('no' == strtolower(trim($enabled)) || '0' == $enabled) {
                    include_once 'MLJ/DB/DataObject/FormBuilder/Frontend/Exception.php';
                    throw new DB_DataObject_FormBuilder_Frontend_Exception(
                        "Table $table is disabled"
                    );
                }
            }

            $this->tableName = $config[0]->name;

            if (!empty($config[0]->listName)) {
                $this->listName = $config[0]->listName;
            }

            if (empty($config[0]->displayName)) {
                $this->displayName = $this->tableName;
            } else {
                $this->displayName = $config[0]->displayName;
            }

            $this->description = $config[0]->description;

            /*
             * Number of records to display in table-list-view.
             * Can also be specified in the request-uri, and this will then
             * override setting in the configfile.
             */
            if (isset($config[0]->recordsPerPage)) {
                $this->recordsPerPage = $config[0]->recordsPerPage;
            }
            if (isset($_REQUEST['recordsPerPage'])) {
                $this->recordsPerPage = (int) $_REQUEST['recordsPerPage'];
            }

            if (isset($config[0]->confirmDelete)) {
                $this->confirmDelete = (int) $config[0]->confirmDelete['value'];
            }
            if (isset($config[0]->deleteMessage)) {
                $this->deleteMessage = (string) $config[0]->deleteMessage;
            }

            if (isset($config[0]->allowAdd)) {
                $this->allowAdd = $this->readConfigFlag(
                    (string) $config[0]->allowAdd['value'], 1, 0
                );
            }
            if (isset($config[0]->allowDelete)) {
                $this->allowDelete = $this->readConfigFlag(
                    (string)$config[0]->allowDelete['value'], 1, 0
                );
            }

            // General Stylesheet to use
            if (isset($config[0]->css)) {
                $this->css = (string) $config[0]->css['filename'];
            }

            if (isset($config[0]->javascriptPath)) {
                $this->javascriptPath = (string) $config[0]->javascriptPath['path'];
                if (substr($this->javascriptPath, -1, 1) != DIRECTORY_SEPARATOR) {
                    $this->javascriptPath .= DIRECTORY_SEPARATOR;
                }
            }

            if (isset($config[0]->cssPath)) {
                $this->cssPath = (string) $config[0]->cssPath['path'];
                if (substr($this->cssPath, -1, 1) != DIRECTORY_SEPARATOR) {
                    $this->cssPath .= DIRECTORY_SEPARATOR;
                }
            }

            // formBuilder-options
            if (isset($config[0]->fbOptions)) {
                // This is not a real fbOption - this is set directly on the form
                if (isset($config[0]->fbOptions->requiredNote)) {
                    $this->fbOptions['requiredNote']
                        = (string) $config[0]->fbOptions->requiredNote;
                }

                foreach ($config[0]->fbOptions->fbOption as $o) {
                    // If the element contains any <key>, its an array
                    $this->fbOptions[(string) $o['name']]
                        = $this->readConfigKeys($o);
                }
            }

            // columns to list
            if (isset($config[0]->listColumns->column)) {
                foreach ($config[0]->listColumns->column as $c) {
                    if (!$column = trim((string) $c)) {
                        throw new DB_DataObject_FormBuilder_Frontend_Exception(
                            "Cannot add column without a name"
                        );
                    }

                    $this->listColumns[$column] = array(
                        'displayName'     => (isset($c['displayName']) ?
                                               (string) $c['displayName'] :
                                               ucfirst($c)),
                        'type'            => (isset($c['type']) ?
                                               (string) $c['type'] :
                                               ''),
                        'allowOrderBy'    => $this->readConfigFlag(
                            (string) $c['allowOrderBy'], 1, 0, 1
                        ),
                    );

                    // Read the options for the column(-formatter).
                    if (isset($c->key)) {
                        $this->listColumns[$column]['__options']
                            = $this->readConfigKeys($c);
                    }
                    // Order data like this
                    if (isset($c['orderby'])) {
                        $this->orderby[$column]
                            = strtolower($c['orderby']) == 'desc' ? 'DESC' : 'ASC'
                        ;
                    }
                }
            }

            // Labels
            if (isset($config[0]->labels->label)) {
                foreach ($config[0]->labels->label as $c) {
                    $this->labels[(string) $c['name']] = $c;
                }
            }

            // Plugins
            if (isset($config[0]->plugins['policy'])) {
                $this->pluginPolicy = $this->readConfigFlag(
                    (string) $config[0]->plugins['policy'],
                    self::PLUGIN_ENABLE,
                    self::PLUGIN_DISABLE
                );
            }

            if (isset($config[0]->plugins->plugin)) {
                foreach ($config[0]->plugins->plugin as $p) {
                    $pluginName = (string) $p['name'];
                    if (isset($p['policy'])) {
                        $this->plugins[$pluginName]['__policy']
                            = $this->readConfigFlag(
                                (string) $p['policy'],
                                self::PLUGIN_ENABLE,
                                self::PLUGIN_DISABLE
                            );
                    } else {
                        // Use default plugin policy
                        $this->plugins[$pluginName]['__policy']
                            = $this->pluginPolicy;
                    }
                    // Where is plugin located?
                    $this->plugins[$pluginName]['__loader'] =
                        array(
                            'className' => 'DB_DataObject_FormBuilder_Frontend_Plugin_' . $pluginName,
                            'path'      => 'DB/DataObject/FormBuilder/Frontend/Plugin',
                        );
                    if (isset($p->pluginLoader)) {
                        $this->plugins[$pluginName]['__loader'] =
                            array(
                                'className' => $p->pluginLoader['className'],
                                'path'      => $p->pluginLoader['path'],
                            );
                    }

                    if (isset($p->option) || isset($p->key)) {
                        $this->plugins[$pluginName]['__options']
                            = (array) $this->readConfigKeys($p);
                    } else {
                        $this->plugins[$pluginName]['__options']
                            = array();
                    }
                }
            }

            // dataGridRenderer
            if (isset($config[0]->dataGridRenderer)) {
                $dg = $config[0]->dataGridRenderer;
                foreach ($dg->dataGridRendererAttribute as $o) {
                    $this->dataGridRendererAttributes[(string) $o['name']]
                        = (string)$o;
                }

                foreach ($dg->dataGridRendererOption as $o) {
                    $this->dataGridRendererOptions[(string) $o['name']]
                        = $this->readConfigKeys($o);
                }
            }

            // Set quickForm options
            // (and formErrorTemplate)
            if (isset($config[0]->quickForm)) {
                $c = $config[0]->quickForm;

                if (isset($c->formErrorTemplate)) {
                    $this->formErrorTemplate = (string) $c->formErrorTemplate;
                }

                if (isset($c->renderer)) {
                    if (isset($c->renderer->templates->template)) {
                        foreach ($c->renderer->templates->template as $o) {
                            $this->quickFormTemplates[(string) $o['name']]
                                = (string) $o;
                        }
                    }
                }
            }

        }

        return $this;

    }

    /**
     * Read in array of options from an xml-node
     *
     * The node should be a <option>, and if it has other <options> inside it,
     * it will recursively call itself, creating an array out of these keys.
     *
     * @param SimpleXMLElement $node    The node to read.
     * @param string           $keyName Read in an <option> or <key>-section?
     *                                  (<key> is supported for bc-reasons)
     *
     * @return array|string The read options.
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If attemting to read an
     *                                                      unsupported section
     */
    protected function readConfigKeys(SimpleXMLElement $node, $keyName="option")
    {
        $keyName = strtolower($keyName);
        if ($keyName !== 'option' && $keyName !== 'key') {
            require_once 'DB_DataObject_FormBuilder_Frontend_Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                "Cannot read configOptions of type: $keyName"
            );
        }

        // For BC with the <key>-element
        if ('option' === $keyName) {
            $keys = $this->readConfigKeys($node, 'key');
            if (!isset($node->option)) {
                return $keys;
            }
        }

        $rv = array();

        if (isset($node->{$keyName})) {
            foreach ($node as $k) {
                if (isset($k->{$keyName})) {
                    if (empty($k['name'])) {
                        $rv[] = $this->readConfigKeys($k, $keyName);
                    } else {
                        $rv[(string) $k['name']] = $this->readConfigKeys($k, $keyName);
                    }
                } else {
                    if (empty($k['name'])) {
                        $rv[] = (string) $k;
                    } else {
                        $rv[(string) $k['name']] = (string) $k;
                    }
                }
            }
        } else {
            $rv = (string) $node;
        }

        if (is_array($rv) && isset($keys) && is_array($keys)) {
            $rv = array_merge_recursive($keys, $rv);
        }

        return $rv;
    }


    /**
     * Take a flag-value an convert it into something else ...
     *
     * Will convert some strings and integers into "proper" flag values
     * which can be booleans, integers or something else.
     *
     * @param string          $flag    The flag to check.
     * @param int|bool|string $true    Value to return if $flag is one of the
     *                                 true-values.
     * @param int|bool|string $false   Value to return if $flag is not 'true'
     * @param int|bool|string $default Return this if $flag does not match
     *                                 either true or false.
     *
     * @return int|boolean If $flag is 1, '1', 'enabled' or 'yes' it will be
     *                     $true, else $false
     */
    protected function readConfigFlag(
        $flag,
        $true=true, $false=false,
        $default=null
    ) {
        // The default to return is the false value
        if (null === $default) {
            $default = $false;
        }

        switch(strtolower($flag)) {
        case '0':
        case 'no':
        case 'off':
        case 'disabled':
            return $false;
            break;

        case '1':
        case 'yes':
        case 'on':
        case 'enabled':
            return $true;
            break;
        default:
            return $default;
        }
    }


    /**
    * Load plugins and make a new instance of them
    *
    * @return DB_DataObject_FormBuilder_Frontend
    */
    protected function loadPlugins()
    {
        if (! $this->pluginsLoaded) {

            if (self::PLUGIN_ENABLE === $this->pluginPolicy) {
                // Scan plugin-directory to add all the default plugins
                // Unless they are disabled
                $path = dirname(__FILE__) . '/Frontend/Plugin/';
                $dir  = new DirectoryIterator($path);
                foreach ($dir as $file) {
                    if ($file->isFile()
                        && strtolower(substr($file->getFilename(), -4, 4)) == '.php'
                    ) {
                        $pluginName = substr($file->getFilename(), 0, -4);
                        if (!isset($this->plugins[$pluginName])) {
                            $this->plugins[$pluginName]['__loader'] = array(
                                'className' =>
                                    "DB_DataObject_FormBuilder_Frontend_Plugin_${pluginName}",
                                'path'      =>
                                    "DB/DataObject/FormBuilder/Frontend/Plugin/{$pluginName}.php",
                            );
                            $this->plugins[$pluginName]['__policy'] = $this->pluginPolicy;
                        }
                    }
                }
            }

            // Go through $this->plugins, see if they are loaded/instantiated
            foreach ($this->plugins as $name => $plugin) {
                if (self::PLUGIN_ENABLE === $plugin['__policy']) {

                    if (! class_exists($plugin['__loader']['className'])) {
                        $pluginFile = rtrim($plugin['__loader']['path'], DIRECTORY_SEPARATOR);
                        $pluginFile .= DIRECTORY_SEPARATOR . $name . '.php';
                        include_once $pluginFile;
                    }
                    $class = $plugin['__loader']['className'];
                    $this->plugins[$name]['__instance'] = new $class($this);
                    $this->plugins[$name]['__className'] = $class;

                    // Set options for the plugin.
                    $pluginOptions = $this->getPluginOptions($name);
                    $this->plugins[$name]['__instance']->setOptions($pluginOptions);

                }
            }

            $this->pluginsLoaded = true;
        }

        return $this;
    }

    /**
     * Get the options for a named plugin.
     *
     * This will return the options for a named plugin, merging options set on
     * the DB_DataObject with those set in the configfile. Those from the
     * configfile will overwrite those set on the DB_DataObject
     *
     * @param string $name Name of plugin to get options for.
     *
     * @return array
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception
     */
    protected function getPluginOptions($name)
    {
        $doOptions = array();
        if (isset($this->getDataObject()->fe_pluginOptions[$name])) {
            $doOptions = $this->getDataObject()->fe_pluginOptions[$name];
            if (!is_array($doOptions)) {
                throw new DB_DataObject_FormBuilder_Frontend_Exception(
                    "In your dataobject, fe_pluginOptions[$name]"
                    . "should contain an array"
                );
            }
        }

        return array_merge(
            $doOptions,
            (array) @$this->plugins[$name]['__options']
        );

    }

    /**
     * Add a message to the formError.
     *
     * @param string $message The error message
     * @param string $details Details about this error
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    protected function addFormError($message, $details)
    {
        $search = array(
            '{message}',
            '{details}',
        );
        $replace = array(
            $message,
            $details,
        );

        $this->formError .= str_replace(
            $search,
            $replace,
            $this->formErrorTemplate
        );

        return $this;
    }

    /**
     * Add a submit-button to the datagrid.
     *
     * @param string $name  Name of the button to add.
     * @param string $value Label to display on the button.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function addDataGridFormSubmit($name, $value)
    {
        $this->addDataGridFormField($name, $value, 'submit');

        return $this;
    }

    /**
     * Add a formfield to the datagrid.
     *
     * Plugins can use this, if they need to place form-fields inside the
     * datagrid: Formfields cannot be nested, so plugins cannot just add a
     * <form>-tag in the beforeDatagridOutput-hooks and expect it to work.
     * Instead, adding a submit-botton here, will make the datagrid encapsulated
     * in a <form> and now your plugin 'only' has to check if its own button
     * has been pressed.
     *
     * @param string $name  Name of the formfield.
     * @param string $value Value of the formfield
     * @param string $type  Type of formfield.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function addDataGridFormField($name, $value, $type='text')
    {
        $this->dataGridFormFields[] = sprintf(
            '<input type="%s" name="%s" value="%s"/>',
            $type,
            $name,
            $value
        );

        return $this;
    }

    /**
     * Return a PEAR::pager set up according to the datagrid
     *
     * @return Pager_Common
     */
    public function getPager()
    {
        if (isset($this->dg)) {
            $options = array(
                'pagerOptions' => array(
                    'fixFileName'  => false,
                    'fileName'     => $this->getScriptUrl(),
                ),
            );
            // Save original renderer
            $renderer = $this->dg->getRenderer();

            // Setup the pager-renderer, get the pager-instance from it.
            $this->dg->setRenderer(DATAGRID_RENDER_PAGER, $options);
            $pager = $this->dg->getRenderer()->getContainer();

            // Restore original renderer
            $this->dg->attachRenderer($renderer);

            return $pager;
        }
    }

    /**
     * Get the array of columns to list for the current table.
     * 
     * @return array
     */
    public function getListColumns()
    {
        return $this->listColumns;
    }

    /**
     * Get the datagrid-instance
     *
     * @return Structures_Datagrid Instance of the datagrid used,
     *                             null if not initialized yet
     */
    public function getDataGrid()
    {
        return $this->dg;
    }

    /**
     * Return the output of the datagrid
     *
     * @access public
     *
     * @return string
     */
    public function getDataGridOutput()
    {
        if (isset($this->dg)) {
            $this->initDataGridRenderer();
            return $this->dg->getOutput();
        }
    }


    /**
     * Get the form from the formBuilder-instance
     *
     * If no formBuilder is initialised yet, one will be created.
     *
     * @return HTML_QuickForm
     */
    public function getForm()
    {
        if (! ($this->form instanceof HTML_QuickForm)) {
            $this->form = $this->getFormBuilder()->getForm();
        }

        return $this->form;
    }

    /**
     * Set the url-format the Datagrid should use
     *
     * :page is replaced with the current page-number
     * :direction is replaced with the sorting-direction
     * :orderBy is replaced with the column-name to sort by
     *
     * @param string $format The format of the url.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function setDataGridUrlFormat($format)
    {
        $this->dataGridUrlFormat = $format;

        return $this;
    }

    /**
     * Set options for the DatagridRenderer
     *
     * @param array $options Array of options recognized by the renderer
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function setDataGridRendererOptions($options)
    {
        $this->dataGridRendererOptions = $options;

        return $this;
    }

    /**
     * Get the formBuilder instance
     *
     * Get the formBuilder instance to use.
     * Will initialize it if not done yet.
     * When a new instance of formBuilder is created, no submitbuttons will be
     * added. This is done later, in edit()
     *
     * @param bool $forceCreate If true, create a new instance of the
     *                          FormBuilder, even if one already exists.
     *
     * @return DB_DataObject_FormBuilder
     */
    public function getFormBuilder($forceCreate = false)
    {
        if ($forceCreate || !$this->fb instanceof DB_DataObject_FormBuilder) {

            $options = array(
                'createSubmit' => false,
            );

            // Call plugins here, before generating the form
            $this->callPlugins('preGenerateForm');

            $do = $this->getDataObject();

            $this->fb = DB_DataObject_FormBuilder::create(
                $do,
                $options
            );

            /*
             * Apply dateToDatabaseCallback to the formbuilder, if set as an
             * option in the dataobject.
             * FormBuilder does not do this
             */
            if (isset($do->fb_dateToDatabaseCallback)) {
                $this->fb->dateToDatabaseCallback
                    = $do->fb_dateToDatabaseCallback;
            }
            if (isset($do->fb_dateFromDatabaseCallback)) {
                $this->fb->dateFromDatabaseCallback
                    = $do->fb_dateFromDatabaseCallback;
            }

            // If a form exists, use it
            if ($this->form instanceof HTML_QuickForm) {
                $this->fb->useForm($this->form);
            }
        }

        return $this->fb;

    }

    /**
     * Set a renderer to use when outputting the form.
     * By default a method called toHtml() is called on the renderer, but this
     * can be changed. The called method is called without any arguments.
     *
     * @param HTML_QuickForm_Renderer $renderer QuickForm renderer to use
     * @param string                  $method   Method on the renderer to use.
     *
     * @return DB_DataObject_FormBuilder_Frontend
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception
     */
    public function setQuickFormRenderer(
        HTML_QuickForm_Renderer $renderer,
        $method = "toHtml"
    ) {
        if (!method_exists($renderer, $method)) {
            include_once 'DB/DataObject/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                "Method '$method' not present in passed renderer"
            );
        }
        $this->quickFormRenderer       = $renderer;
        $this->quickFormRendererMethod = $method;
        return $this;
    }

    /**
     * Get the renderer for the form
     *
     * @return HTML_QuickForm_Renderer
     */
    public function getQuickFormRenderer()
    {
        if (!($this->quickFormRenderer instanceof HTML_QuickForm_Renderer)) {
            include_once 'HTML/QuickForm/Renderer/Default.php';
            $this->quickFormRenderer = new HTML_QuickForm_Renderer_Default();
            $this->quickFormRendererMethod = 'toHtml';
        }

        if ($this->quickFormRenderer instanceof HTML_QuickForm_Renderer_Default) {
            foreach ($this->quickFormTemplates as $name => $template) {
                $method = 'set' . ucfirst($name) . 'Template';
                $this->quickFormRenderer->{$method}($template);
            }
        }

        return $this->quickFormRenderer;
    }

    /**
     * Get the method to use on the quickformrenderer to get the output
     *
     * @return null|string
     */
    public function getQuickFormRendererMethod()
    {
        return $this->quickFormRendererMethod;
    }

    /**
     * Setup a list of the table contents
     *
     * After this is called, use getPager() and getDataGrid() to get the table
     * list
     *
     * @param string $table Table-name
     *
     * @return DB_DataObject_FormBuilder_Frontend
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception
     */
    public function listTable($table)
    {
        $this->readConfig($table);

        if (!empty($this->listName)) {
            $table = $this->listName;
        }

        $do = $this->getDataObject();

        if ($do->__table !== $table) {
            // TODO: Warn
            $this->setDataObject(DB_DataObject::factory($table));
        }

        if ($this->do instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Filter) {
            $this->do->filter();
        }

        if (is_object($this->do) && PEAR::isError($this->do)) {
            include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                'Cannot list table: '.$table
            );
        }

        $this->setupDataGrid($this->allowAdd, $this->allowDelete);

        return $this;
    }

    /**
     * Build the datagrid for displaying content of a table
     *
     * @param bool $allowEdit   Display links to edit records?
     * @param bool $allowDelete Display links to delete records?
     *
     * @return  DB_DataObject_FormBuilder_Frontend
     *
     * @throws DB_DataObject_FormBuilder_Frontend_Exception If DataObject
     *     cannot bind() to the DataGrid
     */
    protected function setupDataGrid($allowEdit, $allowDelete)
    {
        $pk = @DB_DataObject_FormBuilder::_getPrimaryKey($this->do);

        // Fields to pull out of the dataobject.
        // If primary key is not already there, it is selected
        $fields = array_keys($this->listColumns);
        if (!in_array($pk, array_keys($this->listColumns))) {
            $fields[] = $pk;
        }

        include_once 'Structures/DataGrid.php';
        $this->dg = new Structures_DataGrid($this->recordsPerPage);
        $this->dg->setRequestPrefix($this->prefix);

        // Setup sorting of both the datagrid and the dataobject
        $this->dg->setDefaultSort($this->orderby);

        // Only call listview-plugins for listtable (and not delete)
        if ($this->getMode() == self::LISTTABLE) {
            $this->callPlugins('beforeDataGridFill');
        }


        // Bind the DataObject to the Datagrid
        $rv = $this->dg->bind(
            $this->do,
            array(
                'fields' => $fields,
                'primary_key' => array($pk),
                'return_objects' => true
            )
        );

        if ($rv instanceof PEAR_Error) {
            include_once 'DB/DataObject/FormBuilder/Frontend/Exception.php';
            throw new DB_DataObject_FormBuilder_Frontend_Exception(
                "Could not bind() DataObject to Structures_DataGrid: "
                . $rv->getMessage()
            );
        }

        // Add an 'Edit'-link to each record?
        if ($allowEdit == true) {
            $this->dg->addColumn(
                new Structures_DataGrid_Column(
                    $this->labels['edit'],
                    '__edit__',
                    null,
                    null,
                    null,
                    array($this, 'getEditLink'),
                    array('pk' => $pk)
                )
            );
        }
        // Add a 'Delete'-link to each record?
        if ($allowDelete == true) {
            $this->dg->addColumn(
                new Structures_DataGrid_Column(
                    $this->labels['delete'],
                    '__delete__',
                    null,
                    null,
                    null,
                    array($this, 'getDeleteLink'),
                    array(
                        //'database' => $this->_do->database(),
                        'pk' => $pk
                    )
                )
            );
        }

        // Add each column, in the order specified in the config-file
        foreach ($this->listColumns as $columnName => $attr) {
            $col = $this->getColumn($columnName, $attr);
            $this->dg->addColumn($col->getColumn());
        }

        return $this;
    }


    /**
     * Setup a column
     *
     * This will attempt to autoload a class with the column
     * and return this column
     *
     * @param string $columnName The name the column is to have.
     * @param array  $attr       Attributes for the column. These are passed to
     *                           the constructor of the column-object.
     *                           Additionally, the keys
     *                              'type' => which type of column
     *                              'displayName' => display on top of column
     *
     * @return DB_DataObject_FormBuilder_Frontend_Column
     */
    public function getColumn($columnName, $attr)
    {
        $colType = 'DB_DataObject_FormBuilder_Frontend_Column_';
        if (empty($attr['type'])) {
            $colType .= 'Standard';
        } else {
            $colType .= ucfirst($attr['type']);
        }
        include_once str_replace('_', DIRECTORY_SEPARATOR, $colType . '.php');

        $col = new $colType(
               $columnName,
               $attr['displayName'],
               $this,
               // WTF??
               (is_array(@$attr['__options'])
                   ? array_merge($attr, $attr['__options'])
                   : $attr
               )
        );

        return $col;

    }


    /**
     * Setup the HTML_Table-renderer for the datagrid
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    protected function initDataGridRenderer()
    {
        include_once 'HTML/Table.php';
        $hTable       = new HTML_Table();
        //$hTableHeader = $hTable->getHeader();
        $hTable->updateAttributes(
            array_merge(
                array(
                    'cellspacing' => '0',
                    'cellpadding' => '0',
                    'class'       => 'datagrid',
                ),
                $this->dataGridRendererAttributes
            )
        );

        // TODO: Shouldn't this be array_merge_recursive?
        $options = array(
            'columnAttributes' => array(
                '__edit__' => array(
                    'class' => 'edit',
                ),
                '__delete__' => array(
                    'class' => 'delete',
                ),
            ),
            'evenRowAttributes' => array(
                'class' => 'evenRow'
            ),
            'oddRowAttributes'  => array(
                'class' => 'oddRow',
            ),
            'sortIconASC'  => '&uarr;',
            'sortIconDESC' => '&darr;',
            'selfPath'     => $this->getScriptUrl(),
        );
        $renderOptions = array_merge(
            $options,
            $this->dataGridRendererOptions
        );

        $renderer = $this->dg->getRenderer();
        $renderer->setOption('extraVars', $this->getQueryParamsArray());

        if (isset($this->dataGridUrlFormat)
            && !empty($this->dataGridUrlFormat)
        ) {
            $this->dg->setUrlFormat(
                $this->dataGridUrlFormat,
                $this->prefix,
                $this->getScriptUrl()
            );
            $renderer->setUrlMapper($this->dg->_urlMapper);
        }

        $this->dg->fill($hTable, $renderOptions);

        return $this;
    }


    /**
     * Parses the QUERY_STRING and saves the contents as an array
     * in $this->params
     *
     * @access    protected
     *
     * @return    void
     */
    protected function parseQueryString()
    {
        $this->params = array();
        if (isset($_SERVER['QUERY_STRING'])
            && strlen($_SERVER['QUERY_STRING']) > 0
        ) {
            $queryParts = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($queryParts as $p) {
                $v                   = explode('=', $p);
                $this->params[$v[0]] = urldecode($v[1]);
            }
        }
    }

    /**
     * Give an array of parameters on key=>value form
     *
     * @param array $params     Parameters to add or process
     *                          (array('var_name' => value))
     * @param bool  $onlypassed True = only process the values passed in $params
     * @param array $remove     If a key from $params or $this->params is present
     *                          here, it will be removed from the returned array
     *
     * @return array
     */
    public function getQueryParamsArray(
        $params=array(),
        $onlypassed=false, $remove=array()
    ) {
        $qp = array();
        // Should the current query-string be kept?

        if (!$onlypassed && strlen($_SERVER['QUERY_STRING']) > 0) {
            $params = array_merge($this->params, $params);
        }
        foreach ($params as $k => $v) {
            // Only if not in the remove-array
            if (!in_array($k, $remove)) {
                $qp[$k] = $v;
            }
        }
        return $qp;
    }


    /**
     * Get a query-string with the parameters passed and/or set in $this->params
     *
     * @param array  $params     Parameters to include in query-string.
     *                           (array('var_name' => value))
     * @param bool   $onlypassed Only include the parameters passed in $params?
     * @param array  $remove     If a value in here, is present as a key in
     *                           $params, it will not be included in the output
     * @param string $separator  Separate url-parts with this.
     *
     * @return array
     */
    protected function getUrl(
        $params, $onlypassed=false,
        $remove=array(), $separator='&amp;'
    ) {
        $url = $this->getScriptUrl();
        $qp  = $this->getQueryParamsArray($params, $onlypassed, $remove);
        // Hack!
        // TODO: Why was this here?
        //$qp['table'] = $_REQUEST['table'];
        $queryString = http_build_query($qp, null, $separator);

        if (strlen($queryString) > 0) {
            $url .= "?" . $queryString;
        }
        return $url;
    }


    /**
     * Make a link to edit the current record
     *
     * Used as a callback for Structures_DataGrid_Column
     *
     * @param array $params Array of parameters
     *                      ($params['record'] is an instance of DB_DataObject)
     * @param array $extra  Array of extra arguments
     *
     * @return string
     */
    public function getEditLink($params, $extra)
    {
        $pk = $params['record']->$extra['pk'];

        if ($params['record'] instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Access) {

            if ($params['record']->isEditable() !== true) {
                return "[ {$this->labels['edit']} ]";
            }
        }

        return '[&nbsp;<a href="'.
        $this->getUrl(array('record' => $pk)).
            '">' . $this->labels['edit'] . '</a>&nbsp;]';
    }


    /**
     * Make a link to delete the current record
     *
     * Used as a callback for Structures_DataGrid_Column
     *
     * @param array $params Array of parameter.
     *                ($params['record'] is an instance of DB_DataObject)
     * @param array $extra  Array of extra arguments.
     *
     * @return string
     */
    public function getDeleteLink($params, $extra)
    {
        $pk = $params['record']->$extra['pk'];

        if ($params['record'] instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Access) {
            if ($params['record']->isDeleteable() !== true) {
                return "[&nbsp;{$this->labels['delete']}&nbsp;]";
            }
        }
        return '[&nbsp;<a href="'.
        $this->getUrl(array('record' => $pk, 'delete' => 'delete')).
            '">' . $this->labels['delete'] . '</a>&nbsp;]';
    }


    /**
     * Get the url for the page that will list the contents of the table.
     *
     * @return string
     */
    public function getListTableUrl()
    {
        return $this->getUrl(
            array(), false, array('addRecord', 'record', 'delete')
        );
    }


    /**
     * Get the url for the page that lists all the tables that can be edited.
     *
     * @return string
     */
    public function getListTablesUrl()
    {
        return $this->getUrl(array(), true);
    }


    /**
     * Add an external javascript to the page
     *
     * Plugins/columns can call this to add external javascripts that they need
     * Then, in your controller/page you use getJavascripts() to get a list
     * of the javascripts the page needs to function properly
     *
     * @param string $scriptName    path/name of script to use
     * @param bool   $useScriptPath If true, the internal javascript-path is
     *                              prepended to $scriptName
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function addJavascript($scriptName, $useScriptPath = true)
    {
        $script = ($useScriptPath === true
            ? $this->javascriptPath . $scriptName
            : $scriptName
        );

        if (!in_array($script, $this->javascripts)) {
            $this->javascripts[] = $script;
        }
        return $this;
    }


    /**
     * Get the javascripts needed for the page to function
     *
     * @return array    Each row is the path to an external javascript
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }


    /**
     * Add an external stylesheet that is needed
     *
     * Mostly used by plugins, when they need to have a specified stylesheet
     * added to the page.
     *
     * @param string $cssName    Filename of the stylesheet
     * @param bool   $useCssPath Prepend the css-path set on this instance?
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function addCss($cssName, $useCssPath = true)
    {
        $css = ($useCssPath === true ? $this->cssPath . $cssName : $cssName);

        if (!in_array($css, $this->externalCss)) {
            $this->externalCss[] = $css;
        }
        return $this;
    }

    /**
     * Get the list of external stylesheets needed for the page
     *
     * @return array
     */
    public function getCss()
    {
        return $this->externalCss;
    }


    /**
     * Add to the append-property
     * Column-plugins can add html/javascript/whatever to this.
     *
     * @param string $string Text to append. Will be added to append-property
     *
     * @return DB_DataObject_FormBuilder_Frontend
     */
    public function addAppend($string)
    {
        $this->append .= $string;
        return $this;
    }


}
