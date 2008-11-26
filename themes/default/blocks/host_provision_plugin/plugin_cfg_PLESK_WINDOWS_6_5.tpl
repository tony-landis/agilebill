{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      field_keycode 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_server_keycode" value="{$host_server.keycode}" class="form_field" size="40" maxlength="64">
    </td>
  </tr>
</table>
 
<table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
  <tr> 
    <td>To complete the automation for this server with the selected provisioning 
      plugin, you must paste the text in the box below and save it as AgileBill.bat 
      on the target server in the directory where the plesk creation utilities 
      are created. You must then give the AgileBill.bat file you have created 
      proper permission to execute the Plesk creation utilities, and create a 
      task that runs the AgileBill.bat file each time your server starts.<br>
      <br>
      You must also have curl installed, and make sure the correct path to the 
      curl directory is reflected in the 1st line of your .bat file...</td>
  </tr>
  <tr>
    <td> 
      <div align="center"> 
        <textarea name="textfield" cols="60" rows="8" class="form_field">@SET CURL=c:\curl\curl
@SET KEY={$host_server.keycode}
@SET DEL=120
@SET PLG={$host_server.provision_plugin}
@SET URL={$URL}plugins/provision/
 
:LOOP
%CURL% -d "key=%KEY%" %URL%%PLG%.php > AgileBill_Out.bat
GOTO WAIT
AgileBill_Out.bat & CALL AgileBill
GOTO LOOP
:END
 
:WAIT
@ping 127.0.0.1 -n 2 -w 1000 > nul
@ping 127.0.0.1 -n %DEL% -w 1000 > nul
GOTO LOOP
:END
</textarea>
      </div>
    </td>
  </tr>
</table>
