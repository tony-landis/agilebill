

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="affiliate_template_add" name="affiliate_template_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate_template}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="affiliate_template_name" value="{$VAR.affiliate_template_name}" {if $affiliate_template_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="affiliate_template_notes" cols="40" rows="5" {if $affiliate_template_notes == true}class="form_field_error"{/if}>{$VAR.affiliate_template_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
				  {if $VAR.affiliate_template_status != "" }
                    { $list->bool("affiliate_template_status", $VAR.affiliate_template_status, "form_menu") }
				  {else}
                    { $list->bool("affiliate_template_status", "1", "form_menu") }					
				  {/if}
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_affiliate_plugin
                    {/translate}
                  </td>
                  <td width="50%"> 
				    { $list->menu_files("", "affiliate_template_affiliate_plugin", $VAR.affiliate_template_affiliate_plugin, "affiliate_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_avail_campaign_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.affiliate_template_avail_campaign_id, "affiliate_template_avail_campaign_id", "campaign", "name", "", "5", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_max_tiers 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $VAR.affiliate_template_max_tiers == ""}
                    <input type="text" id="affiliate_template_max_tiers" name="affiliate_template_max_tiers" value="1" {if $affiliate_template_max_tiers == true}class="form_field_error"{/if} size="5" onchange="TierUpdate();">
                    {else}
                    <input type="text" id="affiliate_template_max_tiers" name="affiliate_template_max_tiers" value="{$VAR.affiliate_template_max_tiers}" {if $affiliate_template_max_tiers == true}class="form_field_error"{/if} size="5" onChange="TierUpdate();">
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_commission_minimum 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="affiliate_template_commission_minimum" value="{$VAR.affiliate_template_commission_minimum}" {if $affiliate_template_commission_minimum == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_new_commission_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="affiliate_template_new_commission_type" >
                      <option value="0"{if $VAR.affiliate_template_new_commission_type == "0"} selected{/if}> 
                      {translate module=affiliate}
                      none 
                      {/translate}
                      </option>
                      <option value="1"{if $VAR.affiliate_template_new_commission_type == "1"} selected{/if}> 
                      {translate module=affiliate}
                      percent 
                      {/translate}
                      </option>
                      <option value="2"{if $VAR.affiliate_template_new_commission_type == "2"} selected{/if}> 
                      {translate module=affiliate}
                      flat 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_recurr_commission_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="affiliate_template_recurr_commission_type" >
                      <option value="0"{if $VAR.affiliate_template_recurr_commission_type == "0"} selected{/if}> 
                      {translate module=affiliate}
                      none 
                      {/translate}
                      </option>
                      <option value="1"{if $VAR.affiliate_template_recurr_commission_type == "1"} selected{/if}> 
                      {translate module=affiliate}
                      percent 
                      {/translate}
                      </option>
                      <option value="2"{if $VAR.affiliate_template_recurr_commission_type == "2"} selected{/if}> 
                      {translate module=affiliate}
                      flat 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_recurr_max_commission_periods 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="affiliate_template_recurr_max_commission_periods" value="{$VAR.affiliate_template_recurr_max_commission_periods}" {if $affiliate_template_recurr_max_commission_periods == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"></td>
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="affiliate_template:view">
                    <input type="hidden" name="_page_current" value="affiliate_template:add">
                    <input type="hidden" name="do[]" value="affiliate_template:add">
                  </td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br>
  <iframe name="iframeNewTier" id="iframeNewTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  <iframe name="iframeRecTier" id="iframeRecTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  {literal}
  <SCRIPT LANGUAGE="JavaScript">
<!-- START

var new_array = new Array(99);
var rec_array = new Array(99);

// create the hidden fields for the tier rates
{/literal}
{ if $VAR.affiliate_template_new_commission_rate == "" }{literal}
	for(i=0; i<99; i++)
	{
		new_array[i] = '';
		rec_array[i] = '';
		document.write('<input type="hidden" id="new_'+i+'" name="affiliate_template_new_commission_rate[]" value="">');
		document.write('<input type="hidden" id="rec_'+i+'" name="affiliate_template_recurr_commission_rate[]" value="">');
	}
 	
{/literal}
{else}
	{foreach key=key item=item from=$VAR.affiliate_template_new_commission_rate} 	
			new_array[{$key}] = '{$item}';
			document.write('<input type="hidden" id="new_{$key}" name="affiliate_template_new_commission_rate[]" value="{$item}">');	
	{/foreach}
	{foreach key=key item=item from=$VAR.affiliate_template_recurr_commission_rate} 	
			rec_array[{$key}] = '{$item}';
			document.write('<input type="hidden" id="rec_{$key}" name="affiliate_template_recurr_commission_rate[]" value="{$item}">');	
	{/foreach}	
{/if}

{literal} 
function UpdateTierValueNew(id,value) { new_array[id] = value; 	document.getElementById('new_'+id).value = value; }
function UpdateTierValueRecur(id,value) { rec_array[id] = value; 	document.getElementById('rec_'+id).value = value; }
function GetTierValueNew(id) { return new_array[id]; }
function GetTierValueRecur(id) { return rec_array[id]; }
function TierUpdate()
{
	var tiers =  document.getElementById('affiliate_template_max_tiers').value; 
	if(tiers > 99) 
	{
		tiers = 99;
		document.getElementById('affiliate_template_max_tiers').value = 99;
	}
	showIFrame('iframeNewTier',300,300,'?_page=affiliate:new_tier_iframe&_escape=1&tiers='+tiers); 
	showIFrame('iframeRecTier',300,300,'?_page=affiliate:recurr_tier_iframe&_escape=1&tiers='+tiers);		 
}
TierUpdate();
//  END -->
</SCRIPT>{/literal}

</form>
