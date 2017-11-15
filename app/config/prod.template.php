<?php
/**
 * Template file for Doctrine configuration
 *
 * Rename it to prod.php and give appropriate value to the fields
*/

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'jitt',
    'user'     => 'root',
    'password' => '',
);
