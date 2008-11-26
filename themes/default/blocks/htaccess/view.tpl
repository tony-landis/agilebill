
{ $method->exe("htaccess","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'htaccess';
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
{foreach from=$htaccess item=htaccess} <a name="{$htaccess.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="htaccess_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=htaccess}
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
                    {translate module=htaccess}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="htaccess_name" value="{$htaccess.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="htaccess_description" cols="40" rows="5" >{$htaccess.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("htaccess_status", $htaccess.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=htaccess}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->select_groups($htaccess.group_avail,"htaccess_group_avail","form_field","10","") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$htaccess.id}','{$VAR.id}');">
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
              <table width="100%" border="0" cellpadding="3" class="row1">
                <tr> 
                  <td><a href="?_page=core:search&module=htaccess_dir&htaccess_dir_htaccess_id={$htaccess.id}">
                    {translate module=htaccess}
                    view_all 
                    {/translate}
                    </a></td>
                  <td> 
                    <div align="right"><a href="?_page=htaccess_dir:add&id={$htaccess.id}">
                      {translate module=htaccess}
                      add_directory 
                      {/translate}
                      </a></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellpadding="3" class="row1">
                <tr> 
                  <td> 
                    <div align="center"><a href="?_page=core:search&module=htaccess_dir&htaccess_htaccess_id={$htaccess.id}"> 
                      </a> 
                      <textarea name="textarea" cols="80" rows="8" ><?php
#### START COOKIE AUTHENTICATION:      ####
define('_RETURN_URL', 'URL of page this code is inserted into...');
define('_HTACCESS_ID', '{$htaccess.id}');
require_once('{$smarty.const.PATH_AGILE}cookie.index.php');
error_reporting(0);
#### END  COOKIE AUTHENTICATION        ####
?></textarea>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <div align="center"> 
                      <textarea name="textarea2" cols="80" rows="4" >{literal}{group id={/literal}{$htaccess.id}{literal} msg="Sorry, you are not authorized for this area"}
Your html, javascript, images, etc., are placed here for protection within the smarty templates...
{/group}{/literal}</textarea>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td align="center"> 
                    {$list->unserial($htaccess.group_avail, "groups")}
                    {assign var="idx" value=0}
                    <textarea name="textarea4" cols="80" rows="6" ># .htaccess for local/remote Apache servers with mod_auth_mysql enabled
AuthName "{$htaccess.name}"
AuthType Basic
require group 1
AuthMySQLHost {$smarty.const.AGILE_DB_HOST}
AuthMySQLDB {$smarty.const.AGILE_DB_DATABASE}
AuthMySQLUser {$smarty.const.AGILE_DB_USERNAME}
AuthMySQLPassword ********
AuthMySQLUserTable "{$smarty.const.AGILE_DB_PREFIX}account as A,{$smarty.const.AGILE_DB_PREFIX}account_group as B"
AuthMySQLNameField username
AuthMySQLPasswordField password
AuthMySQLGroupField status
AuthMySQLMD5Passwords On
AuthMySQLNoPasswd Off
AuthMySQLUserCondition "( A.date_expire > UNIX_TIMESTAMP(NOW()) OR A.date_expire <= 0 OR A.date_expire IS NULL ) AND B.account_id = A.id AND ({foreach from=$groups item=arrgr}{if $idx != 0}OR {else}{assign var="idx" value=1}{/if}B.group_id = {$arrgr} {/foreach})"
</textarea>
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <textarea name="textarea3" cols="80" rows="6" ># .htaccess for local/remote Apache servers with mod_auth_remote enabled
AuthType	Basic
AuthName	{$htaccess.name}
AuthRemoteServer	yourdomain.com
AuthRemotePort	80
AuthRemoteURL	/agilebill/includes/files/htaccess_{$htaccess.id}/
require	valid-user

</textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="htaccess:view">
    <input type="hidden" name="htaccess_id" value="{$htaccess.id}">
    <input type="hidden" name="do[]" value="htaccess:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
