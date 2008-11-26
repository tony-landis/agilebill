
{ $method->exe("affiliate_template","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'affiliate_template';
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
{foreach from=$affiliate_template item=affiliate_template}  

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="form1" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate_template}title_view{/translate}
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
                    <input type="text" name="affiliate_template_name" value="{$affiliate_template.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="affiliate_template_notes" cols="40" rows="5" >{$affiliate_template.notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("affiliate_template_status", $affiliate_template.status, "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_affiliate_plugin
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "affiliate_template_affiliate_plugin", $affiliate_template.affiliate_plugin, "affiliate_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_avail_campaign_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($affiliate_template.avail_campaign_id, "affiliate_template_avail_campaign_id", "campaign", "name", "", "5", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_max_tiers 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" id="affiliate_template_max_tiers" name="affiliate_template_max_tiers" value="{$affiliate_template.max_tiers}"  size="5" onChange="TierUpdate();">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate_template}
                    field_commission_minimum 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="affiliate_template_commission_minimum" value="{$affiliate_template.commission_minimum}"  size="5">
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
                      <option value="0"{if $affiliate_template.new_commission_type == "0"} selected{/if}> 
                      {translate module=affiliate}
                      none 
                      {/translate}
                      </option>
                      <option value="1"{if $affiliate_template.new_commission_type == "1"} selected{/if}> 
                      {translate module=affiliate}
                      percent 
                      {/translate}
                      </option>
                      <option value="2"{if $affiliate_template.new_commission_type == "2"} selected{/if}> 
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
                      <option value="0"{if $affiliate_template.recurr_commission_type == "0"} selected{/if}> 
                      {translate module=affiliate}
                      none 
                      {/translate}
                      </option>
                      <option value="1"{if $affiliate_template.recurr_commission_type == "1"} selected{/if}> 
                      {translate module=affiliate}
                      percent 
                      {/translate}
                      </option>
                      <option value="2"{if $affiliate_template.recurr_commission_type == "2"} selected{/if}> 
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
                    <input type="text" name="affiliate_template_recurr_max_commission_periods" value="{$affiliate_template.recurr_max_commission_periods}"  size="5">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    <p> 
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" id="new_0" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_1" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_2" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_3" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_4" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_5" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_6" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_7" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_8" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_9" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_10" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_11" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_12" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_13" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_14" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_15" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_16" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_17" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_18" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_19" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_20" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_21" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_22" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_23" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_24" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_25" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_26" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_27" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_28" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_29" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_30" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_31" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_32" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_33" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_34" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_35" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_36" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_37" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_38" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_39" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_40" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_41" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_42" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_43" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_44" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_45" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_46" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_47" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_48" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_49" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_50" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_51" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_52" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_53" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_54" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_55" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_56" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_57" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_58" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_59" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_60" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_61" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_62" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_63" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_64" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_65" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_66" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_67" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_68" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_69" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_70" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_71" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_72" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_73" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_74" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_75" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_76" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_77" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_78" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_79" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_80" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_81" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_82" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_83" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_84" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_85" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_86" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_87" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_88" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_89" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_90" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_91" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_92" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_93" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_94" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_95" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_96" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_97" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_98" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="new_99" name="affiliate_template_new_commission_rate[]">
                      <input type="hidden" id="rec_0" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_1" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_2" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_3" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_4" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_5" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_6" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_7" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_8" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_9" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_10" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_11" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_12" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_13" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_14" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_15" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_16" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_17" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_18" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_19" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_20" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_21" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_22" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_23" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_24" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_25" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_26" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_27" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_28" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_29" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_30" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_31" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_32" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_33" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_34" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_35" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_36" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_37" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_38" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_39" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_40" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_41" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_42" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_43" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_44" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_45" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_46" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_47" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_48" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_49" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_50" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_51" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_52" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_53" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_54" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_55" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_56" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_57" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_58" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_59" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_60" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_61" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_62" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_63" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_64" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_65" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_66" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_67" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_68" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_69" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_70" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_71" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_72" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_73" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_74" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_75" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_76" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_77" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_78" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_79" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_80" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_81" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_82" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_83" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_84" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_85" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_86" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_87" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_88" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_89" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_90" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_91" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_92" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_93" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_94" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_95" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_96" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_97" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_98" name="affiliate_template_recurr_commission_rate[]">
                      <input type="hidden" id="rec_99" name="affiliate_template_recurr_commission_rate[]">
                    </p>
                  </td>
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$affiliate_template.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="affiliate_template:view">
    <input type="hidden" name="affiliate_template_id" value="{$affiliate_template.id}">
    <input type="hidden" name="do[]" value="affiliate_template:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  
 <br>
  
  <iframe name="iframeNewTier" id="iframeNewTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
  <iframe name="iframeRecTier" id="iframeRecTier" style="border:0px; width:0px; height:0px;" scrolling="auto" ALLOWTRANSPARENCY="true" frameborder="0" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 

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
		UpdateTierValueNew(key,"{$item}") ;
	}
{/foreach}
	
{ $list->unserial($affiliate_template.recurr_commission_rate,"recurr_commission_rate") }
{foreach key=key item=item from=$recurr_commission_rate} 	
	var key = {$key};
	{literal}if (key <= 10)
	{ {/literal}
		rec_array[key] = '{$item}';
		UpdateTierValueRecur(key,"{$item}");	
	}
{/foreach}	
 
{literal} 
function UpdateTierValueNew(id,value)   { new_array[id] = value; 	document.getElementById('new_'+id).value = value; }
function UpdateTierValueRecur(id,value) { rec_array[id] = value; 	document.getElementById('rec_'+id).value = value; }
function GetTierValueNew(id)            { return new_array[id]; }
function GetTierValueRecur(id)          { return rec_array[id]; }
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
  
  {/foreach}
{/if}
