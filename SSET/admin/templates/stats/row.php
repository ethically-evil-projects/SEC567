<div class="row">
  <div class="group"><?php echo xssClean($group_name, 'html'); ?></div>

  <div class="bar">
    <div class="inner" style="width: <?php echo $percentage; ?>%;"></div>

    <span class="stat"><?php echo $click_count; ?> clicks</span>
  </div>
</div>
