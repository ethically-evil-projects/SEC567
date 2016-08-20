<tr data-generate-group="<?php echo xssClean($group_name,'attribute');?>" class="extraItemRow">
  <td class="group"><?php echo xssClean($group_name, 'html'); ?></td>

  <td class="link"><?php echo  base_url . tracker_filename . "?" . tracker_variable . "=" . xssClean($code,'html'); ?></td>

  <td class="link">
    <a href="./payload" id="<?php echo xssClean($group_name,'attribute'); ?>_expander" class="extraItemLink">
      Create <span class="expanderArrow">&#9660;</span>
    </a>
  </td>
</tr>

<tr class="hidden additionalInfo">
  <td colspan="3">
    <div class="message message-title">Build Payload</div>

    <form class="form form-inline" method="GET">
      <div class="field">
        <label class="label">Select payload type</label>

        <select id="<?php echo xssClean($group_name,'attribute');?>_type" data-group-dropdown="<?php echo xssClean($group_name,'attribute');?>" class="payloadSelect" name="payload">
          <option value="batch">Batch</option>
          <option value="hta">HTA application</option>
          <option value="java">Java applet</option>
          <option value="pdf">PDF</option>
          <option value="ps" selected>Powershell</option>
          <option value="vba">VBA</option>
        </select>
      </div>

      <div class="field">
        <label class="label">Customise base url which must map to the SSET ip</label>

        <input id="<?php echo xssClean($group_name,'attribute');?>_urlInput" type="text" name="baseurl" class="baseurl" value="<?php echo xssClean(base_url,'attribute'); ?>" autofocus>
      </div>

      <div class="field">
        <label id="<?php echo xssClean($group_name,'attribute');?>_htaLabel" class="label htaAppLabel hidden">
          Application name
        </label>

        <input type="text" id="<?php echo xssClean($group_name,'attribute');?>_htaInput" name="htaAppInput" class="htaAppInput hidden" value="Application">
      </div>

      <div class="actions">
        <input id="<?php echo xssClean($group_name,'attribute');?>_secureId" type="hidden" class="secureId" value="<?php echo xssClean($code,'attribute');  ?>">
        <input id="<?php echo xssClean($group_name,'attribute');?>_csrfToken" type="hidden" class="csrfToken" name="csrf" value="<?php echo $_SESSION['csrf'];?>">
        <input data-payload-download="<?php echo xssClean($group_name,'attribute');?>" type="submit" value="Download" class="btn downloadLink">
      </div>
    </form>

    <pre id="<?php echo xssClean($group_name,'attribute');?>_readme" class="readmesection">
### User Instructions
Convert the ps1 script into an executable here: http://www.f2ko.de/en/op2e.php

Recommended Settings:
```
Architecture: 32-Bit.
Visibility: Invisible.
Admin. Manifest: Optional. If ticked, will ask for Admin privileges from the user.
(Useful for testing who will accept the dialog box)
Password: None.
```</pre>
  </td>
</tr>
