<?php
include("header.php");
include("admin-functions.php");
include("banner.php");

// Check the user is admin if not redirect.
adminCheck();

$type = preg_replace("/[^A-Za-z0-9 ]/", '', $_GET['type']);
$downloadUrl = buildPayloadDlUrl($type);

?>
<META HTTP-EQUIV="Refresh" CONTENT="2; URL=<?php echo xssClean($downloadUrl, "attribute"); ?>">
<div class="generate download">
  <p>The payload should download shortly. If not click <a href="<?php echo xssClean($downloadUrl, "attribute"); ?>">here</a></p>
  <pre>
  <?php include("assets/payloads/".strtolower($_GET['type'])."/readme.txt"); ?>
  </pre>
</div>
<?php
 include("footer.php");
?>
