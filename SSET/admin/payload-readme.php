<?php
include("admin-functions.php");
// Check the user is admin if not redirect.
adminCheck();
// Will be returning JSON so content type needs to match.
header_remove('Content-Type');                     // <<< Maybe check if this header exists before attempting to remove
header('Content-Type: application/json; charset=utf-8');


if(!isset($_REQUEST['readmeType'])) {
    die('No payload specified');
    exit;
}

// Set default status info.
$statusResponse = "Error";
$statusResponse = "Failed to get payload info";

// Get payload array info according to $_REQUEST['payload'] value...
$contents = getReadMeContents(preg_replace("/[^A-Za-z0-9 ]/", '', $_REQUEST['readmeType']));
// Override status info that everything is OK
if ($contents) {
    $statusResponse = "OK";
    $statusMessage = "Success";
}
$jsonContents = json_encode($contents);
// Establish our JSON response.
$jsonResponse = <<< HDT
{
    "payload" : {
        "instructions" : $jsonContents
    },
    "status" : {
        "response"     : "$statusResponse",
        "message"      : "$statusMessage"
    }
}
HDT;

// Now output the JSON.
echo $jsonResponse;
