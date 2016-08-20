<tr data-click-key="<?php echo xssClean($key,'attribute');?>" class="extraItemRow">
  <td class="group" width="20%"><?php echo xssClean($group_name,'html'); ?></td>

  <td class="payload" width="10%"><?php echo xssClean($payloadType,'html'); ?></td>

  <td class="logos" width="10%"><?php echo $logo_content; ?></td>

  <td class="user" width="15%"><?php echo xssClean($username,'html'); ?></td>

  <td class="ip" width="15%"><?php echo xssClean($ip_address,'html'); ?></td>

  <td class="extra" width="10%">
    <?php
      $count = count($extra);
      if ($count > 0) {
    ?>
      <a id="expander_<?php echo xssClean($key,'attribute');?>" class="extraItemLink" href="#" data-tip="Click to expand">
        <span><?php echo $count ?> Item<?php echo ($count != 1 ? "s" : "");?> </span><span class="expanderArrow">&#9660;</span>
      </a>
    <?php } else { ?>
      <span>-</span>
    <?php }?>
  </td>

  <td class="time" width="20%"><?php echo xssClean($time,'html'); ?></td>
</tr>
<tr class="hidden additionalInfo">
  <td class="extraDataExpanded" colspan="7">
    <div class="message message-title">Additional Information</div>

    <ul>
      <?php
        foreach((array)$extra as $extraKey => $extraItem) {
          echo "<li>" . xssClean($extraKey,'html') . " : " .  xssClean($extraItem,'html') ."</li>";
        }
      ?>
    </ul>
  </td>
</tr>
