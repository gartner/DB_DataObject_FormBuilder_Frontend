<?php
/**
 * If a DB_DataObject implements this, then options from the config-file
 * will be passed to the dataobject when using a method from this DB_DataObject
 * as a callback in the configfile
 * (type="dbdoCallback")
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <mads@gartneriet.dk>
 * @copyright 2006-2009 Mads Lie Jensen
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL 2.1
 * @version   SVN: $Id: Dbdocallback.php 311 2010-04-23 05:56:44Z mlj $
 * @link      http://www.gartneriet.dk/
 */
interface DB_DataObject_FormBuilder_Frontend_Column_OptionsInterface
{

    /**
     * The ooptions from the configfile will be passed to your DB_DataObject
     * using this method.
     *
     * @param array $options Array of options, as present in the configfile
     *
     * @return self
     */
    public function setColumnCallbackOptions(array $options);
}

