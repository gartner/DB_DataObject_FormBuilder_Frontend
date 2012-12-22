#!/usr/bin/php
<?php
/**
 * PEAR package v2 generator for DB_Dataobject_FormBuilder_Frontend
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2003-2012 The PHP Group
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  DB
 * @package   DB_DataObject_FormBuilder_Frontend
 * @author    Mads Lie Jensen <php@gartneriet.dk>
 * @copyright 2003-2012 The PHP Group
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/DB_DataObject_FormBuilder_Frontend
 */

// My PEAR setup is somehow broken
//require_once 'PEAR/PEAR/Config.php';

var_dump (getcwd());
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$desc = <<<DOC
Build frontends with just a few lines of code and a configfile.
DOC;

$version = '0.5.0';
$apiver  = '0.5.0';
$state   = 'alpha';

$notes = <<<EOT
-
EOT;

$package = PEAR_PackageFileManager2::importOptions(
    'package.xml',
    array(
        //'filelistgenerator' => 'cvs',
        'changelogoldtonew' => false,
        'simpleoutput'	=> true,
        'packagefile'       => 'package.xml',
        'include' => array(
            'DB/',
            'example/dtd/DB_DataObject_FormBuilder_Frontend-v1.0.dtd',
        ),
        'ignore' => array(
            'includes/',
            'DB/DataObject/FormBuilder/Frontend/Plugin/DateToDateselect.php',
            'DB/DataObject/FormBuilder/Frontend/Plugin/Validate.php',
        ),
        'exceptions' => array(
            'example/dtd/DB_DataObject_FormBuilder_Frontend-v1.0.dtd' => 'data',
        ),
        'packagedirectory'  => '.'));

$package->clearDeps();

$package->setPackage('DB_DataObject_FormBuilder_Frontend');
$package->setPackageType('php');
$package->setSummary('Builds frontends for databases, using not much more than a configfile.');
$package->setDescription($desc);
$package->setChannel('pear.php.net');
$package->setLicense('LGPL', 'http://www.gnu.org/licenses/lgpl.html');
$package->setAPIVersion($apiver);
$package->setAPIStability($state);
$package->setReleaseVersion($version);
$package->setReleaseStability($state);
$package->setNotes($notes);
$package->setPhpDep('5.2.0');
$package->setPearinstallerDep('1.9.4');

$package->addPackageDepWithChannel('required', 'DB_DataObject', 'pear.php.net', '1.9');
$package->addPackageDepWithChannel('required', 'HTML_QuickForm', 'pear.php.net', '3.2.13');
$package->addPackageDepWithChannel('required', 'DB_DataObject_FormBuilder', 'pear.php.net', '1.0.2');
$package->addPackageDepWithChannel('required', 'Structures_DataGrid', 'pear.php.net', '0.9.3');
$package->addPackageDepWithChannel('required', 'Structures_DataGrid_DataSource_DataObject', 'pear.php.net', '0.2.2dev1');
$package->addPackageDepWithChannel('required', 'Structures_DataGrid_DataSource_Array', 'pear.php.net', '0.2.0dev1');
$package->addPackageDepWithChannel('required', 'Structures_DataGrid_Renderer_Pager', 'pear.php.net', '0.1.3');
$package->addPackageDepWithChannel('required', 'Structures_DataGrid_Renderer_HTMLTable', 'pear.php.net', '0.1.6');
$package->addPackageDepWithChannel('required', 'Pager', 'pear.php.net', '2.4.8');

//$package->addIgnore(array('package.php', 'package2.php', 'package.xml', 'package2.xml'));
//$package->addReplacement('DTD.php', 'package-info', '@package_version@', 'version');
//$package->addReplacement('DTD/XmlValidator.php', 'package-info', '@package_version@', 'version');

$package->addGlobalReplacement('package-info', '@package-version@', 'version');

$package->generateContents();

if ($_SERVER['argv'][1] == 'make') {
    $result = $package->writePackageFile();
} else {
    $result = $package->debugPackageFile();
}

if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}