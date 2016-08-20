<?php
include("header.php");
include("admin-functions.php");
include("banner.php");

// Check the user is admin if not redirect.
adminCheck();

if (isset($_GET['order'])){
  $order = $_GET['order'];
  if ($order == 'username' || $order == 'ip_address' || $order == 'group' || $order == 'time'){
    // Leaving space for future checks or pre-conditions.
  } else {
    $order = 'time';
  }
} else {
  $order = 'time';
}
?>

<div class="data">
  <table class="table">
    <thead>
      <tr>
        <th class="group"><a href="?order=group&csrf=<?php echo $_SESSION['csrf'];?>" class="title">Group &darr;</a></th>
        <th class="payload"><span class="title">Payload</span></th>
        <th class="logos"><span class="title">Browser</span></th>
        <th class="user"><a href="?order=username&csrf=<?php echo $_SESSION['csrf'];?>" class="title">User &darr;</a></th>
        <th class="ip"><a href="?order=ip_address&csrf=<?php echo $_SESSION['csrf'];?>" class="title">Local IP &darr;</a></th>
        <th class="extra"><span class="title">Extra</span></th>
        <th class="time"><a href="?order=time&csrf=<?php echo $_SESSION['csrf'];?>" class="title">Date & Time &darr;</a></th>
      </tr>
    </thead>

    <tbody>
      <?php renderClicks($order); ?>
    </tbody>
  </table>
</div>

<?php include("footer.php"); ?>
