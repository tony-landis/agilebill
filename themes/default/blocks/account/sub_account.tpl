<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr valign="top">
        <td width="65%" class="table_heading"> {translate module=account}title_view_sub_account{/translate} </td>
      </tr>
      <tr valign="top">
        <td width="65%" class="row1"><table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr class="row4" valign="top">
            <td colspan="2">&nbsp;</td>
            <td colspan="2"><div align="right"><a href="?_page=account:sub_account_add&account_company={$account.company}">Add a Sub Account</a></div>			</td>
          </tr>
		  
		  {foreach from=$subaccount item=sub}
          <tr valign="top">
            <td width="25%">{$sub.first_name} {$sub.last_name}</td>
            <td width="18%">{$sub.username}</td>
            <td width="28%">{$sub.email}</td>
            <td width="29%"><div align="right"><a href="?_page=account:view&id={$sub.id}">Sub-Account Management</a></div></td>
          </tr> 
		  {/foreach}
		  
        </table>
		</td>
      </tr>
    </table>
	</td>
  </tr>
</table>