<?php
include("admin-functions.php");

if (isset($_GET['csrf'])){
  session_destroy();
  header("Location: ./login");
  die("Redirect to dashboard");
  exit;
}

//Do not allow logout without csrf token
header("Location: .");
die("Redirect to dashboard");
exit;
?>
