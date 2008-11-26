{ $block->display("core:top_clean") }

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="account_memo_add" name="account_memo_add" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=account_memo}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_memo}
                    field_memo 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea id="focus" name="account_memo_memo" {if $account_memo_memo == true}class="form_field_error"{/if} cols="55" rows="5">{$VAR.account_memo_memo}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="account_memo_type" value="admin">
                    <input type="hidden" name="account_memo_account_id" value="{$VAR.account_memo_account_id}">
                    <input type="hidden" name="account_memo_staff_id" value="{$smarty.const.SESS_ACCOUNT}">
                    <input type="hidden" name="account_memo_date_orig" value="{$smarty.now}">
                    <input type="hidden" name="_page" value="account_memo:view">
                    <input type="hidden" name="_page_current" value="account_memo:add">
                    <input type="hidden" name="_escape" value="1">
                    <input type="hidden" name="_escape_next" value="1">
                    <input type="hidden" name="do[]" value="account_memo:add">
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
<script language="JavaScript">
  	document.getElementById('focus').focus();
  </script>
