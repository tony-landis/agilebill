<!--{literal}
Removed for the time being...

<form id="version_check" name="version_check" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
                consistancy_file
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    current_version
                    {/translate}
                  </td>
                  <td width="58%"> 
                    {$list->
version()}
                    {$ab_version}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    current_ab_version
                    {/translate}
                  </td>
                  <td width="58%"> 
                    {$version}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    encoding_version
                    {/translate}
                  </td>
                  <td width="58%">
                    {$encoding_version}
                    <input type="hidden" name="ver" value="{$encoding_version}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    optional_modules
                    {/translate}
                  </td>
                  <td width="58%"> 
				  {foreach from=$modules item="module"}
				  {$module}
				  <input type="hidden" name="module[]" value="{$module}">
				  <br>
				  {/foreach} </td>
                </tr>
                <tr valign="top">
                  <td width="42%">&nbsp;</td>
                  <td width="58%">
                    <input type="submit" name="Submit2" value="{translate module=module}consistancy_submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="module:remote_update">
                    <input type="hidden" name="do[]" value="module:remote_update">
                    <input type="hidden" name="step" value="1">
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
{/literal}-->
 
 
 {$method->exe_noauth("module","remote_version_check")}
{if $send}
<form id="version_check" name="version_check" method="post" action="http://agileco.com/accounts/">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
                consistancy_file
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="row1">
                <tr valign="top">
                  <td width="10%"><strong>Send?</strong></td>
                  <td width="14%"><strong>Data</strong></td>
                  <td width="76%"><strong>Value</strong></td>
                </tr>
                <tr valign="top">
                  <td>Required
                  <input name="version" type="hidden" id="version" value="{$send.version}"></td>
                  <td>Version</td>
                  <td>{$send.version}</td>
                </tr>
                <tr valign="top">
                  <td>Required
                  <input name="license" type="hidden" id="license" value="{$send.license}"></td>
                  <td>License </td>
                  <td>{$send.license}</td>
                </tr>
                <tr valign="top">
                  <td><input name="php" type="checkbox" id="php" value="{$send.php}" checked></td>
                  <td>PHP </td>
                  <td>{$send.php}</td>
                </tr>
                <tr valign="top">
                  <td><input name="mysql" type="checkbox" id="mysql" value="{$send.mysql}" checked></td>
                  <td>MySQL </td>
                  <td>{$send.mysql}</td>
                </tr>
                <tr valign="top">
                  <td><input name="os" type="checkbox" id="os" value="{$send.os}" checked></td>
                  <td>OS</td>
                  <td>{$send.os}</td>
                </tr>
                <tr valign="top">
                  <td><input name="proc" type="checkbox" id="proc" value="{$send.proc}" checked></td>
                  <td>Architecture </td>
                  <td>{$send.proc}</td>
                </tr>
                <tr valign="top">
                  <td><input name="arch" type="checkbox" id="arch" value="{$send.arch}" checked></td>
                  <td>Processor </td>
                  <td>{$send.arch}</td>
                </tr>
                <tr valign="top">
                  <td><input name="server" type="checkbox" id="server" value="{$send.server}" checked></td>
                  <td>Server </td>
                  <td>{$send.server}</td>
                </tr>
                <tr valign="top">
                  <td colspan="3"><div align="center">
                    <input type="submit" name="Submit2" value="Check Version Now" class="form_button">
                    <input type="hidden" name="_page" value="license:user_version">
                    <input type="hidden" name="do[]" value="license:user_version">
                  </div></td>
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



<form id="module_add" name="module_form" method="post" action=""> 
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=module}
                title_upgrade 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    upgrademodules 
                    {/translate}
                  </td>
                  <td width="58%"> 
                    { $list->menu_multi($VAR.module_name, 'module_name', 'module', 'name', '', '20', 'form_menu') }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%">&nbsp; </td>
                  <td width="58%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"> 
                    {translate module=module}
                    default_groups 
                    {/translate}
                  </td>
                  <td width="58%"> 
                    {$list->select_groups($VAR.module_group, module_group, "form_field", "1", "_1")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"></td>
                  <td width="58%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="42%"></td>
                  <td width="58%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="module:upgrade">
                    <input type="hidden" name="_page_current" value="module:upgrade">
                    <input type="hidden" name="do[]" value="module:upgrade">
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
{$js}
 