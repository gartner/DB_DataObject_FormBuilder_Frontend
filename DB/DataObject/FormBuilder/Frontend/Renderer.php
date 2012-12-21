<?php
/**
 * A base renderer for the frontend
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Renderer.php 466 2012-11-21 08:11:57Z mlj $
 * @link      http://www.gartneriet.dk/
 */

/**
 * Take an instance of DB_DataObject_FormBuilder_Frontend, and 'run' it
 * and output raw html.
 * This is more of a helper-thing than a real renderer: This displays a basic
 * html with the frontend. (list of tables, list of records in a table,
 * an editable form).
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Renderer.php 466 2012-11-21 08:11:57Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Renderer
{
    /**
     * The Frontend
     *
     * @var DB_DataObject_FormBuilder_Frontend
     */
    protected $do;

    /**
     * Constructor
     *
     * @param DB_DataObject_FormBuilder_Frontend $do DataObject to use.
     */
    public function __construct(DB_DataObject_FormBuilder_Frontend $do)
    {
        $this->do = $do;

    }

    /**
     * "run" the thing.
     *
     * Process data and display either a list of tables, the contents of table,
     * a delete-form or an edit-form.
     *
     * @return void
     */
    public function run()
    {
        $output = $this->do->process();
        $this->pageStart();

        echo $this->do->getToolbar();

        echo $output;

        $this->pageEnd();
    }

    /**
     * Output start of page.
     *
     * @return void
     */
    public function pageStart()
    {
        echo '<?xml version="1.0" encoding="utf-8"?>';
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="da" lang="da">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="author" content="Mads Lie Jensen" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->do->css; ?>"
            title="Layout1" />
        <title><?php echo $this->do->displayName; ?></title>
        <?php
        echo implode("\n", $this->do->callPlugins('pageHeader'));
        foreach ($this->do->getJavascripts() as $script) {
            echo '<script type="text/javascript" src="' . $script . '"></script>' . "\n";
        }
        // Javascript added by the dataObject
        if ($this->do->getDataObject() instanceof DB_DataObject_FormBuilder_Frontend_Hooks_Scripts) {
            $scripts = $this->do->getDataObject()->getHeadScriptFiles();
            foreach ($scripts as $script) {
                echo '<script type="text/javascript" src="' . $script . '"></script>' . "\n";
            }

            $inline = $this->do->getDataObject()->getInlineHeadScripts();
            if (count($inline) > 0) {
                echo '<script type="text/javascript">';
                foreach ($inline as $script) {
                    echo $script . "\n\n";
                }
                echo '</script>';
            }
        }

        foreach ($this->do->getCss() as $css) {
            echo '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
        }
        ?>
        </head>
        <body>
        <h1><?php echo htmlentities($this->do->displayName); ?></h1>
        <p><?php echo htmlentities($this->do->description); ?></p>
        <?php
    }

    /**
     * Output end of page.
     *
     * @return void
     */
    public function pageEnd()
    {
        ?>
        <p><?php
        if ($this->do->getMode() == DB_DataObject_FormBuilder_Frontend::EDIT
            || $this->do->getMode() == DB_DataObject_FormBuilder_Frontend::DELETE
            || $this->do->getMode() == DB_DataObject_FormBuilder_Frontend::LISTTABLE
        ) {
                ?> [ <a
                href="<?php echo $this->do->getListTablesUrl(); ?>">Show tables</a>
            ] <?php
        }//if (strlen($this->tableName) > 0) {
        if ($this->do->getMode() == DB_DataObject_FormBuilder_Frontend::EDIT
            || $this->do->getMode() == DB_DataObject_FormBuilder_Frontend::DELETE
        ) { ?>
            [ <a
            href="<?php echo $this->do->getListTableUrl(); ?>">Show table:
            <?php echo $this->do->displayName; ?></a> ] <?php
        }
        ?></p>
        </body>
        </html>
        <?php
    }


}
