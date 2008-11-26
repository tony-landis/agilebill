

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="host_registrar_plugin_add" name="host_registrar_plugin_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=host_registrar_plugin}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_registrar_plugin_status", $VAR.host_registrar_plugin_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_registrar_plugin_name" value="{$VAR.host_registrar_plugin_name}" {if $host_registrar_plugin_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
			  
		{assign var=thistype 	value="add"}
        {assign var="afile" 	value=$VAR.host_registrar_plugin_file}
        {assign var="ablock" 	value="host_registrar_plugin:plugin_cfg_"}
		{assign var="blockfile" value="$ablock$afile"}
		 
					   
			{ $block->display($blockfile) } 
			
			  <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"></td>
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="host_registrar_plugin:view">
                    <input type="hidden" name="_page_current" value="host_registrar_plugin:add">
                    <input type="hidden" name="do[]" value="host_registrar_plugin:add">
                    <input type="hidden" name="host_registrar_plugin_file" value="{$VAR.host_registrar_plugin_file}">
                  </td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
