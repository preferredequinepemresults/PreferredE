<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env
if (class_exists(Dotenv::class)) {
	$dotenv = Dotenv::createImmutable(__DIR__);
	$dotenv->safeLoad();
} else {
	$env_path = __DIR__ . '/.env';
	if (is_readable($env_path)) {
		foreach (file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
			$line = trim($line);
			if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
				continue;
			}

			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value, " \t\n\r\0\x0B\"'");
			if (getenv($key) === false) {
				putenv($key . '=' . $value);
				$_ENV[$key] = $value;
			}
		}
	}
}

//Development Database Information
$db_host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '64.176.210.96'; //Host name of database
$db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'preferred_equine_staging'; //Name of Database
$db_user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: ''; //Name of database user
$db_pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: ''; //Password for database user
$db_table_prefix = ""; // if the table prefix exists use this variable as a global


//following variable declaration
global $errors;
global $successes;

$errors = array();
$successes = array();

// 1. Create a database connection
//$connection = mysql_connect(DB_HOST,DB_USER,DB_PSWD);
// $connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// if (!$connection) {
// die("Database connection failed: " . mysqli_connect_error());
// }

/* Create a new mysqli object with database connection parameters */

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
global $mysqli;

if (mysqli_connect_errno()) {
	//display the reason for mysql connection error.
	echo "Connection Failed1: " . mysqli_connect_errno();
	exit();
} else {
	//echo "Connection Successful";
}
