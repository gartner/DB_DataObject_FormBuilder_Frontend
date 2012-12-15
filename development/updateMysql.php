#!/usr/bin/php
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mads
 * Date: 09-12-12
 * Time: 08:19
 * To change this template use File | Settings | File Templates.
 */

if ($argc < 2) {
    echo "\nError: You need to pass sql-script to run as first argument\n\n";
    return 10;
}

$file = $argv[1];
$fileLong = realpath($file);

if (!file_exists($file)) {
    echo "\nError: File $file not found\n\n";
    return 10;
}

require_once '../includes/setup.php';

foreach ($db as $database) {
    if ($database['type'] == 'mysql') {
        // mysql --user=user_name --password=your_password db_name
        $cmd = sprintf('mysql --user=%s --password=%s %s < %s',
            escapeshellarg($database['user']),
            escapeshellarg($database['password']),
            escapeshellarg($database['database']),
            escapeshellarg($fileLong)
        );

        echo "\nRunning cmd: $cmd\n\n";
        $lastLine = system($cmd, $return);

        break;
    }
}