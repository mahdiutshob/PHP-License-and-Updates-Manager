<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Database configuration for LicenseBox
 * 
 * LicenseBox v1.2
 *
 * teamcodemonks@gmail.com
 * https://www.techdynamics.org
 * 
 */

$active_group = 'default';
$query_builder = TRUE;

// localhost : MySQL hostname
// licensebox : The name of the database for LicenseBox
// root : MySQL database username
// root_pass : MySQL database password
$db['default'] = array(
    'dsn'   => 'mysql:host=localhost;dbname=licensebox',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'root_pass',
    'database' => 'licensebox',
    'dbdriver' => 'pdo',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/* All done! */