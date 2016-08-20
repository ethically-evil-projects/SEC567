<?php
include("admin-functions.php");
// Check the user is admin if not redirect.
adminCheck();
$archiveName = createPayloadZip();

header('Cache-Control: no-store, no-cache, must-revalidate');
header("Content-type: application/zip");
header('Content-Length: '.filesize($archiveName));
header('Content-Disposition: attachment; filename="payload.zip"');

readfile($archiveName);
unlink($archiveName);
die("Archive successul");
exit;
?>
