<?php
include("header.php");
include("admin-functions.php");
include("banner.php");

if (adminCount() != 0){
 header("Location: ./login");
 die("Redirect to dashboard");
 exit;
}


if (isset($_POST['submit']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])){
  $errors = registerAdmin($_POST['username'], $_POST['password'], $_POST['password2']);
  if (count($errors) === 0){
    // Put logged in admin into dashboard.
    header("Location: ./login");
    die("Redirect to dashboard");
    exit;
  } else {
    $throw_message = '<div class="message message-error"><ul>';
    foreach($errors as $error){
      $throw_message .= '<li>'. xssClean($error,'html') . '</li>';
    }
    $throw_message .= '</ul></div>';
  }
}

?>
<div class="sessions">
  <div class="message message-heading">Admin sign up</div>

  <?php if (isset($throw_message)){ echo $throw_message; } ?>

  <form name="login" class="form" action="./register" method="POST" accept-charset="utf-8">
    <div class="field">
      <label class="label">Username</label>

      <input type="text" name="username">
    </div>

    <div class="field">
      <label class="label">Password</label>

      <input type="password" name="password" placeholder="Password">

      <div class="tip">Enter a password that has at least 10 characters and contains at least 1 special character, 1 number, 1 uppercase and 1 lowercase characters.</div>
    </div>

    <div class="field">
      <label class="label">Confirm password</label>

      <input type="password" name="password2">
    </div>

    <div class="actions">
      <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf'];?>">
      <input name="submit" type="submit" value="Submit" class="btn btn-full">
    </div>
  </form>
</div>

<?php include("footer.php"); ?>
