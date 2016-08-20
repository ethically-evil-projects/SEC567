<?php
include("header.php");
include("admin-functions.php");
include("banner.php");
// Need inline styling for this page untill we figure out a better way.
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'  fonts.googleapis.com; font-src 'self' fonts.gstatic.com;");

// Check the user is admin if not redirect.
adminCheck();
?>

<div class="chart">
  <?php renderStatsChart(); ?>
</div>

<?php include("footer.php"); ?>
