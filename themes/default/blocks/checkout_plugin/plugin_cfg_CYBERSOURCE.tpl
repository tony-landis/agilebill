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
    <td width="50%">Cybersource Merchant ID</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[merchantID]" value="{$plugin_data.merchantID}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Cybersource Keys Location</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[keysDirectory]" value="{$plugin_data.keysDirectory}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Cybersource API Version</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[targetAPIVersion]" value="{$plugin_data.targetAPIVersion}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Cybersource SSL Certificate File Location</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[sslCertFile]" value="{$plugin_data.sslCertFile}" class="form_field">
    </td>
  </tr>    
  <tr valign="top">
    <td width="50%">
      {translate module=checkout}
      card_type
      {/translate}
    </td>
    <td width="50%">
      <select name="checkout_plugin_data[card_type][]" class="form_menu" size="6" multiple>
        <option value="visa"{foreach from=$plugin_data.card_type item=type}{if $type == "visa"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_visa
        {/translate}
        </option>
        <option value="mc"{foreach from=$plugin_data.card_type item=type}{if $type == "mc"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_mc
        {/translate}
        </option>
        <option value="amex"{foreach from=$plugin_data.card_type item=type}{if $type == "amex"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_amex
        {/translate}
        </option>
        <option value="discover"{foreach from=$plugin_data.card_type item=type}{if $type == "discover"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_discover
        {/translate}
        </option>
        <option value="delta"{foreach from=$plugin_data.card_type item=type}{if $type == "delta"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_delta
        {/translate}
        </option>
        <option value="solo"{foreach from=$plugin_data.card_type item=type}{if $type == "solo"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_solo
        {/translate}
        </option>
        <option value="switch"{foreach from=$plugin_data.card_type item=type}{if $type == "switch"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_switch
        {/translate}
        </option>
        <option value="jcb"{foreach from=$plugin_data.card_type item=type}{if $type == "jcb"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_jcb
        {/translate}
        </option>
        <option value="diners"{foreach from=$plugin_data.card_type item=type}{if $type == "diners"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_diners
        {/translate}
        </option>
        <option value="carteblanche"{foreach from=$plugin_data.card_type item=type}{if $type == "carteblanche"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_carteblanche
        {/translate}
        </option>
        <option value="enroute"{foreach from=$plugin_data.card_type item=type}{if $type == "enroute"} selected{/if}{/foreach}>
        {translate 
        module=checkout}
        card_type_enroute
        {/translate}
        </option>
      </select>
    </td>
  </tr>
</table>


 