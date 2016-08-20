<div class="banner">
  <img src="assets/images/logo.png" alt="Simple Social Engineering Tracker" class="logo">

  <div class="nav">
    <a href="." class="link">Home</a>

    <a href="generate" class="link">Generate Code</a>

    <a href="stats" class="link">Stats</a>

    <?php if (isset($_SESSION['admin_id'])){?>
      <a href="./logout?csrf=<?php echo $_SESSION['csrf']; ?>" class="link">Logout</a>
    <?php }?>
  </div>
</div>
