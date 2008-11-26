{$list->unserial($checkout.plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=checkout}
      mode 
      {/translate}
    </td>
    <td width="50%"> 
      <select name="checkout_plugin_data[mode]" class="form_menu">
        <option value="0" {if $plugin_data.mode == "0"}selected{/if}> 
        {translate module=checkout}
        mode_test 
        {/translate}
        </option>
        <option value="1" {if $plugin_data.mode == "1"}selected{/if}> 
        {translate module=checkout}
        mode_live 
        {/translate}
        </option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 2checkout.com Merchant ID</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[id]" value="{$plugin_data.id}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">2checkout.com Secret Phrase</td>
    <td width="50%">
      <input type="text" name="checkout_plugin_data[secret]" value="{$plugin_data.secret}" class="form_field">
    </td>
  </tr>
</table>


 