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
    <td width="50%"> CrowneGold Account</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[account]" value="{$plugin_data.account}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Passphrase</td>
    <td width="50%">
      <input type="text" name="checkout_plugin_data[secret]" value="{$plugin_data.secret}" class="form_field">
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">Metal</td>
    <td width="50%">
      <select name="checkout_plugin_data[metal]" class="form_menu">
        <option value="Z01" {if $plugin_data.metal == "Z01"}selected{/if}>Buyers 
        Choice</option>
        <option value="XAU" {if $plugin_data.metal == "XAU"}selected{/if}>Gold</option>
        <option value="XAG" {if $plugin_data.metal == "XAG"}selected{/if}>Silver</option>
        <option value="XPT" {if $plugin_data.metal == "XPT"}selected{/if}>Platinum</option>
        <option value="XPD" {if $plugin_data.metal == "XPD"}selected{/if}>Palladium</option>
      </select>
    </td>
  </tr>
</table>


 