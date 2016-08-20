<?php
// Administrative view functions
require("../shared/_init.php");
require_once("../config.php");

// ===========
// ADMIN COUNT
// ===========
function adminCount(){
	require("../db.php");
	$sql="SELECT COUNT(id) FROM admins";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	if ($stmt->execute()){
		$return_var = $stmt->fetchColumn();
		return $return_var;
	} else {
		die("Error executing count of total clicks on DB.");
		exit;
	}
}

// =========
// GET ADMIN
// =========
function getAdmin($username=null) {
  require("../db.php");
  // Get admin by username
  $sql="SELECT id, username, password
        FROM admins
        WHERE username = :username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":username" => $username
  ));
  $data = $stmt->fetch();
  if (isset($data['id'])) {
    return $data;
  } else {
    return false;
  }
}

// ==============
// DO ADMIN LOGIN
// ==============
function doAdminLogin($username,$password) {
  require("../db.php");
  // Get admin and IP info
  $admin = getAdmin($username);
  $attempts = getFailedAttempts($username);
  if (isAdminLocked($attempts)){
    // Too many attempts
    return "Too many attempts please try again in 10 Minutes.";
  }
  // If a successful login
  if ($admin && password_verify($password, $admin['password'])) {
    // Generate new ID to avoid session cache poisoning
    session_regenerate_id(true);
    $ip = getUserIP();

    // Mark user as having logged in by setting the date & time plus IP address
    $sql="UPDATE admins SET last_login_datetime = NOW(), last_login_ip = :ip WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ":id" => $admin['id'],
      ":ip" => $ip
    ));
    dropAttempts($username);
    $_SESSION['admin_id'] = $admin['id'];

    return true;
  // Return false response
  } else {
    failedLoginAttempt($username, $attempts);
    return "Sorry, that was not the correct username and password.";
  }
}

// ====================
// CHECK LOGIN ATTEMPTS
// ====================
function isAdminLocked($attempts){
  $attemptLimit = 5;
  // in minutes
  $lockout_time = 10;

  if(!$attempts){
    return false;
  }

  $start_date = new DateTime();
  $since_start = $start_date->diff(new DateTime($attempts['first_failed_login_time']));
  if(($attempts['login_attempts'] >= $attemptLimit)
    && ( $since_start->i < $lockout_time)) {
    return true;
  }

  return false;
}

// ==================
// FAILED LOGIN COUNT
// ==================
function getFailedAttempts($username){
  require("../db.php");
  $sql="SELECT login_attempts, first_failed_login_time
  FROM login_attempts WHERE username = :username";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
    ":username" => $username
  ));

	if ($stmt->execute()){
		$data = $stmt->fetch();
		return $data;
	} else {
		die("Error executing count of total clicks on DB.");
		exit;
	}
}

// ====================
// FAILED LOGIN ATTEMPT
// ====================
function failedLoginAttempt($username, $attempts){
  require("../db.php");
  $lockReset = unlockCheck($username, $attempts);
  if ($attempts && !$lockReset) {
    $sql="UPDATE login_attempts SET login_attempts = login_attempts + 1 WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ":username" => $username,
    ));
    if (!$stmt) {
      die("An error occured updating login attempts.");
      exit;
    }
  } else {
    $sql = "INSERT INTO login_attempts (username, login_attempts, first_failed_login_time)
    VALUES (:username, 1, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ":username" => $username,
    ));
    if (!$stmt) {
      die("An error occured updating login attempts.");
      exit;
    }
  }

  return true;
}

// ============
// UNLOCK CHECK
// ============
// Check if we need to reset lockout time, drop row if so
function unlockCheck($username, $attempts){
  //in minutes
  $lockout_time = 10;
  $start_date = new DateTime();
  $since_start = $start_date->diff(new DateTime($attempts['first_failed_login_time']));
  if ($attempts && $since_start->i > $lockout_time) {
    dropAttempts($username);
    return true;
  }

  return false;
}

// =============
// DROP ATTEMPTS
// =============
function dropAttempts($username){
  require("../db.php");
  $sql = "DELETE from login_attempts
    where username = :username limit 1";
  $stmt = $pdo->prepare($sql);
  if ($stmt->execute(array(":username" => $username))) {
    return true;
  } else {
    die("An error occured dropping attempts");
    exit;
  }
}

// ===========
// ADMIN CHECK
// ===========
function adminCheck(){
  if (!isset($_SESSION['admin_id'])){
    header("Location: ./login");
    die("Redirect to login");
    exit;
  }
}

// ==============
// ADMIN REGISTER
// ==============
function registerAdmin($username, $pwd, $pwd2){
  $errors = checkPassword($pwd,$pwd2);
  if (getAdmin($username) !== false ) {
    $errors[] = "Username already exists";
  }
  if(count($errors) !== 0) {
    return $errors;
  }
  $hash = password_hash($pwd, PASSWORD_BCRYPT);
  if ($hash === false) {
    $errors[] = "Unknown error please try a different password";
    return $errors;
  }
  addAdmin($username, $hash);
  return $errors;
}



// =========
// ADD ADMIN
// =========
function addAdmin($username, $pwd){
  require("../db.php");
  $sql = "INSERT INTO admins (username, password, last_login_datetime, last_login_ip)
  VALUES (:username, :password, NOW(), :last_login_ip)";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':username', $username, PDO::PARAM_STR);
  $stmt->bindParam(':password', $pwd , PDO::PARAM_STR);
  $stmt->bindParam(':last_login_ip', getUserIP(), PDO::PARAM_STR);
  if ($stmt->execute()) {
    return true;
  } else {
    die("An error occured inserting the new code.");
    exit;
  }
}

// ==============
// CHECK PASSWORD
// ==============
function checkPassword($pwd, $pwd2) {
    $errors = [];
    if ($pwd !== $pwd2) {
      $errors[] = "Passwords do not match";
    }

    if (strlen($pwd) < 10) {
        $errors[] = "Password must be at least 10 characters!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-z]+#", $pwd)) {
        $errors[] = "Password must include at least one lowercase letter!";
    }

    if (!preg_match("#[A-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one uppercase letter!";
    }

    if (!preg_match("#[^a-zA-Z\d]#",$pwd)){
      $errors[] = "Password must include a special character";
    }

    return $errors;
}

// ===========
// LOOKUP CODE
// ===========
function lookupCode($group){
	require("../db.php");
	$sql = "SELECT code FROM codes WHERE group_name = :group";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':group', $group, PDO::PARAM_STR);
	$stmt->execute();
	$return_var = $stmt->fetchColumn();

	if ($return_var == ''){
		// Code did not match
		invalidRequest();
	}
	return $return_var;
}

// ===========
// CREATE CODE
// ===========
function createCode($group, $redirecturl){
	// Check if the group_name already exists
	$result_group_check = checkifGroupExists($group);
	if ($result_group_check == '1') {
		return "group_exists";
	}
	// Generate a new code and check it does not already exist.
  	do {$proposed_code = generateNewCode();}
	while (checkifCodeExists($proposed_code) == '1');

  	// Now do insert
	require("../db.php");
	$sql = "INSERT INTO codes (code, group_name, redirect_url) VALUES (:code, :group, :redirect_url)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $proposed_code, PDO::PARAM_STR);
  $stmt->bindParam(':redirect_url', $redirecturl, PDO::PARAM_STR);
	$stmt->bindParam(':group', $group, PDO::PARAM_STR);
	if ($stmt->execute()) {
		return $proposed_code;
	} else {
		die("An error occured inserting the new code.");
		exit;
	}

}

// ====================
// CHECK IF CODE EXISTS
// ====================
function checkifCodeExists($code){
	// If code exists return 1, else return 0.
	require("../db.php");
	$sql = "SELECT code FROM codes WHERE code = :code";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $proposed_code, PDO::PARAM_STR);
	$stmt->execute();
	$obj = $stmt->fetchObject();
	$count = $stmt->rowCount();
	return $count == '0'
	? 0
	: 1;
}

// =====================
// CHECK IF GROUP EXISTS
// =====================
function checkifGroupExists($group){
	// If group exists return 1, else return 0.
	require("../db.php");
	$sql = "SELECT group_name FROM codes WHERE group_name = :group";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':group', $group, PDO::PARAM_STR);
	$stmt->execute();
	$obj = $stmt->fetchObject();
  $count = $stmt->rowCount();
  return $count == '0'
  ? 0
  : 1;
}

// =================
// GENERATE NEW CODE
// =================
function generateNewCode(){
	// Simple scheme for short codes, could use a hash or just augment numerically. Your call!
	return uniqid();
}


// =============
// RENDER CLICKS
// =============
function renderClicks($order){
	require("../db.php");
	if ($order == ''){
		$sql="SELECT codes.group_name,clicks.username,clicks.ip_address,browser_name,browser_version,platform,clicks.time,extra from clicks RIGHT JOIN codes on clicks.codeid = codes.codeid";
	} else {
		$sql="SELECT codes.group_name,clicks.username,clicks.ip_address,browser_name,browser_version,platform,clicks.time,extra from clicks INNER JOIN codes on clicks.codeid = codes.codeid ORDER BY ";
		if ($order == 'username') {
			$sql.="clicks.username ASC";
		} elseif ($order == 'ip_address') {
			$sql.="clicks.ip_address ASC";
		} elseif ($order == 'group') {
			$sql.="codes.group_name ASC";
		} elseif ($order == 'time') {
			$sql.="clicks.time DESC";
		} else {
			die("Invalid order value requested. Terminating.");
			exit;
		}
	}

	$stmt = $pdo->prepare($sql);
	$results = array();
	if ($stmt->execute()){
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}

		if (count($results) == 0){
			echo '<div class="message message-error">There are no clicks yet, go snare some targets!</div>';
			return;
		}

		// Walk to build the list
		foreach($results as $key => $result) {
      $result['logo_content'] = buildLogoContent($result);
      $extra = json_decode($result['extra'], true);
      $result['payloadType'] = getPayloadType($extra);
      $result['extra'] = removeDuplicateInfo($extra);
      $result['key'] = $key;
      renderTemplate('templates/dashboard/tablerow.php', $result);
    }
  }
}

// ===========
// BUILD LOGO
// ===========
function buildLogoContent($result){
  $browser_name = $result['browser_name'];
  $browser_logo = strtolower(str_replace(" ","-",$browser_name)).".png";
  $browser_version = $result['browser_version'];
  $platform = $result['platform'];
  $platform_logo = strtolower($result['platform']).".png";
  $viewObjects['group_name'] = $result['group_name'];

  return $browser_name != "Unknown" && $platform != "Unknown"
  ? '<img src="assets/images/'.$browser_logo.'" alt="'.$browser_name.'"><img src="assets/images/'.$platform_logo.'" alt="'.$platform.'"><br>'.$browser_version
  : '';
}

// ===========
// REMOVE DUPS
// ===========
function removeDuplicateInfo($queryArray) {
  unset($queryArray['secure_id']);
  unset($queryArray['payload']);
  unset($queryArray['ip']);
  unset($queryArray['username']);
  return $queryArray;
}

// ================
// GET PAYLOAD TYPE
// ================
function getPayloadType($request){
  $type = "Link";
  if (isset($request['payload'])) {
    $type = $request['payload'];
  }
  return $type;
}

// ================
// RENDER CODE LIST
// ================
function renderCodeList(){
	require("../db.php");
	$sql="SELECT group_name, code FROM codes ORDER BY codeid DESC";
	$stmt = $pdo->prepare($sql);
	$results = array();
	if ($stmt->execute()){
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
		}
		if (count($results) == 0){
			return;
		}
		foreach ($results as $result) {
      renderTemplate('templates/generate/tablerow.php', $result);
		}
	} else {
		die("Error executing request for group_name from database.");
		exit;
	}
}

// ================
// GET TOTAL CLICKS
// ================
function getTotalClicks(){
	require("../db.php");
	$sql="SELECT COUNT(codeid) FROM clicks";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':group', $group, PDO::PARAM_STR);
	$stmt->execute();

	if ($stmt->execute()){
		$return_var = $stmt->fetchColumn();
		return $return_var;
	} else {
		die("Error executing count of total clicks on DB.");
		exit;
	}
}

// ===================
// GET TOTAL FOR GROUP
// ===================
function getTotalForGroup($group){
	require("../db.php");
  $sql="SELECT COUNT(clicks.codeid) FROM clicks
        INNER JOIN codes ON clicks.codeid = codes.codeid
        WHERE codes.group_name = :group_name";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':group_name', $group, PDO::PARAM_STR);
  $stmt->execute();

	if ($stmt->execute()){
		$return_var = $stmt->fetchColumn();
		return $return_var;
	} else {
		die("Error executing count of total clicks on DB. Does group exist?");
		exit;
	}
}
// ==================
// BUILD PAYLOAD ZIP
// ==================
function createPayloadZip(){
  $archiveName = "/tmp/" . uniqid()."-payload.zip";
  $zip = new ZipArchive();
  if ($zip->open($archiveName, ZipArchive::CREATE)!==TRUE) {
      die("cannot open <$archiveName>\n");
      exit;
  }
  $baseDir = "./assets/payloads/";
  $type = preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($_GET['payload']));
  switch ($type) {
    case 'batch':
      addPayload($zip, $type, "payload.bat");
      addReadmeFile($zip, $type);
      break;
    case 'hta':
      $htaPayload = str_replace("#APPLICATION_TITLE#", $_GET['htaAppname'], replaceBaseUrl($baseDir . "/hta/payload.hta"));
      $zip->addFromString("payload.hta", $htaPayload);
      addReadmeFile($zip, $type);
      break;
    case 'java':
      addPayload($zip, $type, "applet.java");
      addPayload($zip, $type, "applet.html");
      addReadmeFile($zip, $type);
      break;
    case 'pdf':
      $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir . "pdf"),
        RecursiveIteratorIterator::LEAVES_ONLY
      );
      foreach ($files as $name => $file){
        if (!$file->isDir() && $file->getFilename() != 'pdf_gen.ps1'){
          // Get real and relative path for current file
          $filePath = $file->getPathName();
          $relativePath = str_replace("./assets/payloads/pdf", "", $filePath);

          // Add current file to archivev
          $zip->addFile($filePath, $relativePath);
        }
      }
      addPayload($zip, $type, "pdf_gen.ps1");
      break;
    case 'ps':
      addPayload($zip, $type, "payload.ps1");
      addReadmeFile($zip, $type);
      break;
    case 'vba':
      addPayload($zip, $type, "payload.vba");
      addReadmeFile($zip, $type);
      break;
    default:
      die("unknown payload type");
      exit;
  }

  $zip->close();
  return $archiveName;
}

// ==================
// ADD PAYLOAD TO ZIP
// ==================
function addPayload(ZipArchive $zip, $type, $filename){
  $baseDir = "./assets/payloads/";
  $zip->addFromString($filename, replaceBaseUrl($baseDir . $type . "/". $filename));
}

// ==================
// ADD README TO ZIP
// ==================
function addReadmeFile(ZipArchive $zip, $type){
  $baseDir = "./assets/payloads/";
  $path = $baseDir . $type ."/readme.txt";
  $zip->addFile($path, basename($path));
}

// ===================
// BUILD PAYLOAD DL URL
// ===================
function buildPayloadDlUrl($type){
  if (!validatePayloadType($type)) {
    die("unknown payload type");
    exit;
  }
  $url = "./payload-dl.php?payload=" .$_GET['type'] ."&baseurl=" . $_GET['url'] . "&csrf=".$_SESSION['csrf'];
  if(array_key_exists('htaAppname', $_GET)){
    $url .= "&htaAppname=" . $_GET['htaAppname'];
  }
  return $url;
}

// ==================
// GET README CONTENTS
// ==================
function getReadMeContents($type){
  if (!validatePayloadType($type)) {
    die("unknown payload type");
    exit;
  }
  $baseDir = "./assets/payloads/";
  $path = $baseDir . $type ."/readme.txt";
  return file_get_contents($path);

}

// =====================
// VALIDATE PAYLOAD TYPE
// =====================
function validatePayloadType($type){
  $typeArray = ["batch","hta","java","pdf","ps","vba"];
  return in_array(strtolower($type), $typeArray);
}

// ==================
// REPLACE BASE URL
// ==================
function replaceBaseUrl($filePath){
  return str_replace("{{BASE_URL}}", $_GET['baseurl'], file_get_contents($filePath));
}

// ==================
// RENDER STATS CHART
// ==================
function renderStatsChart(){
	require("../db.php");
	$sql="SELECT codes.group_name, codes.codeid, COUNT(clicks.codeid)
        AS click_count FROM codes
        LEFT JOIN clicks ON codes.codeid = clicks.codeid GROUP BY codes.codeid
        ORDER BY click_count DESC";
	$stmt = $pdo->prepare($sql);
	$results = array();
	if ($stmt->execute()){
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $row;
			}
		if (count($results) == 0){
			echo '<div class="message message-error">There are no clicks yet, go snare some targets!</div>';
			return;
		}
    $total = getTotalClicks();
    foreach($results as $result){
      $result['percentage'] = ($result['click_count']/$total)*100;
      renderTemplate("./templates/stats/row.php", $result);
    }

	}
}

// ===========
// GET USER IP
// ===========
function getUserIP() {
  // Walk through possibilities in the order we'd like to determine the IP
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
?>
