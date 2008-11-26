{ $method->exe("voip_rate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'voip_rate';
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
{foreach from=$voip_rate item=voip_rate} <a name="{$voip_rate.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_rate_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=voip_rate}
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
                    {translate module=voip_rate}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_name" value="{$voip_rate.name}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_pattern
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="voip_rate_pattern" cols="55" rows="5">{$voip_rate.pattern}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_connect_fee 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_connect_fee" value="{$voip_rate.connect_fee}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_increment_seconds 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_increment_seconds" value="{$voip_rate.increment_seconds}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_seconds_included 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_seconds_included" value="{$voip_rate.seconds_included}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_amount 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_amount" value="{$voip_rate.amount}"> (eg: 0.025 for a 2.5 cents connect charge.)
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_min 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_min" value="{$voip_rate.min}"> (0 for no minimum) {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_max
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_max" value="{$voip_rate.max}"> (-1 for no maximum) {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_type
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_type" >
                      <option value="0" {if $voip_rate.type == "0"}selected{/if}>
                      {translate module=voip_rate}
                      type_innetwork
                      {/translate}
                      </option>
                      <option value="1" {if $voip_rate.type == "1"}selected{/if}>
                      {translate module=voip_rate}
                      type_local
                      {/translate}
                      </option>
                      <option value="2" {if $voip_rate.type == "2"}selected{/if}>
                      {translate module=voip_rate}
                      type_regular
                      {/translate}
                      </option>
                      <option value="3" {if $voip_rate.type == "3"}selected{/if}>
                      {translate module=voip_rate}
                      type_default
                      {/translate}
                      </option>

                    </select>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_direction
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_direction" >
                      <option value="0" {if $voip_rate.direction == "0"}selected{/if}>
                      {translate module=voip_rate}
                      direction_inbound
                      {/translate}
                      </option>
                      <option value="1" {if $voip_rate.direction == "1"}selected{/if}>
                      {translate module=voip_rate}
                      direction_outbound
                      {/translate}
                      </option>
                      <option value="2" {if $voip_rate.direction == "2"}selected{/if}>
                      {translate module=voip_rate}
                      direction_both
                      {/translate}
                      </option>

                    </select>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_combine
                    {/translate} 
                  </td>
                  <td width="65%">
                    <select name="voip_rate_combine" >
                      <option value="0" {if $voip_rate.combine == "0"}selected{/if}>
                      {translate module=voip_rate}
                      type_no
                      {/translate}
                      </option>
                      <option value="1" {if $voip_rate.combine == "1"}selected{/if}>
                      {translate module=voip_rate}
                      type_yes
                      {/translate}
                      </option>
                    </select> {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_percall
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_perCall" >
                      <option value="0" {if $voip_rate.perCall == "0"}selected{/if}>
                      {translate module=voip_rate}
                      type_no
                      {/translate}
                      </option>
                      <option value="1" {if $voip_rate.perCall == "1"}selected{/if}>
                      {translate module=voip_rate}
                      type_yes
                      {/translate}
                      </option> 
                    </select>
                  </td>
                </tr>


                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                  <td width="65%"> 
                    <div align="right">
                      <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_rate.id}','{$VAR.id}');">
                      </div></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="voip_rate:view">
    <input type="hidden" name="voip_rate_id" value="{$voip_rate.id}">
    <input type="hidden" name="do[]" value="voip_rate:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>  
  
{/foreach}
{/if}
  

