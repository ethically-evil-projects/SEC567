<?php
$csrfCheck = false;
require("shared/_init.php");
require("functions.php");

if (isset($_GET['secure_id'])){
	// Die if incorrect and redirect (handled in function).
	$code_id = checkCode($_GET['secure_id']);

	// Structure the insert of data using if statements
	// Is username being sent?
	$username = handleGET('username');
	$ip_address = handleGET('ip');
	registerClick($code_id,$username,$ip_address);
} else {
	die("No secure_id was specified. Terminating.");
	exit;
}
?>
