

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

{ if $success_add == true }

<!-- Display the Backup Iframe -->
<iframe name="iframeBackup" id="iframeBackup" style="border:0px; width:0px; height:0px" scrolling="no" frameborder="0" ALLOWTRANSPARENCY="true"></iframe> 
 
<SCRIPT LANGUAGE="JavaScript">{literal}
var limit = 500;
var offset = 0;
function Backup(module_id,backup_id,offset)
{	
	showIFrame('iframeBackup',600,300,'?_page=backup:backup_module&do[]=backup:backup_module&_escape=1&module_id='+{/literal}module_id{literal}+'&limit='+limit+'&offset='+offset+'&backup_id='+backup_id);
}
showIFrame('iframeBackup',600,300,'?_page=backup:backup_module&do[]=backup:backup_module&_escape=1&module_id=&limit='+limit+'&offset='+offset+'&backup_id='+{/literal}{$record_id}{literal});
</SCRIPT>{/literal}

{else}

<!-- Display the form to collect the input values -->
<form id="backup_add" name="backup_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=backup}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("backup_date_expire", $VAR.backup_date_expire, "form_field") }
                    <input type="hidden" name="backup_date_orig" value="{$smarty.now}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_modules 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_multi($VAR.backup_modules, 'backup_modules', 'module', 'name', '', '20', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="backup_notes" cols="40" rows="5" {if $backup_notes == true}class="form_field_error"{/if}>{$VAR.backup_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="backup:add">
                    <input type="hidden" name="_page_current" value="backup:add">
                    <input type="hidden" name="do[]" value="backup:add">
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
<br>
<form id="backup_add" name="backup_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=backup}
                title_manual 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5" class="row1">
                <tr>
                  <td> 
                    {translate module=backup}
                    manual_instructions 
                    {/translate}
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_manual 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="back_filename" value="{$VAR.backup_filename}" {if $backup_filename == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_add("backup_date_expire", $VAR.backup_date_expire, "form_field") }
                    <input type="hidden" name="backup_date_orig2" value="{$smarty.now}">
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=backup}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="backup_notes" cols="40" rows="5" {if $backup_notes == true}class="form_field_error"{/if}>{$VAR.backup_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="backup:view">
                    <input type="hidden" name="_page_current" value="backup:add">
                    <input type="hidden" name="do[]" value="backup:manual">
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
{/if}
