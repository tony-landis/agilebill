{$list->unserial($checkout.plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=checkout}
      mode 
      {/translate}
    </td>
    <td width="50%"> 
      <select name="checkout_plugin_data[testMode]" class="form_menu">
        <option value="0" {if $plugin_data.testMode == "0"}selected{/if}>
        {translate module=checkout}
        mode_live
        {/translate}
        </option>
        <option value="100" {if $plugin_data.testMode == "100"}selected{/if}>
        {translate module=checkout}
        mode_test
        {/translate}
        </option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> WorldPay Install ID</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[instId]" value="{$plugin_data.instId}" class="form_field">
    </td>
  </tr>
</table>


 