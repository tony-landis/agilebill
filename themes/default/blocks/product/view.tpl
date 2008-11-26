
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'product';
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
{foreach from=$product item=product} <a name="{$product.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="product_view" name="product_view" method="post" action="" enctype="multipart/form-data">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=product}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_date_orig 
                    {/translate}
                    </b></td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_date_last 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {$list->date($product.date_orig)}
                  </td>
                  <td width="50%"> 
                    {$list->date($product.date_last)}
                    <input type="hidden" name="product_date_last" value="{$smarty.now}">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_sku 
                    {/translate}
                    </b> </td>
                  <td width="50%"><b> 
                    {translate module=product}
                    field_position 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="50%"> 
                    <input type="text" name="product_sku" value="{$product.sku}"  size="32">
                  </td>
                  <td width="50%">
                    <input type="text" name="product_position" value="{$product.position}"  size="3">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_active 
                    {/translate}
                    </b> </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_taxable 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    { $list->bool("product_active", $product.active, "form_menu") }
                  </td>
                  <td width="50%"> 
                    { $list->bool("product_taxable", $product.taxable, "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    </b> </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="text" name="product_price_base" value="{$product.price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="product_price_setup" value="{$product.price_setup}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_avail_category_id
                    {/translate}
                    </b><br>
                  </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_group_avail 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">{$method->exe_noauth("product_cat","admin_menu_product")}</td>
                  <td width="50%"> 
                    { $list->menu_multi($product.group_avail, 'product_group_avail', 'group', 'name', '', '5', 'form_menu') }
                  </td>
                </tr>
              </table> 
			  {if $product.price_type == "1"} 
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 			  
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"><b> 
                    {translate module=product}
                    field_price_recurr_default 
                    {/translate}
                    </b></td>
                  <td width="50%"> 
                    <select name="product_price_recurr_default" >
                      <option value="0" {if $product.price_recurr_default == "0"} selected{/if}> 
                      {translate module=product}
                      recurr_week 
                      {/translate}
                      </option>
                      <option value="1" {if $product.price_recurr_default == "1"} selected{/if}> 
                      {translate module=product}
                      recurr_month 
                      {/translate}
                      </option>
                      <option value="2" {if $product.price_recurr_default == "2"} selected{/if}> 
                      {translate module=product}
                      recurr_quarter 
                      {/translate}
                      </option>
                      <option value="3" {if $product.price_recurr_default == "3"} selected{/if}> 
                      {translate module=product}
                      recurr_semianual 
                      {/translate}
                      </option>
                      <option value="4" {if $product.price_recurr_default == "4"} selected{/if}> 
                      {translate module=product}
                      recurr_anual 
                      {/translate}
                      </option>
                      <option value="5" {if $product.price_recurr_default == "5"} selected{/if}> 
                      {translate module=product}
                      recurr_twoyear 
                      {/translate}
                      </option>
                      <option value="6" {if $product.price_recurr_default == "6"} selected{/if}> 
                      {translate module=product}
                      recurr_threeyear 
                      {/translate}
                      </option>					  
                    </select>
                  </td>
                </tr>
                <tr valign="top" class="row2"> 
                  <td width="50%"><b> 
                    {translate module=product}
                    field_price_recurr_type 
                    {/translate}
                    </b></td>
                  <td width="50%"><b> 
                    {translate module=product}
                    user_options 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top" class="row2"> 
                  <td width="50%"> 
                    <input type="radio" name="product_price_recurr_type" value="0" {if $product.price_recurr_type == "0"}checked{/if}>
                    {translate module=product}
                    recurr_type_aniv 
                    {/translate}
                    <br>
                    <input type="radio" name="product_price_recurr_type" value="1" {if $product.price_recurr_type == "1"}checked{/if}>
                    {translate module=product}
                    recurr_type_fixed 
                    {/translate}
                    <br>
                    {translate module=product}
                    field_price_recurr_weekday 
                    {/translate}
                    <input type="text" name="product_price_recurr_weekday" value="{$product.price_recurr_weekday}"  size="2" maxlength="2">
                    (1-28) </td>
                  <td width="50%"> 
                    { $list->bool("product_price_recurr_schedule", $product.price_recurr_schedule, "form_menu") }
                    {translate module=product}
                    field_price_recurr_schedule 
                    {/translate}
                    <br>
                    { $list->bool("product_price_recurr_cancel", $product.price_recurr_cancel, "form_menu") }
                    {translate module=product}
                    field_price_recurr_cancel 
                    {/translate}
                    <br>
                    { $list->bool("product_cart_multiple", $product.cart_multiple, "form_menu") }
                    {translate module=product}
                    field_cart_multiple
                    {/translate}
                  </td>
                </tr>
              </table>
			  {/if}
			  

			  {if $product.price_type == "2"}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 			  
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_price_trial_prod 
                    {/translate}
                    </b><br>
                  </td>
                  <td width="50%"> <b> 
                    {translate module=product}
                    field_price_trial_length 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    { $list->menu("", "product_price_trial_prod", "product", "sku", $product.price_trial_prod, "form_menu") }
                  </td>
                  <td width="50%">
                    <input type="text" name="product_price_trial_length" value="{$product.price_trial_length}"  size="4">
                    <select name="product_price_trial_length_type" >
                      <option value="0" {if $product.price_trial_length_type == "0"} selected{/if}>{translate}day{/translate}</option>
                      <option value="1" {if $product.price_trial_length_type == "1"} selected{/if}>{translate}week{/translate}</option>
                      <option value="2" {if $product.price_trial_length_type == "2"} selected{/if}>{translate}month{/translate}</option>
                    </select>
                  </td>
                </tr>
              </table>
			  {/if}
			  
			  			  
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%" valign="top"><b> 
                    {if $product.thumbnail != ""}
                    <img src="{$URL}{$smarty.const.URL_IMAGES}{$product.thumbnail}"> 
                    &nbsp;&nbsp;&nbsp; 
                    {/if}
                    {translate module=product}
                    field_thumbnail 
                    {/translate}
                    &nbsp;&nbsp;&nbsp; </b> </td>
                  <td width="50%" align="left"><b> 
                    <input type="file" name="upload_file1" size="38" >
                    {if $product.thumbnail != ""}
                    <img title="Delete Thumbnail Image" src="themes/{$THEME_NAME}/images/icons/del_16.gif" onClick="document.getElementById('delimg').value = '1'; document.getElementById('product_view').submit();">
                    {/if}
                    </b></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1">
                <tr class="row2"> 
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">                    
                  </td>
                  <td align="right" width="50%"> 
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$product.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%" align="center"> 
                    <!-- feature inactive
				  <a href="javascript:showImages();">
				  {translate module=product}manage_images{/translate}</a> | 
				  -->
                    <a href="javascript:viewTranslations()">Name &amp; Description</a> 
                    | <a href="javascript:showAttributes();"> 
                    {translate module=product}
                    attributes 
                    {/translate}
                    </a> | <a href="javascript:showBilling();"> 
                    {translate module=product}
                    advanced_billing 
                    {/translate}
                    </a> | <a href="javascript:showAssociations();"> 
                    {translate module=product}
                    associations 
                    {/translate}
                    </a> | <a href="javascript:showDiscounts();"> 
                    {translate module=product}
                    discounts 
                    {/translate}
                    </a> 
                    {if $list->is_installed("host_server")}
                    | <a href="javascript:showHosting();"> 
                    {translate module=product}
                    hosting 
                    {/translate}
                    </a> 
                    {/if}
                    | <a href="javascript:showPlugins();"> 
                    {translate module=product}
                    product_plugins 
                    {/translate}
                    </a> | <a href="javascript:showLink('{$product.id}', '{$product.price_recurr_default}')"> 
                    {translate module=product}
                    link 
                    {/translate}
                    </a>| <a href="javascript:clone('{$product.id}')"> 
                    {translate module=product}
                    clone 
                    {/translate}
                    </a> </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="product:view">
    <input type="hidden" name="product_id" value="{$product.id}">
    <input type="hidden" name="do[]" value="product:update">
    <input type="hidden" name="id" value="{$VAR.id}">
    <input type="hidden" id="delimg" name="delimg" value="0">
	<input type="hidden" name="product_assoc_req_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_group" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_price_group" value="IGNORE-ARRAY-VALUE"> 
	<input type="hidden" name="product_host_discount_tld" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_host_provision_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_prod_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_modify_product_arr" value="IGNORE-ARRAY-VALUE">  
</form>

<CENTER><iframe name="iframeProduct" id="iframeProduct" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> </CENTER>
<div id="code"></div>
  
{literal}
<SCRIPT LANGUAGE="JavaScript">
<!-- START
 
var product_id 	= {/literal}{$product.id}{literal}; 

function viewTranslations(product_id,language_id) 
{
	document.getElementById("code").innerHTML = "";
	var product_id 	= {/literal}{$product.id}{literal};
	var language_id = {/literal}'{$smarty.const.DEFAULT_LANGUAGE}'{literal};
	var url = '?_page=core:search_iframe&module=product_translate&product_translate_language_id='+
			   language_id+'&product_translate_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=product_translate_product_id&val_id1='
			   +product_id+'&name_id2=product_translate_language_id&val_id2='+language_id; 
	showIFrame('iframeProduct',getPageWidth(600),300,url);
}
viewTranslations();
 

function  showLink(product_id, recurr_schedule)
{ 
	showIFrame('iframeProduct',0,0,'?_page=core:blank');
	var code;
	code = "<br>Add to cart and checkout:<br><textarea cols=120 rows=2 class=form_field>"+
			"{/literal}{$URL}{literal}?_page=checkout:checkout&_next_page=checkout:checkout&do[]=cart:add&product_id="+product_id+"&recurr_schedule="+recurr_schedule+ 
			"</textarea>";
	code += "<br><br>Add to cart and view cart:<br><textarea cols=120 rows=2 class=form_field>"+
		 	"{/literal}{$URL}{literal}?_page=cart:cart&do[]=cart:add&product_id="+product_id+"&recurr_schedule="+recurr_schedule+
			"</textarea>";			
	document.getElementById("code").innerHTML = code; 
} 

function  showImages()
{ 
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=core:search_iframe&module=product_img&product_img_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=product_img_product_id&val_id1='
			+product_id);		 
} 

function  showAttributes()
{ 
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=core:search_iframe&module=product_attr&product_attr_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=product_attr_product_id&val_id1='
			+product_id+'&name_id2=product_attribute_product_price_type&val_id2={/literal}{$product.price_type}{literal}');		 
}

function showBilling()
{
	document.getElementById("code").innerHTML = "";
	var billingtype = {/literal}{$product.price_type}{literal}; 	
	if(billingtype == "0" || billingtype == "2") 
		showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_price_onetime&_escape=1&id='+product_id); 
	else if(billingtype == "1") 
		showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_price_recurring&_escape=1&id='+product_id); 
}

function showAssociations()
{
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_associations&_escape=1&id='+product_id);
}

function showDiscounts()
{
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_discounts&_escape=1&id='+product_id);
}

function showHosting()
{
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_hosting&_escape=1&id='+product_id);
}

function showPlugins()
{
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_plugins&_escape=1&id='+product_id);
}

function clone()
{
	document.getElementById("code").innerHTML = "";
	showIFrame('iframeProduct',getPageWidth(600),300,'?_page=product:iframe_clone&_escape=1&id='+product_id);
}
//  END -->
</SCRIPT>{/literal}

  {/foreach}
{/if}
