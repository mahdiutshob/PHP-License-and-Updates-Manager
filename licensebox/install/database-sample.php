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

// {[DB_HOST]} : MySQL hostname
// {[DB_NAME]} : The name of the database for LicenseBox
// {[DB_USER]} : MySQL database username
// {[DB_PASS]} : MySQL database password
$db['default'] = array(
	'dsn'	=> 'mysql:host={[DB_HOST]};dbname={[DB_NAME]}',
	'hostname' => '{[DB_HOST]}',
	'username' => '{[DB_USER]}',
	'password' => '{[DB_PASS]}',
	'database' => '{[DB_NAME]}',
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