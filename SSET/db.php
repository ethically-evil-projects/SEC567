<?php
// DB Configuration for the tracking script. Seperate from admin DB functions.

if (runmode == 'live') {
  // LIVE
  $DBHost = "localhost";
  $DBPort = "3306";
  $DBUser = "root";
  $DBPass = "password";
  $DBName = "social_engineering";
} else {
  // DEV
  $DBHost = $_SERVER['SSET_RDS_HOSTNAME'];
  $DBPort = $_SERVER['SSET_RDS_PORT'];
  $DBUser = $_SERVER['SSET_RDS_USERNAME'];
  $DBPass = $_SERVER['SSET_RDS_PASSWORD'];
  $DBName = $_SERVER['SSET_RDS_DBNAME'];
}

try {
	$pdo=new PDO('mysql:dbname='.$DBName.';host='.$DBHost,$DBUser, $DBPass);
} catch (PDOException $e) {
	die("Error in creating the database connection in dbcreds " . $e->getMessage());
	exit;
}
?>
