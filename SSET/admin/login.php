<?php
include("header.php");
include("admin-functions.php");
include("banner.php");

if (adminCount() == 0){
  header("Location: ./register");
  die("Redirect to dashboard");
  exit;
}

if (isset($_SESSION['admin_id'])){
  header("Location: .");
  die("Redirect to login");
  exit;
}

if (isset($_POST['submit'])){
  $result = doAdminLogin($_POST['username'],$_POST['password']);
  if ($result === true){
    // Put logged in admin into dashboard.
    header("Location: .");
    die("Redirect to dashboard");
    exit;
  } else {
    $throw_message = '<div class="message message-error">'.$result.'</div>';
  }
}
?>

<div class="sessions">
  <div class="message message-heading">Admin access required</div>

  <?php if (isset($throw_message)){ echo $throw_message; } ?>

  <form name="login" class="form" action="./login" method="POST" accept-charset="utf-8">
    <div class="field">
      <label class="label">Username</label>

      <input type="text" name="username">
    </div>

    <div class="field">
      <label class="label">Password</label>

      <input type="password" name="password">
    </div>

    <div class="actions">
      <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf'];?>">
      <input name="submit" type="submit" value="Log in" class="btn btn-full">
    </div>
  </form>
</div>

<?php include("footer.php"); ?>
