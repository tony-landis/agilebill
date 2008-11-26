 
<p><b>Step 1:</b> Checking file permissions...
  <?php if($files === true) { ?>
done!<br>
<br>
</p>
<b>Please check the path and database settings below: </b> <br>
 
<form name="form1" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr> 
      <td width="27%">Base Path:</td>
      <td width="73%"> <input type="text" name="path_agile" size="60" value="<?php echo $path ?>"></td>
    </tr>
    <tr> 
      <td width="27%">Database Type:</td>
      <td width="73%"> <select name="db_type">
        <option value="mysqlt" selected>MySQL with Transactions (recommended)</option>
        <option value="mysql" selected>MySQL</option>
      </select></td>
    </tr>
    <tr> 
      <td width="27%">Database Host:</td>
      <td width="73%"> <input type="text" name="db_host" value="<?php if(defined('AGILE_DB_HOST')) echo AGILE_DB_HOST; ?>"></td>
    </tr>
    <tr> 
      <td width="27%">Database Name:</td>
      <td width="73%"> <input type="text" name="db_database" value="<?php if(defined('AGILE_DB_DATABASE')) echo AGILE_DB_DATABASE ?>"></td>
    </tr>
    <tr> 
      <td width="27%">Database Username:</td>
      <td width="73%"> <input type="text" name="db_username" value="<?php if(defined('AGILE_DB_USERNAME')) echo AGILE_DB_USERNAME ?>"></td>
    </tr>
    <tr> 
      <td width="27%">Database Password:</td>
      <td width="73%"> <input type="text" name="db_password" value="<?php if(defined('AGILE_DB_PASSWORD')) echo AGILE_DB_PASSWORD ?>"></td>
    </tr>
    <tr> 
      <td width="27%">Database Prefix</td>
      <td width="73%"> <input type="text" name="db_prefix" value="<?php if(defined('AGILE_DB_PREFIX') && AGILE_DB_PREFIX != "") echo AGILE_DB_PREFIX; else echo 'ab_'; ?>"></td>
    </tr>
    <tr> 
      <td width="27%">&nbsp;</td>
      <td width="73%"> <input type="submit" name="Submit" value="Step 2">
        <input type="hidden" name="do" value="step2"></td>
    </tr>
  </table>
</form>
<p> 
  <?php } else { ?>
  <b>failed!</b> <?php echo $files; ?> <br>
  <br>
  <b>To Resolve This Issue:</b><br>
Please access the files/directories listed above and change their permissions to be writable. Then refresh this page in your browser.<br>
  <br>
On Linux based systems, you can use an FTP program to change the CHMOD settings to '777', or you can use the <i>chmod</i> command from the command line.<br>
  <br>
On Windows based systems, you should check that the file/directory is not set to &quot;Read-only.&quot;<br>
  <?php } ?>
</p>
