

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="blocked_ip_add" name="blocked_ip_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=blocked_ip}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=blocked_ip}
                    field_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="blocked_ip_ip" value="{$VAR.blocked_ip_ip}" {if $blocked_ip_ip == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=blocked_ip}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="blocked_ip_notes" cols="40" rows="5" {if $blocked_ip_notes == true}class="form_field_error"{/if}>{$VAR.blocked_ip_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="blocked_ip:view">
                    <input type="hidden" name="_page_current" value="blocked_ip:add">
                    <input type="hidden" name="do[]" value="blocked_ip:add">
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
