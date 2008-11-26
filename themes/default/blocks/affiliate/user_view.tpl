 
<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="affiliate_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=affiliate}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_id 
                    {/translate}
                  </td>
                  <td width="50%"> <b> 
                    {$affiliate.id}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->date_time($affiliate.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->date_time("")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $affiliate.status == "1"}
                    {translate}
                    true
                    {/translate}
                    {else}
                    {translate}
                    false 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_max_tiers 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$affiliate.max_tiers}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_commission_minimum 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->format_currency($affiliate.commission_minimum, "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_recurr_max_commission_periods 
                    {/translate}
                  </td>
                  <td width="50%">{$affiliate.recurr_max_commission_periods} </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_new_commission_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $affiliate.new_commission_type == "0"}
                    {translate module=affiliate}
                    none 
                    {/translate}
                    {/if}
                    {if $affiliate.new_commission_type == "1"}
                    {translate module=affiliate}
                    percent 
                    {/translate}
                    {/if}
                    {if $affiliate.new_commission_type == "2"}
                    {translate module=affiliate}
                    flat 
                    {/translate}
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_recurr_commission_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $affiliate.recurr_commission_type == "0"}
                    {translate module=affiliate}
                    none 
                    {/translate}
                    {/if}
                    {if $affiliate.recurr_commission_type == "1"}
                    {translate module=affiliate}
                    percent 
                    {/translate}
                    {/if}
                    {if $affiliate.recurr_commission_type == "2"}
                    {translate module=affiliate}
                    flat 
                    {/translate}
                    {/if}
                  </td>
                </tr>
			  </table>
			  <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="50%" valign="top"> 
                    {$record.name}
                  </td>
                  <td width="50%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
			  </table>
			  <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr valign="top"> 
                  <td width="50%" valign="top"> 
                    {translate module=affiliate}
                    field_affiliate_plugin
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "affiliate_affiliate_plugin", $affiliate.affiliate_plugin, "affiliate_plugin", "", ".php", "") }
                  </td>
                </tr>
              </table>
              {assign var="afile" 	value=$affiliate.affiliate_plugin}
              {assign var="ablock" 	value="affiliate:plugin_"}
              {assign var="blockfile" value="$ablock$afile"}
              { $block->display($blockfile) }
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="hidden" name="_page" value="affiliate:affiliate">
                          <input type="hidden" name="_page_current" value="affiliate:affiliate">
                          <input type="hidden" name="do[]" value="affiliate:user_update">
                          <input type="hidden" name="affiliate_date_last" value="{$smarty.now}">
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                      </tr>
                    </table>
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
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=affiliate}
                title_statistics 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    sessions 
                    {/translate}
                  </td>
                  <td width="50%"> <b> 
                    {$affiliate_stats.stats_sessions}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    accounts 
                    {/translate}
                  </td>
                  <td width="50%"><b> 
                    {$affiliate_stats.stats_accounts}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    invoices 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->format_currency($affiliate_stats.stats_invoices_amt, '')}
                    ( 
                    {$affiliate_stats.stats_invoices}
                    ) &nbsp;&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    commissions 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->format_currency($affiliate_stats.stats_commissions, '')}
                     </td>
                </tr>
                <tr valign="top">
                  <td width="50%">
                    {translate module=affiliate}
                    commissions_due 
                    {/translate}
                  </td>
                  <td width="50%"><b>
                    {$list->format_currency($affiliate_stats.commissions_due, '')}
                    </b></td>
                </tr>
                {foreach from=$affiliate.static_var item=record}
                {/foreach}
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  {if $affiliate_campaign != ""}
  <br>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=affiliate}
                title_campaigns 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
			  
			  <!-- Loop through each record -->
			  {foreach from=$affiliate_campaign item=affiliate_campaign}  
			  
                <tr valign="top"> 
                  <td width="50%">
                    {$affiliate_campaign.name}
                  </td>
                  <td width="50%"> <a href="?_page=campaign:affiliate&id={$affiliate_campaign.id}&curr_aid={$affiliate.id}"> 
                    {translate module=affiliate}
                    campaign_code 
                    {/translate}
                    </a> </td>
                </tr> 
                {/foreach}
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {/if}
 </form> 
 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr valign="top"> 
    <td width="50%"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
            <table width="97%" border="0" cellspacing="1" cellpadding="0" class="table_background">
              <tr valign="top"> 
                <td width="65%" class="table_heading"> 
                  <div align="center"> 
                    {translate module=affiliate}
                    field_new_commission_rate 
                    {/translate}
                  </div>
                </td>
              </tr>
              <tr valign="top"> 
                <td width="65%" class="row1">                                      
                  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
                    { $list->unserial($affiliate.new_commission_rate,"new_commission_rate") }
                    {foreach key=key item=item from=$new_commission_rate}
                    {if $item}
                    <tr> 
                      <td width="50%"> 
                        {translate module=affiliate}tier{/translate} {math equation="x+y" x=$key y=1}
                      </td>
                      <td width="50%" align="right"> 
                        {if $affiliate.new_commission_type == "0"}
                        {elseif $affiliate.new_commission_type == "1"}
                        {math equation="x*y" x=$item y=100}
                        % 
                        {elseif $affiliate.new_commission_type == "2"}
                        {$list->format_currency($item, '')}
                        {/if}
                      </td>
                    </tr>
				  {/if}{/foreach}
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td width="50%" align="right"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td align="right"> 
            <table width="97%" border="0" cellspacing="1" cellpadding="0" class="table_background">
              <tr valign="top"> 
                <td width="65%" class="table_heading"> 
                  <div align="center"> 
                    {translate module=affiliate}
                    field_recurr_commission_rate 
                    {/translate}
                  </div>
                </td>
              </tr>
              <tr valign="top"> 
                <td width="65%" class="row1"> 
                
				  
				  <table width="100%" border="0" cellspacing="0" cellpadding="3" class="row1">
                    { $list->unserial($affiliate.recurr_commission_rate,"recurr_commission_rate") }
                    {foreach key=key item=item from=$recurr_commission_rate}
                    {if $item}
                    <tr> 
                      <td width="50%"> 
                        {translate module=affiliate}tier{/translate} {math equation="x+y" x=$key y=1}
                      </td>
                      <td width="50%" align="right"> 
                        {if $affiliate.recurr_commission_type == "0"}
                        {elseif $affiliate.recurr_commission_type == "1"}
                        {math equation="x*y" x=$item y=100}
                        % 
                        {elseif $affiliate.recurr_commission_type == "2"}
                        {$list->format_currency($item, '')}
                        {/if}
                      </td>
                    </tr>
				  {/if}{/foreach}
                  </table>
                
								  
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
