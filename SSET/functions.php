<?php
// Tracker releated functions

require_once("config.php");

// ==========
// CHECK CODE
// ==========
function checkCode($code){
	// Use: Checks if provided code exists or not.
	// Returns: Codeid number if exists, otherwise calls invalidRequestRedirect to redirect to decoy and dies.
	require("db.php");
	$sql = "SELECT codeid FROM codes WHERE code = :code";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code, PDO::PARAM_STR);
	$stmt->execute();
	$return_code = $stmt->fetchColumn();

	if ($return_code == ''){
		// Code did not match
		invalidRequestRedirect();
	}
	return $return_code;

}
// ==========
// REDIRECTURL
// ==========
function getRedirectUrl($code){
  require("db.php");
	$sql = "SELECT redirect_url FROM codes WHERE codeid = :code";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code, PDO::PARAM_STR);
	$stmt->execute();
	$url = $stmt->fetchColumn();

	if ($url == ''){
		// Code did not match
		return redirect_on_success;
	}
	return $url;
}

// ===============
// INVALID REQUEST
// ===============
function invalidRequestRedirect(){
	$redirect_header = "Location: " . redirect_on_error;
	header($redirect_header);
	die("Didn't redirect to invalid URL");
	exit;
}

// ======================
// VALID REQUEST REDIRECT
// ======================
function validRequestRedirect($code){
	$redirect_header = "Location: " . getRedirectUrl($code);
	header($redirect_header);
	die("Did redirect to valid URL");
	exit;
}

// ==========
// HANDLE GET
// ==========
function handleGET($variable){
	// Use: Check if a GET was set for the given name and if so return it, else return blank '-'
	return isset($_GET[$variable])
		? $_GET[$variable]
		: '-';
}

// ==============
// REGISTER CLICK
// ==============
function registerClick($codeid,$username,$ip_address){
	// Use: Insert in to the database the provided details and date/time.

	// First get users browser info:
	$browser=getBrowser();
  $_REQUEST['Public_IP'] = $_SERVER['REMOTE_ADDR'];
  $extraData = json_encode($_REQUEST);

	require("db.php");
	$sql = "INSERT INTO clicks (codeid, username, ip_address, browser_name, browser_version, platform, user_agent_full, time, extra) VALUES (:codeid, :username, :ip_address, :browser_name, :browser_version, :platform, :user_agent_full, NOW(), :extra)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':codeid', $codeid, PDO::PARAM_INT);
	$stmt->bindParam(':username', $username, PDO::PARAM_STR);
	$stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
	$stmt->bindParam(':browser_name', $browser['name'], PDO::PARAM_STR);
	$stmt->bindParam(':browser_version', $browser['version'], PDO::PARAM_STR);
	$stmt->bindParam(':platform', $browser['platform'], PDO::PARAM_STR);
	$stmt->bindParam(':user_agent_full', $browser['userAgent'], PDO::PARAM_STR);
	$stmt->bindParam(':extra', $extraData, PDO::PARAM_STR);
	$stmt->execute();
	validRequestRedirect($codeid);
	die("Redirect failed. Terminating.");
	exit;
}

// ===========
// GET BROWSER
// ===========
function getBrowser() {
	// Establish defaults
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $b_name = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    // First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }

    // Next get the name of the useragent
	if(preg_match('/Trident/i', $u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $b_name = 'Internet Explorer';
        $ub = "Trident";
    } elseif(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $b_name = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $b_name = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $b_name = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $b_name = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $b_name = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $b_name = 'Netscape';
        $ub = "Netscape";
    }

    // Finally, get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number, just continue
    }

    // See how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        // We will have two since we are not using 'other' argument yet
        // see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)) {
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
    }

    // Check if we have a number
    if ($version==null || $version=="") {$version="?";}

	// Return the data
    return array(
        'userAgent' => $u_agent,
        'name'      => $b_name,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}
?>
