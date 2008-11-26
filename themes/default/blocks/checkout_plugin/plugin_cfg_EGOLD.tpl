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
        <option value="100" {if $plugin_data.mode == "100"}selected{/if}> 
        {translate module=checkout}
        mode_live 
        {/translate}
        </option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> E-Gold Account</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[account]" value="{$plugin_data.account}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Passphrase</td>
    <td width="50%">
      <input type="text" name="checkout_plugin_data[secret]" value="{$plugin_data.secret}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">Metal </td>
    <td width="50%">
      <select name="checkout_plugin_data[metal]" class="form_menu">
        <option value="0" {if $plugin_data.metal == "0"}selected{/if}>Buyers Choice</option>
        <option value="1" {if $plugin_data.metal == "1"}selected{/if}>Gold</option>
        <option value="2" {if $plugin_data.metal == "2"}selected{/if}>Silver</option>
        <option value="3" {if $plugin_data.metal == "3"}selected{/if}>Platinum</option>
        <option value="4" {if $plugin_data.metal == "4"}selected{/if}>Palladium</option>
      </select>
    </td>
  </tr>
</table>


 