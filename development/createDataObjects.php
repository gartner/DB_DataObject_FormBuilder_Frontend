#!/usr/bin/php
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 08-12-12
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */
define('DB_DATAOBJECT_NO_OVERLOAD',1);

$includePath = realpath(dirname(__FILE__) . '/../includes/');
set_include_path($includePath . PATH_SEPARATOR . get_include_path());

require_once 'setup.php';

// Do not timeout
set_time_limit(0);

// use debug level from file if set..
DB_DataObject::debugLevel(isset($options['debug']) ? $options['debug'] : 1);

$generator = new DB_DataObject_Generator();
$generator->start();

