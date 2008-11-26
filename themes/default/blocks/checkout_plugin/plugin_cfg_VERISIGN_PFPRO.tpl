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
    <td width="50%">Default Transaction Type</td>
    <td width="50%"> 
      <select name="checkout_plugin_data[type]" class="form_menu">
        <option value="S" {if $plugin_data.type == "S"}selected{/if}> Authorization 
        + Capture (standard) </option>
        <option value="A" {if $plugin_data.type == "A"}selected{/if}> Authorization 
        Only </option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Verisign Login</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[user]" value="{$plugin_data.user}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Verisign Password</td>
    <td width="50%"> 
      <input type="password" name="checkout_plugin_data[password]" value="{$plugin_data.password}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Verisign Partner</td>
    <td width="50%"> 
      <input type="text" name="checkout_plugin_data[partner]" value="{ if $plugin_data.partner == ""}VeriSign{else}{$plugin_data.partner}{/if}" class="form_field">
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


 