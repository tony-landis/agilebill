{if $SESS_LOGGED == true}

<form method="post" action="">
<u>Account Quick Search</u> {html_select_account name="id"}  
<input type="hidden" name="_page" value="account_admin:view">               
</form>


{ $method->exe('invoice', 'performance') }
{if $method->result == TRUE}
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="body"> 
      <div align="center"> 
	  	  <a href="?_page=core:admin&period=w"><b>{translate module=invoice}weekly{/translate}</b></a> 
		| <a href="?_page=core:admin&period=m"><b>{translate module=invoice}monthly{/translate}</b></a> 
		| <a href="?_page=core:admin&period=y"><b>{translate module=invoice}yearly{/translate}</b></a> 
      </div>	  
	<br> 	
    </td>
  </tr>
  <tr>
    <td>
      <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
        <form id="form1" name="form1" method="post" action="">
          <tr> 
            <td> 
              <table width="100%" border="0" cellspacing="1" cellpadding="3"> 
                <tr valign="middle" align="center" class="table_heading"> 
                  <td width="25%" class="table_heading"> 
                    {translate module=invoice}
                    indicator 
                    {/translate}
                  </td>
                  <td width="30%" class="table_heading"> 
                    {translate module=invoice}
                    period 
                    {/translate}
                  </td>
                  <td width="15%" class="table_heading"> <b> 
                    {translate module=invoice}
                    current 
                    {/translate}
                    </b></td>
                  <td width="15%" class="table_heading"> 
                    {translate module=invoice}
                    previous 
                    {/translate}
                  </td>
                  <td width="15%" class="table_heading"> <b> 
                    {translate module=invoice}
                    change 
                    {/translate}
                    </b></td>
                </tr>
                <tr class="row1"> 
                  <td width="214" bgcolor="#ECFAEB"><b> 
                    {translate module=invoice}
                    sales 
                    {/translate}
                    </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    { $list->format_currency_num($sales_current, '') }
                    </b></td>
                  <td width="330">{ $list->format_currency_num($sales_previous, '')}
                  </td>
                  <td width="173" align="center">{$sales_change}</td>
                </tr>
                <tr class="row2"> 
                  <td width="214" bgcolor="#ECFAEB"><b> 
                    {translate module=invoice}
                    forcast 
                    {/translate}
                    </b></td>
                  <td width="273">{$period_forcast}</td>
                  <td width="358"><b> 
                    { $list->format_currency_num($forcast_current, '') }
                    </b></td>
                  <td width="330"> -&nbsp;&nbsp;</td>
                  <td width="173" align="center">{$forcast_change}</td>
                </tr>
                <tr class="row1"> 
                  <td width="214" bgcolor="#ECFAEB"><b>{translate module=invoice}
                    quota 
                    {/translate}
                    </b></td>
                  <td width="273"> 
                    {translate module=invoice}
                    today 
                    {/translate}
                  </td>
                  <td width="358"><b> 
                    { $list->format_currency_num($quota_current, '') }
                    </b></td>
                  <td width="330"> -</td>
                  <td width="173" align="center"><b>-</b></td>
                </tr>
                <tr class="row2"> 
                  <td width="214" bgcolor="#ECFAEB"><b> 
                    {translate module=invoice}
                    arcredits 
                    {/translate}
                    </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    { $list->format_currency_num($ar_credits_current, '') }
                    </b></td>
                  <td width="330">  
                    { $list->format_currency_num($ar_credits_previous, '')}
                  </td>
                  <td width="173" align="center">{$ar_credit_change}</td>
                </tr>
                <tr class="row1"> 
                  <td width="214" bgcolor="#ECFAEB"><b> 
                    {translate module=invoice}
                    arbalance 
                    {/translate}
                    </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    { $list->format_currency_num($ar_balance_current, '') }
                    </b></td>
                  <td width="330">  
                    { $list->format_currency_num($ar_balance_last, '')}
                  </td>
                  <td width="173" align="center"><b>-</b></td>
                </tr>
                <tr class="row2"> 
                  <td width="214" bgcolor="#ECFAEB">
                    <b>{translate module=invoice}
                    users 
                    {/translate} 
					 </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    {$users_current}
                    </b></td>
                  <td width="330"> 
                    {$users_previous}
                  </td>
                  <td width="173" align="center">{$users_change}</td>
                </tr>
                {if $show_tickets == true}
                <tr class="row1"> 
                  <td width="214" bgcolor="#ECFAEB"><b> <a href="?_page=ticket:main">
                    {translate module=invoice}
                    tickets 
                    {/translate}
                    </a> </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    {$tickets_current}
                    </b></td>
                  <td width="330">  
                    {$tickets_previous}
                  </td>
                  <td width="173" align="center">{$tickets_change}</td>
                </tr>
                {/if}
                {if $show_affiliates == true}
                <tr class="row2"> 
                  <td width="214" bgcolor="#ECFAEB"><b>
                    {translate module=invoice}
                    affiliatesales 
                    {/translate}
                    </b></td>
                  <td width="273">{$period_compare}</td>
                  <td width="358"><b> 
                    { $list->format_currency_num($affiliate_sales_current, '') }
                    </b></td>
                  <td width="330">  
                    { $list->format_currency_num($affiliate_sales_previous, '')}
                  </td>
                  <td width="173" align="center">{$affiliate_sales_change}</td>
                </tr>
				{/if}
				<!--
				{if $list->is_installed(voip)}
                <tr class="row1">
                  <td bgcolor="#ECFAEB"><strong>Avg. Call Duration </strong></td>
                  <td>{$period_compare}</td>
                  <td><strong>{$acd} minute(s)</strong></td>
                  <td>{$acd_last} minute(s)</td>
                  <td align="center">{$acd_change}</td>
                </tr>
                <tr class="row2">
                  <td bgcolor="#ECFAEB"><strong>Avg. Successful Rate</strong></td>
                  <td>{$period_compare}</td>
                  <td><strong>{$asr}</strong></td>
                  <td>{$asr_last}</td>
                  <td align="center">{$asr_change}</td>
                </tr>
                <tr class="row1">
                  <td bgcolor="#ECFAEB"><strong>Number of CDRs</strong></td>
                  <td>{$period_compare}</td>
                  <td><strong>{$cdrs}</strong></td>
                  <td>{$cdrs_last}</td>
                  <td align="center">{$cdrs_change}</td>
                </tr>
                {/if}
				-->
              </table>
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
</table>
<br>
{$calendar}
{/if}

{if $list->is_installed('account_message')}
{$method->exe_noauth('account_message','view')}
<br>
<b>Update System Message</b><br>
<form method="post" action="">
<textarea name="message" cols="50" rows="5">{$message}</textarea>
<input type="hidden" name="do[]" value="account_message:add">
<input type="hidden" name="_page" value="core:admin"> 
<input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
</form>
{/if}

{else} 
	{$block->display('account:main')}
{/if}

