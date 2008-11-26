{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr valign="top" class="row2">
    <td colspan="2"><font size="+1">VoIP Platform</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Platform: </td>
    <td><select name="product_prod_plugin_data[voip_platform]" onChange="submit();">
		<option value="asterisk" {if $plugin_data.voip_platform =="asterisk"}selected{/if}>Asterisk</option>
		<option value="ser" {if $plugin_data.voip_platform =="ser"}selected{/if}>SER</option>
        </select></td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2">&nbsp;</td>
  </tr>
{if $plugin_data.voip_platform eq "asterisk" || $plugin_data.voip_platform eq ""}  
  <tr valign="top" class="row2"> 
    <td colspan="2"><font size="+1">Account Creation and Provisioning</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td width="49%"><em>Allow Selection of Parent Service?</em><br>
      This allows you to modify existing accounts to provision additional features to their existing services. It also allows you to provision virtual accounts to a parent service. </td>
    <td valign="top">{ $list->bool("product_prod_plugin_data[parent_enabled]", $plugin_data.parent_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2"><div align="center"><strong>NOTE: From the selections available below, only <em>ONE</em> may be yes. Failure to yield to this will cause unpredictable results. </strong></div></td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Virtual Account?</em><br>
    This allows the assignment of new DIDs to a parent service, so that calling a purchased DID actually rings the phone associated with a parent service. </td>
    <td>{ $list->bool("product_prod_plugin_data[virtual_number]", $plugin_data.virtual_number, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Provision a peer account on Asterisk?</em><br>
      For regular accounts, this allows the provisioning of a channel, allowing for an actual telephone/adaptor to register with the Asterisk server using the channel type selected below.. </td>
    <td>{ $list->bool("product_prod_plugin_data[provision_enabled]", $plugin_data.provision_enabled, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Channel Type</em><br>
      The channel type to be provisioned. Requires the above selection to be Yes. </td>
    <td><select name="product_prod_plugin_data[provision_channel]">
		<option value="0" {if $plugin_data.provision_channel =="0"}selected{/if}>SIP</option>
		<option value="1" {if $plugin_data.provision_channel =="1"}selected{/if}>IAX2</option>
        </select>
	</td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Remote Call Forwarding?</em><br>
      Allows a DID to forward to another DID.</td>
    <td>{$list->bool("product_prod_plugin_data[remote_call_forwarding]", $plugin_data.remote_call_forwarding, "form_menu")} <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>MeetMe Conferencing?</em><br>
    Allows the DID to terminate into a MeetMe conference</td>
    <td>{ $list->bool("product_prod_plugin_data[meetme_account]", $plugin_data.meetme_account, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Fax2Email?</em><br>
    Allows the DID to terminate into the fax 2 email application</td>
    <td>{ $list->bool("product_prod_plugin_data[fax_account]", $plugin_data.fax_account, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Context within the dial plan for provisioned account:</em><br>
      This gives you the ability to create different service levels, by placing different services in to different contexts. For instance: A context called economy could terminate all calls over VoIP, but a context called premium could terminate all calls out TDM. </td>
    <td><input type="text" name="product_prod_plugin_data[context]" value="{$plugin_data.context}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Use DID Pool Plugin(s) :</em><br>
      Allow the customer to pick their DIDs from the following..</td>
    <td>{html_menu_multi name=product_prod_plugin_data[voip_did_plugins] assoc_table="voip_did_plugin" assoc_field="name" size=5 default=$plugin_data.voip_did_plugins}</td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2"><font size="+1">Features</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Provision Voicemail? </td>
    <td>{ $list->bool("product_prod_plugin_data[voicemail_enabled]", $plugin_data.voicemail_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Provision Call waiting? </td>
    <td>{ $list->bool("product_prod_plugin_data[callwaiting_enabled]", $plugin_data.callwaiting_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Provision Blacklisting? </td>
    <td>{ $list->bool("product_prod_plugin_data[blacklist_enabled]", $plugin_data.blacklist_enabled, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Provision ANI call routing? </td>
    <td>{ $list->bool("product_prod_plugin_data[anirouting_enabled]", $plugin_data.anirouting_enabled, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Provision Fax detection? </td>
    <td>{ $list->bool("product_prod_plugin_data[faxdetection_enabled]", $plugin_data.faxdetection_enabled, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Calling Name Display?<br>
      Requires add-on service from iSPServices.</td>
    <td>{ $list->bool("product_prod_plugin_data[cnam_enabled]", $plugin_data.cnam_enabled, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Allow assignment of fail-over destination? </td>
    <td>{ $list->bool("product_prod_plugin_data[can_failover]", $plugin_data.can_failover, "form_menu") } <font color="red">*</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2"><font size="+1">Rating Engine</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Add DIDs provisioned in to &quot;In-Network&quot; rating type? </td>
    <td>{ $list->bool("product_prod_plugin_data[innetwork_enabled]", $plugin_data.innetwork_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Rate CDR records associated with this service? </td>
    <td>{ $list->bool("product_prod_plugin_data[rate_cdr]", $plugin_data.rate_cdr, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td>Rate CDR records associated with this service as account codes?<br>
      Required if there isn't a DID assignment. </td>
    <td>{ $list->bool("product_prod_plugin_data[rate_accountcode]", $plugin_data.rate_accountcode, "form_menu") } </td>
  </tr>
  <!--
  <tr valign="top"> 
    <td width="49%"> MeetMe Conference Limit (Combined Calling Minute Usage)</td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_prod_plugin_data[meetme_min_limit]" value="{$plugin_data.meetme_min_limit}"  size="10">
      0-Unlimited</td>
  </tr>
  -->
{/if}
{if $plugin_data.voip_platform eq "ser"}
  <tr valign="top" class="row2"> 
    <td colspan="2"><font size="+1">Account Creation and Provisioning</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td width="49%"><em>Allow Selection of Parent Service?</em><br>
      This allows you to modify existing accounts to provision additional features to their existing services. It also allows you to provision virtual accounts to a parent service. </td>
    <td valign="top">{ $list->bool("product_prod_plugin_data[parent_enabled]", $plugin_data.parent_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Virtual Account?</em><br>
    This allows the assignment of new DIDs to a parent service, so that calling a purchased DID actually rings the phone associated with a parent service. </td>
    <td>{ $list->bool("product_prod_plugin_data[virtual_number]", $plugin_data.virtual_number, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Groups granted for provisioned account:</em><br>
      This gives you the ability to create different service levels, by placing different services in to different groups. For instance: A group called economy could terminate all calls over VoIP, but a group called premium could terminate all calls out TDM. </td>
    <td><p>
      <input type="text" name="product_prod_plugin_data[context]" value="{$plugin_data.context}"  size="24">
    </p>
    <p><em>Please separate multiple entries with a comma. </em>      </p></td>
  </tr>
  <tr valign="top" class="row2">
    <td><em>Use DID Pool Plugin(s) :</em><br>
      Allow the customer to pick their DIDs from the following..</td>
    <td>{html_menu_multi name=product_prod_plugin_data[voip_did_plugins] assoc_table="voip_did_plugin" assoc_field="name" size=5 default=$plugin_data.voip_did_plugins}</td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;
	<input type="hidden" name="product_prod_plugin_data[provision_channel]" value="0">
	<input type="hidden" name="product_prod_plugin_data[provision_enabled]" value="1">
	</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2"><font size="+1">Rating Engine</font></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Add DIDs provisioned in to &quot;In-Network&quot; rating type? </td>
    <td>{ $list->bool("product_prod_plugin_data[innetwork_enabled]", $plugin_data.innetwork_enabled, "form_menu") }</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Rate CDR records associated with this service? </td>
    <td>{ $list->bool("product_prod_plugin_data[rate_cdr]", $plugin_data.rate_cdr, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td>Rate CDR records associated with this service as account codes?<br>
      Required if there isn't a DID assignment. </td>
    <td>{ $list->bool("product_prod_plugin_data[rate_accountcode]", $plugin_data.rate_accountcode, "form_menu") } </td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td colspan="2"><font size="+1">SER Specific</font></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Database Host: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_host]" value="{$plugin_data.ser_db_host}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Database Name: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_name]" value="{$plugin_data.ser_db_name}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Database Username: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_user]" value="{$plugin_data.ser_db_user}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Database Password: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_pass]" value="{$plugin_data.ser_db_pass}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>SER IP or Hostname for URIs: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_ip]" value="{$plugin_data.ser_ip}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Subscriber table name: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_subscriber]" value="{$plugin_data.ser_db_subscriber}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Aliases table name: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_alias]" value="{$plugin_data.ser_db_alias}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Group table name: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_grp]" value="{$plugin_data.ser_db_grp}"  size="24"></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Accounting table name: </td>
    <td><input type="text" name="product_prod_plugin_data[ser_db_acc]" value="{$plugin_data.ser_db_acc}"  size="24"></td>
  </tr>  
{/if}
  <tr valign="top" class="row2">
    <td colspan="2"><font size="-1">(<font color="red">*</font>) Anything marked with an asterisk is available in the professional version only.</font></td>
  </tr>
</table>
