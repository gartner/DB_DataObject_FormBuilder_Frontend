<?php
/**
 * Hooks for Scripts, both inline and linked from files.
 *
 * If the dataobject that the frontend uses, implements this interface,
 * then the functions here will be called according to comments below.
 *
 * PHP Version 5
 *
 * @category DB
 * @package  DB_DataObject_FormBuilder_Frontend
 * @author   Mads Lie Jensen <mads@gartneriet.dk>
 * @license  http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version  Release: SVN: $Id: Delete.php 184 2009-05-21 06:52:44Z mads $
 * @link     http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Hooks_Scripts
{
    /**
     * Get the javascript-files to include in the <head>-section of the page
     *
     * @return array Array of script-files
     */
    function getHeadScriptFiles();

    /**
     * Javascript to place inline in the page header.
     *
     * @return array Each element is a script to place in the header.
     */
    function getInlineHeadScripts();

}
