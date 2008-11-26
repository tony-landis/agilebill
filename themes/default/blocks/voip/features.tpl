
{ $method->exe("voip","features") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 
<form name="voip" method="post" action="">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td valign="top" width="15%">{translate module=voip}phoneno{/translate} </td>
    <td valign="top" align="left"> 
		{html_menu field=voip_did_id assoc_table=voip_did assoc_field=did conditions="account_id = `$smarty.const.SESS_ACCOUNT`" default=$record.id}
		<input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></p> 
		<input type="hidden" name="_page" value="voip:features">  
	</td>
	<td valign="top" align="right">
	<a href="?_page=core:user_search&module=voip_blacklist&_next_page=user_search_show">{translate module=voip}view_blacklist{/translate}</a><br>
	<a href="?_page=voip_blacklist:user_add">{translate module=voip}add_blacklist{/translate}</a>
	</td>
  </tr>
</table>
</form>

{if $record}
<form name="voip" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
         
		<tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip}user_features{/translate} 
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">            <table width="100%" border="0" cellspacing="4" cellpadding="3" class="row1">
            <!--voicemail menus-->
  {if $record.voicemailenabled}
  <td class="row1"><strong>{translate module=voip}user_voicemail{/translate}</strong></td>
  </tr>
  <tr valign="top">
    <td> {translate module=voip}vm1{/translate}
        <select name="voip_voicemailafter">
          <option label="5" value="5" {if $record.voicemailafter == 5}selected{/if}>5</option>
          <option label="10" value="10" {if $record.voicemailafter == 10}selected{/if} >10</option>
          <option label="15" value="15" {if $record.voicemailafter == 15}selected{/if}>15</option>
          <option label="20" value="20" {if $record.voicemailafter == 20}selected{/if}>20</option>
          <option label="25" value="25" {if $record.voicemailafter == 25}selected{/if}>25</option>
          <option label="30" value="30" {if $record.voicemailafter == 30}selected{/if}>30</option>
          <option label="35" value="35" {if $record.voicemailafter == 35}selected{/if}>35</option>
          <option label="40" value="40" {if $record.voicemailafter == 40}selected{/if}>40</option>
        </select>
      {translate module=voip}seconds{/translate} </td>
  </tr>
  <tr valign="top">
    <td> {translate module=voip}vmemail{/translate}
        <input type="text" name="voip_vm_email" value="{$record.vm_email}" size="30">
    </td>
  </tr>
  {/if}
  <!-- callforwarding menus-->
  <tr class="row1" valign="middle" align="left">
    <td class="row1"><strong>{translate module=voip}user_callforwarding{/translate}</strong></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td><input type="checkbox" name="voip_callforwardingenabled" value="1" {if $record.callforwardingenabled}checked{/if}>
      {translate module=voip}ringfor{/translate}
        <select name="voip_cfringfor">
          <option label="5" value="5" {if $record.cfringfor == 5}selected{/if}>5</option>
          <option label="10" value="10" {if $record.cfringfor == 10}selected{/if} >10</option>
          <option label="15" value="15" {if $record.cfringfor == 15}selected{/if}>15</option>
          <option label="20" value="20" {if $record.cfringfor == 20}selected{/if}>20</option>
          <option label="25" value="25" {if $record.cfringfor == 25}selected{/if}>25</option>
          <option label="30" value="30" {if $record.cfringfor == 30}selected{/if}>30</option>
          <option label="35" value="35" {if $record.cfringfor == 35}selected{/if}>35</option>
          <option label="40" value="40" {if $record.cfringfor == 40}selected{/if}>40</option>
        </select>
      {translate module=voip}secbefore{/translate}
      <input name="voip_cfnumber" type="text" value="{$record.cfnumber}" size="11" maxlength="12"></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td class="row1"><div align="left"><strong>{translate module=voip}user_busycallforwarding{/translate}</strong></div></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td><input type="checkbox" name="voip_busycallforwardingenabled" value="1" {if $record.busycallforwardingenabled}checked{/if}>
      {translate module=voip}busyforward{/translate}
        <input name="voip_bcfnumber" type="text" value="{$record.bcfnumber}" size="12" maxlength="12"></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td class="row1"><div align="left"><strong>{translate module=voip}user_callwaiting{/translate}</strong></div></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td><input name="sip_callwaiting" type="checkbox" value="1" {if $record.sip_callwaiting}checked{/if}>
    {translate module=voip}callwaiting{/translate}</td>
  </tr>
  {if $record.rxfax}
  <tr class="row1" valign="middle" align="left">
    <td  class="row1"><strong>{translate module=voip}user_faxing{/translate}</strong></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=voip}emailfaxes{/translate}
        <input name="voip_faxemail" type="text" value="{$record.faxemail}" size="22"></td>
  </tr>
  {/if} {if $record.failover}
  <tr class="row1" valign="middle" align="left">
    <td><strong><strong>{translate module=voip}user_failover{/translate}</strong></strong></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=voip}outageno{/translate}
        <input name="voip_failovernumber" type="text" value="{$record.failovernumber}" size="12" maxlength="12"></td>
  </tr>
  {/if} {if $record.remotecallforward}
  <tr class="row1" valign="middle" align="left">
    <td><strong><strong>{translate module=voip}user_remote_callforward{/translate}</strong></strong></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td><div align="left"> {translate module=voip}remoteforwardline{/translate}
            <input name="voip_remotecallforwardnumber" type="text" value="{$record.remotecallforwardnumber}" size="12" maxlength="12">
    </div></td>
  </tr>
  {/if}
  <tr class="row1" valign="middle" align="left">
    <td><div align="center">
        <input type="hidden" name="voip_did_id" value="{$record.id}">
        <input type="hidden" name="_page" value="voip:features">
        <input type="hidden" name="do[]" value="voip:update_features">
        <br>
        <br>
        <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
    </div></td>
  </tr>

          </table></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</form> 
{/if}
{/if}
