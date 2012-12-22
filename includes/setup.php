<?php
/**
 * This file is used to setup the project. It includes the settings for paths,
 * database-setup and ....
 */
require_once 'vendor/autoload.php';

// Passwords for the database is here
require_once 'dbpw.php';

define('__ROOT__', realpath(dirname(__FILE__) . '/../'));

/*
 * Composer does not install PEAR::Pager correct, thus Pager is placed in the
 * /includes/-folder also. This folder is set in the include-path from within the
 * apache-vhost. But, Composers autoloader will place the already created include-
 * paths AFTER its own packages. So, we add it again at the front.
 */
set_include_path(__ROOT__ . '/includes' . PATH_SEPARATOR . __ROOT__ . PATH_SEPARATOR . get_include_path());

/*
 * Setup dataobjects
 */
$config = parse_ini_file(__ROOT__ . '/config/database.ini');

foreach($db as $database => $settings) {
    $ds = sprintf('%s://%s:%s@%s/%s',
        $settings['type'],
        $settings['user'],
        $settings['password'],
        (@$settings['host'] ?: 'localhost'),
        $settings['database']
    );
    $config['database_' . $database] = $ds;
}
$config = str_replace('%{basepath}', __ROOT__, $config);

$options = &PEAR::getStaticProperty('DB_DataObject','options');
$options = $config;

unset($options);
