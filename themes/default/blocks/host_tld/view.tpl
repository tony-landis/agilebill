
{ $method->exe("host_tld","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'host_tld';
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
{foreach from=$host_tld item=host_tld} <a name="{$host_tld.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="host_tld_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=host_tld}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_tld_status", $host_tld.status, "onChange=\"submit()\"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_tld_name" value="{$host_tld.name}"  size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_whois_plugin 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "host_tld_whois_plugin", $host_tld.whois_plugin, "whois_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
			 </table> 
			  {assign var="ablock" 	   value="host_whois_plugin:plugin_cfg_"}
              {assign var="afile"      value=$host_tld.whois_plugin} 
              {assign var="blockfile"  value="$ablock$afile"}
              { $block->display($blockfile) }
           
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_registrar_plugin_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "host_tld_registrar_plugin_id", "host_registrar_plugin", "name", $host_tld.registrar_plugin_id, "form_menu") }
                  </td>
                </tr>
                
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_taxable 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_tld_taxable", $host_tld.taxable, "form_menu") }
                  </td>
                </tr>				
				<tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_auto_search 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_tld_auto_search", $host_tld.auto_search, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_tld}
                    field_default_term_new 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>
                          <input type="text" name="host_tld_default_term_new" value="{$host_tld.default_term_new}"  size="5">
                          {$list->unserial($host_tld.price_group, "price")}
                        </td>
                        <td>
                          <div align="right">
                            <input type="submit" name="Submit22" value="{translate}submit{/translate}" class="form_button">
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    parking 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[0][show]", $price[0].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              { if ($list->smarty_array("group","name"," AND pricing='1' ", "group_array")) }
              {/if}
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[0][{$arr.id}][register]" value="{$price[0][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%">&nbsp; </td>
                  <td width="20%">&nbsp; </td>
                </tr>
              </table>
              {/foreach} 
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year1 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[1][show]", $price[1].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
			  <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[1][{$arr.id}][register]" value="{$price[1][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[1][{$arr.id}][renew]" value="{$price[1][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[1][{$arr.id}][transfer]" value="{$price[1][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
               
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year2 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[2][show]", $price[2].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[2][{$arr.id}][register]" value="{$price[2][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[2][{$arr.id}][renew]" value="{$price[2][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[2][{$arr.id}][transfer]" value="{$price[2][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year3 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[3][show]", $price[3].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[3][{$arr.id}][register]" value="{$price[3][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[3][{$arr.id}][renew]" value="{$price[3][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[3][{$arr.id}][transfer]" value="{$price[3][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year4 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[4][show]", $price[4].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[4][{$arr.id}][register]" value="{$price[4][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[4][{$arr.id}][renew]" value="{$price[4][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[4][{$arr.id}][transfer]" value="{$price[4][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year5 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[5][show]", $price[5].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[5][{$arr.id}][register]" value="{$price[5][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[5][{$arr.id}][renew]" value="{$price[5][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[5][{$arr.id}][transfer]" value="{$price[5][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year6 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[6][show]", $price[6].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[6][{$arr.id}][register]" value="{$price[6][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[6][{$arr.id}][renew]" value="{$price[6][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[6][{$arr.id}][transfer]" value="{$price[6][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"><b> </b> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year7 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[7][show]", $price[7].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[7][{$arr.id}][register]" value="{$price[7][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[7][{$arr.id}][renew]" value="{$price[7][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[7][{$arr.id}][transfer]" value="{$price[7][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"><b> </b> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year8 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[8][show]", $price[8].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[8][{$arr.id}][register]" value="{$price[8][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[8][{$arr.id}][renew]" value="{$price[8][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[8][{$arr.id}][transfer]" value="{$price[8][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"><b> </b> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year9 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[9][show]", $price[9].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[9][{$arr.id}][register]" value="{$price[9][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[9][{$arr.id}][renew]" value="{$price[9][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[9][{$arr.id}][transfer]" value="{$price[9][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"><b> </b> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
                <tr> 
                  <td class="table_heading"> <b> 
                    {translate module=host_tld}
                    year10 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("host_tld_price_group[10][show]", $price[10].show, "form_menu") }
                    </b> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> </b> </td>
                  <td width="20%"> Registrations</td>
                  <td width="20%"> Renewals</td>
                  <td width="20%"> Transfers</td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="30%"> <b> 
                    {$arr.name}
                    </b> </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[10][{$arr.id}][register]" value="{$price[10][$idx].register}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[10][{$arr.id}][renew]" value="{$price[10][$idx].renew}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="20%"> 
                    <input type="text" name="host_tld_price_group[10][{$arr.id}][transfer]" value="{$price[10][$idx].transfer}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
			  
			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="33%">
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="67%"> 
                    <div align="right">
                      <input type="button" name="delete2" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$host_tld.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="host_tld:view">
    <input type="hidden" name="host_tld_id" value="{$host_tld.id}">
    <input type="hidden" name="do[]" value="host_tld:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
