<?php
/**
 * This plugin will remove the name and target-attributes from the
 * generated form, making it Xhtml-compatible.
 *
 * PHP version 5
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Xhtml.php 462 2012-11-17 20:38:11Z mlj $
 * @link      http://www.gartneriet.dk/
 */

require_once 'DB/DataObject/FormBuilder/Frontend/Plugin.php';

/**
 * This plugin will remove the name and target-attributes from the
 * generated form, making it Xhtml-compatible.
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Xhtml.php 462 2012-11-17 20:38:11Z mlj $
 * @link     http://www.gartneriet.dk/
 */
class DB_DataObject_FormBuilder_Frontend_Plugin_Xhtml
   extends DB_DataObject_FormBuilder_Frontend_Plugin
{
    /**
     * This is called by the Frontend after generating the form.
     *
     * @param DB_DataObject             $do   DataObject to work on.
     * @param DB_DataObject_FormBuilder $fb   The formBuilder generating the form
     * @param HTML_QuickForm            $form The instance of QuickForm holding the
     *                                        form.
     *
     * @return bool|void
     *
     * @see DB/DataObject/FormBuilder/Frontend/Plugin.php#postGenerateForm()
     */
    public function postGenerateForm(DB_DataObject $do,
        DB_DataObject_FormBuilder $fb,
        HTML_QuickForm $form
    ) {

        // Fjern name-attribut fra <form>-tag for at gøre det xhtml-kompatibelt
        $form->removeAttribute('name');
        $form->removeAttribute('target');
    }

}
