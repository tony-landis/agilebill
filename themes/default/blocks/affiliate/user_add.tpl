<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values --> 
<form id="affiliate_add" name="affiliate_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
                {translate module=affiliate}
                title_user_add 
                {/translate}
              </center>
          </td>
        </tr>
        <tr valign="top">
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=affiliate}
                    user_add_instruct
                    {/translate}
                    <br>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                </tr>
              </table>
        
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="55%"> 
                    {translate module=affiliate}
                    field_max_tiers 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    {$affiliate_template.max_tiers}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="55%"> 
                    {translate module=affiliate}
                    field_commission_minimum 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    { $list->format_currency($affiliate_template.commission_minimum,"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="55%" height="20"> 
                    {translate module=affiliate}
                    field_new_commission_type 
                    {/translate}
                  </td>
                  <td width="45%" height="20"> 
                    {if $affiliate_template.new_commission_type == "0"}
                    {translate module=affiliate}
                    none 
                    {/translate}
                    {/if}
                    {if $affiliate_template.new_commission_type == "1"}
                    {translate module=affiliate}
                    percent 
                    {/translate}
                    {/if}
                    {if $affiliate_template.new_commission_type == "2"}
                    {translate module=affiliate}
                    flat 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="55%"> 
                    {translate module=affiliate}
                    field_recurr_commission_type 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    {if $affiliate_template.recurr_commission_type == "0"}
                    {translate module=affiliate}
                    none 
                    {/translate}
                    {/if}
                    {if $affiliate_template.recurr_commission_type == "1"}
                    {translate module=affiliate}
                    percent 
                    {/translate}
                    {/if}
                    {if $affiliate_template.recurr_commission_type == "2"}
                    {translate module=affiliate}
                    flat 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="55%"> 
                    {translate module=affiliate}
                    field_affiliate_plugin
                    {/translate}
                  </td>
                  <td width="45%"> 
                    { $list->menu_files("", "affiliate_affiliate_plugin", $VAR.affiliate_affiliate_plugin, "affiliate_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="29%"> 
                    {$record.name}
                  </td>
                  <td width="71%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
                <tr valign="top"> 
                  <td width="55%"></td>
                  <td width="45%"> 
                    <div align="right"> 
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="affiliate:affiliate">
                      <input type="hidden" name="_page_current" value="affiliate:affiliate">
                      <input type="hidden" name="do[]" value="affiliate:user_add">
                    </div>
                  </td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td align="left"> <iframe name="iframeNewTier" id="iframeNewTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
      </td>
      <td align="right"> <iframe name="iframeRecTier" id="iframeRecTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
      </td>
    </tr>
  </table>
  {literal}
  <SCRIPT LANGUAGE="JavaScript">
<!-- START

var new_array = new Array(99);
var rec_array = new Array(99);

// create the hidden fields for the tier rates
for(i=0; i<99; i++)
{
	new_array[i] = '';
	rec_array[i] = ''; 
}
{/literal}
 

{ $list->unserial($affiliate_template.new_commission_rate,"new_commission_rate") }
{foreach key=key item=item from=$new_commission_rate}
	var key = {$key};
	{literal}if (key <= 10)
	{ {/literal}
		new_array[key] = '{$item}'; 
	}
{/foreach}
	
{ $list->unserial($affiliate_template.recurr_commission_rate,"recurr_commission_rate") }
{foreach key=key item=item from=$recurr_commission_rate} 	
	var key = {$key};
	{literal}if (key <= 10)
	{ {/literal}
		rec_array[key] = '{$item}'; 	
	}
{/foreach}	
 
{literal} 
function UpdateTierValueNew(id,value)   { new_array[id] = value; 	document.getElementById('new_'+id).value = value; }
function UpdateTierValueRecur(id,value) { rec_array[id] = value; 	document.getElementById('rec_'+id).value = value; }
function GetTierValueNew(id)            { return new_array[id]; }
function GetTierValueRecur(id)          { return rec_array[id]; }
function TierUpdate()
{
	var tiers = "{/literal}{$affiliate_template.max_tiers}{literal}"; 
	var newc = "{/literal}{$affiliate_template.new_commission_type}{literal}"; 
	var recc = "{/literal}{$affiliate_template.recurr_commission_type}{literal}"; 
	
	if(tiers > 99) 
	{
		tiers = 99;
		document.getElementById('affiliate_max_tiers').value = 99;
	}
	if(newc != "0")
	showIFrame('iframeNewTier',250,255,'?_page=affiliate:user_new_tier_iframe&_escape=1&tiers='+tiers); 
	if(recc != "0")
	showIFrame('iframeRecTier',250,255,'?_page=affiliate:user_recurr_tier_iframe&_escape=1&tiers='+tiers);		 
}
TierUpdate();
//  END -->
</SCRIPT>{/literal}
  
  </form>
