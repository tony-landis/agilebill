<p> <b>Step 2:</b> 
 Writing new configuration settings... 

<?php if($files === true) { ?>
Done!<br><br>
 
<?php if (is_array($modules)) { ?>
<b>Please select any optional modules you wish to install</b></p>
<form name="form1" method="post"> 
  <table width="100%" cellspacing="3" cellpadding="4" bordercolor="#FFFFFF">
    <?php for($i=0; $i<count($modules); $i++) { ?>
    <tr valign="top" bgcolor="#F3F3F3"> 
      <td width="2%" height="25" bgcolor="#F8F8F8">
        <input type="checkbox" name="modules[]" value="<?php echo $modules[$i]['name']; ?>">
	  </td>
      <td width="98%" height="25" bgcolor="#F8F8F8">&nbsp;  
        <?php echo $modules[$i]['desc']; ?>
      </td>
    </tr>
    <?php } ?>
  </table> 
<p>   
<?php } ?>
<?php if (!empty($license_agreement)) { ?>
<input type="submit" name="Submit" value="I Agree, Continue to Step 3">
<?php } else { ?>
<input type="submit" name="Submit" value="Step 3">
<?php } ?>
<input type="hidden" name="do" value="step3">
</form>

<?php } else { ?>
  <b>failed!</b> 
<?php echo $database; ?>
</p>
<p> <br>
  <br>
  <b>To Resolve This Issue:</b><br>
  Please access the files/directories listed above and change their permissions 
  to be writable. Then refresh this page in your browser.<br>
  <br>
  On Linux based systems, you can use an FTP program to change the CHMOD settings 
  to '777', or you can use the <i>chmod</i> command from the command line.<br>
  <br>
  On Windows based systems, you should check that the file/directory is not set 
  to &quot;Read-only,&quot; and the web user has full access to the files.<br>
  <br>
 
  <?php } ?>
</p>
