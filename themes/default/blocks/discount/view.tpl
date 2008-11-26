{ $method->exe("discount","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'discount';
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
{foreach from=$discount item=discount} <a name="{$discount.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="discount_view" name="discount_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=discount}
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
                    {translate module=discount}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->date_time($discount.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->calender_view("discount_date_start", $discount.date_start, "form_field", $discount.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->calender_view("discount_date_expire", $discount.date_expire, "form_field", $discount.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("discount_status", $discount.status, "onChange=\"submit()\"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_name" value="{$discount.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="discount_notes" cols="40" rows="5" >{$discount.notes}</textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b>
                      {translate module=discount}
                      discount_restrictions
                      {/translate}
                      </b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_max_usage_account 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_max_usage_account" value="{$discount.max_usage_account}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_max_usage_global 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_max_usage_global" value="{$discount.max_usage_global}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_account_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {html_select_account name="discount_avail_account_id" default=$discount.avail_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_product_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($discount.avail_product_id, "discount_avail_product_id", "product", "sku", "", "10", "form_menu") }
                  </td>
                </tr>
				{if $list->is_installed("host_tld")}
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_tld_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
					{ $list->menu_multi($discount.avail_tld_id, "discount_avail_tld_id", "host_tld", "name", "", "10", "form_menu") }
                  </td>
                </tr>
				{/if}				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_group_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($discount.avail_group_id, 'discount_avail_group_id', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b> 
                      {translate module=discount}
                      discount_new 
                      {/translate}
                      </b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_status 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    { $list->bool("discount_new_status", $discount.new_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_type 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <select name="discount_new_type" >
                      <option value="0"{if $discount.new_type == "0"} selected{/if}>
                      {translate module=discount}
					  	percent
                      {/translate}
                      </option>
                      <option value="1"{if $discount.new_type == "1"} selected{/if}>
                      {translate module=discount}
					  	flat
                      {/translate}
                      </option>					  
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_rate 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_rate" value="{$discount.new_rate}"  size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_max_discount 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_max_discount" value="{$discount.new_max_discount}"  size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_min_cost 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_min_cost" value="{$discount.new_min_cost}"  size="12">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b> 
                      {translate module=discount}
                      discount_recurr 
                      {/translate}
                      </b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_status 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    { $list->bool("discount_recurr_status", $discount.recurr_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_type 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <select name="discount_recurr_type" >
                      <option value="0"{if $discount.recurr_type == "0"} selected{/if}>
                      {translate module=discount}
					  	percent
                      {/translate}
                      </option>
                      <option value="1"{if $discount.recurr_type == "1"} selected{/if}>
                      {translate module=discount}
					  	flat
                      {/translate}
                      </option>					
                    </select>				  
				  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_rate 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_rate" value="{$discount.recurr_rate}"  size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_max_discount 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_max_discount" value="{$discount.recurr_max_discount}"  size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_min_cost 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_min_cost" value="{$discount.recurr_min_cost}"  size="12">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="65%"> 
                    <div align="right">
                      <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$discount.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="discount:view">
    <input type="hidden" name="discount_id" value="{$discount.id}">
    <input type="hidden" name="do[]" value="discount:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
