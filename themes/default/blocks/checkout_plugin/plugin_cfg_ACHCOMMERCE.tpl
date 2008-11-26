{$list->unserial($checkout.plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">ACH Commerce Login</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[login]" value="{$plugin_data.login}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">ACH Commerce Merchant ID </td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[merchantid]" value="{$plugin_data.merchantid}" >
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">ACH Commerce Password </td>
    <td width="50%"> 
      <input type="password" name="checkout_plugin_data[password]" value="{$plugin_data.password}">
    </td>
  </tr>  
  <tr valign="top"> 
    <td width="50%">Batch ID </td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[batchid]" value="{$plugin_data.batchid}" class="form_field"> 
      (leave blank to auto-generate unique batchid) </td>
  </tr>  
  <tr valign="top"> 
    <td width="50%">Verification String</td>
    <td width="50%"><select name="checkout_plugin_data[verstr]" class="form_menu">
      <option value="R" {if $plugin_data.verstr == "R"}selected{/if}>Routing number check indicator (R)</option>
      <option value="T" {if $plugin_data.verstr == "T"}selected{/if}>Thompson database check indicator (T)</option>
	  <option value="S" {if $plugin_data.verstr == "S"}selected{/if}>Store check to database (S)</option>
	  <option value="RS" {if $plugin_data.verstr == "RS"}selected{/if}>Routing &amp; Store (RS)</option>
	  <option value="RTS" {if $plugin_data.verstr == "RTS"}selected{/if}>Routing, Thompson, &amp; Store (RTS)</option>
    </select> 
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Standard Entry Class (contact Ach Commerce sales rep to determine proper code)</td>
    <td width="50%"><select name="checkout_plugin_data[sec]" class="form_menu">
      <option value="PPD" {if $plugin_data.sec == "PPD"}selected{/if}>(PPD) Prearranged Payment and Deposit (Default)</option>
      <option value="POP" {if $plugin_data.sec == "POP"}selected{/if}>(POP) Point of Purchase</option>
	  <option value="TEL" {if $plugin_data.sec == "TEL"}selected{/if}>(TEL) Telephone Initiated Transaction</option>
	  <option value="WEB" {if $plugin_data.sec == "WEB"}selected{/if}>(WEB) Web Initiated Entry</option>
    </select> 
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">&nbsp;
      </td>
    <td width="50%">&nbsp;
    </td>
  </tr>
</table>


 