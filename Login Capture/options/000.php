<?php include ("../shared/_header.php"); ?>

<!--

  ** Note **
  - This is the standard structure with basic default styles.

  ** Layouts **
  - .layout-simple
  - .layout-enclosed
  - .layout-split (with .panel-left or .panel-right)
  - .layout-edge (with .panel-left or .panel-right)

-->

<body class="option layout-simple">
  <div class="content">
    <div class="header">
      <div class="logo">Widgets Inc.</div>
    </div>

    <?php include ("../shared/_form.php"); ?>

    <div class="footer">
      <div class="info">Copyright Widgets Inc. <?php echo date("Y") ?></div>
    </div>
  </div>
</body>

<?php include ("../shared/_footer.php"); ?>
