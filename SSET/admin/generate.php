<?php
include("header.php");
include("admin-functions.php");
include("banner.php");

// Check the user is admin if not redirect.
adminCheck();

if (isset($_POST['submit'])){
  if ($_POST['group'] == ""){
    $throw_message = '<div class="message message-error">Please enter a group name to create a code.</div>';
  } else {
    $group_name = $_POST['group'];
    $return_code = createCode($group_name, $_POST['redirecturl']);
    if ($return_code != 'group_exists'){
      $code = $return_code;
      $url = base_url . tracker_filename . "?" . tracker_variable . "=" . $code;
      $cleanGroup = xssClean($group_name,'html');
      $throw_message = <<<EDT
  <div class="message message-success">Success! The tracking URL for $cleanGroup is $url</div>
EDT;
    } else {
      $throw_message = '<div class="message message-error">Sorry, your code could not be generated as this group already exists.</div>';
    }
  }
}
?>

<div class="generate">
  <div class="message message-heading">Generate code</div>

  <?php if (isset($throw_message)){ echo $throw_message; } ?>

  <form class="form" method="POST">
    <div class="field">
      <label class="label">Enter group name</label>

      <input type="text" id="groupInput" name="group">
    </div>

    <div class="field">
      <label class="label">Enter URL to redirect users to</label>

      <input type="text" pattern="https?://.+" id="redirectUrlInput" name="redirecturl">

      <div class="tip"><strong>Tip:</strong> Must start with http(s)://, default is http://www.google.com</div>
    </div>

    <div class="actions">
      <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf'];?>">
      <input type="submit" name="submit" value="Generate code" class="btn btn-full">
    </div>
  </form>

  <div class="codes">
    <div class="message message-heading">View existing codes</div>

    <table class="table">
      <thead>
        <tr>
          <th><span class="title">Group</span></th>
          <th><span class="title">Link</span></th>
          <th><span class="title">Payload</span></th>
        </tr>
      </thead>

      <tbody>
        <?php renderCodeList(); ?>
      </tbody>
    </table>
  </div>
</div>

<?php include("footer.php"); ?>
