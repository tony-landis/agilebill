
{ $method->exe("campaign","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'campaign';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
    	// Mass update, view, and delete controller
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$campaign item=campaign} 
<p> 
  <!-- Display the field validation -->
  {if $form_validation}
  { $block->display("core:alert_fields") }
  {/if}
  <!-- Display each record -->
</p>

 
<table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="3">
        <!-- DISPLAY THE SEARCH HEADING -->
        <tr valign="middle" align="right" class="table_heading"> 
          <td width="28%" class="table_heading"><b> </b></td>
          <td width="18%" class="table_heading"> 
            <div align="right"><b> 
              {translate module=campaign}
              impressions 
              {/translate}
              </b> </div>
          </td>
          <td width="18%" class="table_heading"> 
            <div align="right"><b> 
              {translate module=campaign}
              clicks 
              {/translate}
              </b> </div>
          </td>
          <td width="18%" class="table_heading"> 
            <div align="right"><b> 
              {translate module=campaign}
              cost 
              {/translate}
              </b></div>
          </td>
          <td width="18%" class="table_heading"> 
            <div align="right"><b> 
              {translate module=campaign}
              conversion
              {/translate}
              </b></div>
          </td>
          <!-- LOOP THROUGH EACH RECORD -->
          {if $campaign.served1 != 0 || $campaign.clicked1 != 0 || $campaign.file1 != ""}
        <tr class="row2"> 
          <td width="28%"> <b> <a href="#1">
            {translate module=campaign}
            add_one 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served1|number_format}
            {$campaign.impressions_percentage1}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked1|number_format}
            {$campaign.clicks_percentage1}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost1,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr1}
          </td>
        </tr>
        {/if}
        {if $campaign.served2 != 0 || $campaign.clicked2 != 0 || $campaign.file2 != ""}
        <tr class="row1"> 
          <td width="28%"> <b> <a href="#2">
            {translate module=campaign}
            add_two 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served2|number_format}
            {$campaign.impressions_percentage2}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked2|number_format}
            {$campaign.clicks_percentage2}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost2,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr2}
          </td>
        </tr>
        {/if}
        {if $campaign.served3 != 0 || $campaign.clicked3 != 0 || $campaign.file3 != ""}
        <tr class="row2"> 
          <td width="28%"> <b> <a href="#3">
            {translate module=campaign}
            add_three 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served3|number_format}
            {$campaign.impressions_percentage3}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked3|number_format}
            {$campaign.clicks_percentage3}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost3,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr3}
          </td>
        </tr>
        {/if}
        {if $campaign.served4 != 0 || $campaign.clicked4 != 0 || $campaign.file4 != ""}
        <tr class="row1"> 
          <td width="28%"> <b> <a href="#4">
            {translate module=campaign}
            add_four 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served4|number_format}
            {$campaign.impressions_percentage4}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked4|number_format}
            {$campaign.clicks_percentage4}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost4,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr4}
          </td>
        </tr>
        {/if}
        {if $campaign.served5 != 0 || $campaign.clicked5 != 0 || $campaign.file5 != ""}
        <tr class="row2"> 
          <td width="28%"><b> <a href="#5">
            {translate module=campaign}
            add_five 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served5|number_format}
            {$campaign.impressions_percentage5}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked5|number_format}
            {$campaign.clicks_percentage5}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost5,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr5}
          </td>
        </tr>
        {/if}
        {if $campaign.served6 != 0 || $campaign.clicked6 != 0 || $campaign.file6 != ""}
        <tr class="row1"> 
          <td width="28%"><b> <a href="#6">
            {translate module=campaign}
            add_six 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served6|number_format}
            {$campaign.impressions_percentage6}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked6|number_format}
            {$campaign.clicks_percentage6}
          </td>
          <td width="18%" align="right"> 
           {$list->format_currency_num($campaign.cost6,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr6}
          </td>
        </tr>
        {/if}
        {if $campaign.served7 != 0 || $campaign.clicked7 != 0 || $campaign.file7 != ""}
        <tr class="row2"> 
          <td width="28%"><b> <a href="#7">
            {translate module=campaign}
            add_seven 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served7|number_format}
            {$campaign.impressions_percentage7}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked7|number_format}
            {$campaign.clicks_percentage7}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost7,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr7}
          </td>
        </tr>
        {/if}
        {if $campaign.served8 != 0 || $campaign.clicked8 != 0 || $campaign.file8 != ""}
        <tr class="row1"> 
          <td width="28%"><b> <a href="#8">
            {translate module=campaign}
            add_eight 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served8|number_format}
            {$campaign.impressions_percentage8}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked8|number_format}
            {$campaign.clicks_percentage8}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost8,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr8}
          </td>
        </tr>
        {/if}
        {if $campaign.served9 != 0 || $campaign.clicked9 != 0 || $campaign.file9 != ""}
        <tr class="row2"> 
          <td width="28%"><b> <a href="#9">
            {translate module=campaign}
            add_nine 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served9|number_format}
            {$campaign.impressions_percentage9}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked9|number_format}
            {$campaign.clicks_percentage9}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost9,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr9}
          </td>
        </tr>
        {/if}
        {if $campaign.served10 != 0 || $campaign.clicked10 != 0 || $campaign.file10 != ""}
        <tr class="row1"> 
          <td width="28%"><b> <a href="#10">
            {translate module=campaign}
            add_ten 
            {/translate}
            </a> </b></td>
          <td width="18%" align="right"> 
            {$campaign.served10|number_format}
            {$campaign.impressions_percentage10}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked10|number_format}
            {$campaign.clicks_percentage10}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost10,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr10}
          </td>
        </tr>
        {/if}
        {if $campaign.served11 != 0 || $campaign.clicked11 != 0 || $campaign.file11 != ""}
        <tr class="row2"> 
          <td width="28%"><b> <a href="#11">
            {translate module=campaign}
            add_eleven 
            {/translate}
            </a></b></td>
          <td width="18%" align="right"> 
            {$campaign.served11|number_format}
            {$campaign.impressions_percentage11}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked11|number_format}
            {$campaign.clicks_percentage11}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost11,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr11}
          </td>
        </tr>
        {/if}
        {if $campaign.served12 != 0 || $campaign.clicked12 != 0 || $campaign.file12 != ""}
        <tr class="row1"> 
          <td width="28%"><b> <a href="#12">
            {translate module=campaign}
            add_twelve 
            {/translate}
            </a></b></td>
          <td width="18%" align="right"> 
            {$campaign.served12|number_format}
            {$campaign.impressions_percentage12}
          </td>
          <td width="18%" align="right"> 
            {$campaign.clicked12|number_format}
            {$campaign.clicks_percentage12}
          </td>
          <td width="18%" align="right"> 
            {$list->format_currency_num($campaign.cost12,"")}
          </td>
          <td width="18%" align="right"> 
            {$campaign.ctr12}
          </td>
        </tr>
        {/if}
        <tr class="table_heading"> 
          <td width="28%"><b></b></td>
          <td width="18%" align="right"> <b> 
            {$campaign.impressions_total|number_format}
            </b></td>
          <td width="18%" align="right"><b> 
            {$campaign.clicks_total|number_format}
            {$campaign.clicks_percentage3}
            </b></td>
          <td width="18%" align="right"><b> 
            {$list->format_currency_num($campaign.budget,"")}
            </b></td>
          <td width="18%" align="right"><b> 
            {$campaign.ctr_avg}
            </b></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" height="8"></td>
  </tr>
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="3" class="table_background">
        <tr class="row1"> 
          <td width="35%"><a href="?_page=core:search&module=invoice&invoice_campaign_id={$campaign.id}">
		  {translate module=campaign}referred_invoices{/translate}</a> 
            
            
          </td>
          <td width="11%"> 
            {$campaign.invoices_referred}
          </td>
          <td width="36%"><a href="?_page=core:search&module=account_admin&account_admin_campaign_id={$campaign.id}"> 
            {translate module=campaign}
            referred_accounts
            {/translate}
            </a> </td>
          <td width="18%"> 
            {$campaign.accounts_referred}
          </td>
        </tr>
        <tr class="row2"> 
          <td width="35%"> 
            {translate module=campaign}
            revenue_generated
            {/translate}
          </td>
          <td width="11%" class="row1"> 
            {$list->format_currency_num($campaign.invoices_revenue, "")}
          </td>
          <td width="36%"> 
            {translate module=campaign}
            avg_cpi
            {/translate}
          </td>
          <td width="18%" class="row1"> 
            {$list->format_currency_num($campaign.cpi_avg, "")}
          </td>
        </tr>
        <tr class="row1"> 
          <td width="35%" height="24"> 
            {translate module=campaign}
            avg_cost_conversion
            {/translate}
          </td>
          <td width="11%" height="24"> 
            {$list->format_currency_num($campaign.conversion_cost,"")}
          </td>
          <td width="36%" height="24"> 
            {translate module=campaign}
            avg_cpc
            {/translate}
          </td>
          <td width="18%" height="24"> 
            {$list->format_currency_num($campaign.cpc_avg,"")}
          </td>
        </tr>
        <tr class="row2"> 
          <td width="35%"> 
            {translate module=campaign}
            avg_sale
            {/translate}
          </td>
          <td width="11%" class="row1"> 
            {$list->format_currency_num($campaign.invoice_avg,"")}
          </td>
          <td width="36%"> 
            {translate module=campaign}
            impr_to_buy
            {/translate}
          </td>
          <td width="18%" class="row1"> 
            {$campaign.impr_to_buy}
          </td>
        </tr>
        <tr class="row1"> 
          <td width="35%"> 
            {translate module=campaign}
            roi
            {/translate}
          </td>
          <td width="11%"> 
            {$campaign.roi}
          </td>
          <td width="36%"> 
            {translate module=campaign}
            click_to_buy
            {/translate}
          </td>
          <td width="18%"> 
            {$campaign.click_to_buy}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>	
	
<br><br>
<form name="campaign_view" method="post" action="" enctype="multipart/form-data">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=campaign}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($campaign.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($campaign.date_last)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("campaign_date_start", $campaign.date_start, "form_field", $campaign.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("campaign_date_expire", $campaign.date_expire, "form_field", $campaign.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("campaign_status", $campaign.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_name" value="{$campaign.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="campaign_description" cols="40" rows="5" >{$campaign.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_budget 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_budget" value="{$campaign.budget}"  size="10">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=campaign}
                    field_url 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="campaign_url" value="{$campaign.url}"  size="48">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                  <td width="65%"> </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%" align="right"> 
                    <input type="button" name="delete2" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$campaign.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
              <br>
              <p><a name="1"></a></p>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_one 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="hidden" name="campaign_file1" value="{$campaign.file1}">
                          <input type="file" name="upload_file1"  size="38" {if $campaign_file1 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file1 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=1&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <div align="center"> 
                      <textarea name="code"  cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=1"></a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="2"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_two 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file2"  size="38">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file2 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=2&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"><a href="{$URL}?_page=campaign:view&file=2&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      </a> 
                      <textarea name="textarea"  cols="85" rows="2"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=2"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=2"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=2&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="3"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_three 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file3"  size="38">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file3 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=3&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea2"  cols="85" rows="2"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=3"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=3"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=3&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="4"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_four 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file4"  size="38" >
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file4 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=4&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea3"  cols="85" rows="2"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=4"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=4"></a></textarea>
                      <a href="?_page=campaign:view&file=4&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      <br>
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="5"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_five 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file5"  size="38" {if $campaign_file5 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file5 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=5&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea5" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=5"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=5"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=5&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="6"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_six 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file6"  size="38" {if $campaign_file6 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file6 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=6&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea6" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=6"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=6"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=6&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="7"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_seven 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file7"  size="38" {if $campaign_file7 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file7 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=7&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea7" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=7"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=7"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=7&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="8"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_eight 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file8"  size="38" {if $campaign_file8 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file8 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=8&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <div align="center" class="row1"> 
                      <textarea name="textarea8" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=8"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=8"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=8&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="9"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_nine 
                          {/translate}
                          </b></td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file9"  size="38" {if $campaign_file9 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file9 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=9&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea9" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=9"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=9"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=9&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="10"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_ten 
                          {/translate}
                          </b> </td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file10"  size="38" {if $campaign_file10 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file10 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=10&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea10" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=10"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=10"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=10&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="11"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_eleven 
                          {/translate}
                          </b> </td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file11"  size="38" {if $campaign_file11 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file11 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=11&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea11" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=11"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=11"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=11&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> <a name="12"></a> 
              <table width="100%" border="0" cellpadding="5">
                <tr class="row2"> 
                  <td class="row2"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row2">
                      <tr> 
                        <td width="34%"> <b> 
                          {translate module=campaign}
                          add_twelve 
                          {/translate}
                          </b> </td>
                        <td align="right" width="66%"> 
                          <input type="file" name="upload_file12"  size="38" {if $campaign_file12 == true}class="form_field_error"{/if}>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                {if $campaign.file12 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=12&_log=no" target="_blank" border="0"> 
                        <img src="{$URL}modules/campaign/?id={$campaign.id}&file=12&_log=no" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea12" cols="85" rows="2" readonly="true"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=12"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=12"></a></textarea>
                      <br>
                      <a href="?_page=campaign:view&file=12&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      {translate module=campaign}
                      delete_file 
                      {/translate}
                      </a> <br>
                      <br>
                      <br>
                      <a name=""></a> </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <p>&nbsp;</p>
              <p align="center"><b><br>
                </b> 
                {translate module=campaign}
                random_code 
                {/translate}
              </p>
              <p align="center"></p>
              <br>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1" align="center"> 
              <textarea name="textarea4"  cols="75" rows="5"><script language="Javascript">{literal}<!--
var currentdate = 0;
var core = 0;
function StringArray (n) {
  this.length = n;
  for (var i  = 1; i <= n; i++) {
    this[i]   = " ";
  }
}{/literal}
{counter start=0 skip=1 assign="counter"}{if $campaign.file2 != ""}{counter}{/if}{if $campaign.file3 != ""}{counter}{/if}{if $campaign.file4 != ""}{counter}{/if}{counter}
image = new StringArray({$counter});
{counter start=0 skip=1 assign="counter"} 
image[0] = '1';  
{if $campaign.file2 != ""}{counter}image[{$counter}] = '2';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '3'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '4'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '5';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '6'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '7'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '8';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '9'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '10'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '11';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '12'; 
{/if} 

var ran = 60/image.length
{literal}function ranimage() {
  currentdate = new Date()
  core = currentdate.getSeconds()
  core = Math.floor(core/ran)
  return(image[core])
}{/literal}
var fileId = ranimage(); 
var write1 = '<a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file='+fileId+ '&_escape">';
var write2 = '<img src="{$URL}modules/campaign/?id={$campaign.id}&file='+fileId+'" border="0"></a>';
document.write(write1 + "" + write2);
//--></script></textarea>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="6">
                <tr> 
                  <td> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td align="right"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$campaign.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <p>
    <input type="hidden" name="_page" value="campaign:view">
    <input type="hidden" name="campaign_id" value="{$campaign.id}">
    <input type="hidden" name="do[]" value="campaign:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </p>
  <p>&nbsp; </p>
</form>
  {/foreach}
{/if}
