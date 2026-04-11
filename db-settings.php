<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Development Database Information
$db_host = $_ENV['DB_HOST']; //Host name of rds database
$db_name = $_ENV['DB_NAME']; //Name of Database
$db_user = $_ENV['DB_USER']; //Name of database user
$db_pass = $_ENV['DB_PASS']; //Password for database user
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
