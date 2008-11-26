{if $SESS_LOGGED == true}

{ $method->exe('invoice', 'performance') }
{if $method->result == TRUE}
<table width="550" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="body"> 
      <div align="center"><a href="?_page=core:admin&period=w"> 
        {translate module=invoice}
        weekly 
        {/translate}
        </a> | <a href="?_page=core:admin&period=m"> 
        {translate module=invoice}
        monthly 
        {/translate}
        </a> | <a href="?_page=core:admin&period=y"> 
        {translate module=invoice}
        yearly 
        {/translate}
        </a><br>
        <br>
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <table id="main1" width="550" border="0" cellspacing="0" cellpadding="0" class="table_background">
        <form id="form1" name="form1" method="post" action="">
          <tr> 
            <td> 
              <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="3">
                <!-- DISPLAY THE SEARCH HEADING -->
                <tr valign="middle" align="center" class="table_heading"> 
                  <td width="163" class="table_heading"> 
                    {translate module=invoice}
                    indicator 
                    {/translate}
                  </td>
                  <td width="351" class="table_heading"> 
                    {translate module=invoice}
                    period 
                    {/translate}
                  </td>
                  <td width="189" class="table_heading"> <b> 
                    {translate module=invoice}
                    current 
                    {/translate}
                    </b></td>
                  <td width="163" class="table_heading"> 
                    {translate module=invoice}
                    previous 
                    {/translate}
                  </td>
                  <td width="141" class="table_heading"> <b> 
                    {translate module=invoice}
                    change 
                    {/translate}
                    </b></td>
                </tr>
                <tr class="row1"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    sales 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($sales_current, '') }
                    </b></td>
                  <td width="163">&nbsp; 
                    { $list->format_currency_num($sales_previous, '')}
                  </td>
                  <td width="141" align="center"> <b> 
                    {$sales_change}
                    </b></td>
                </tr>
                <tr class="row2"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    forcast 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($forcast_current, '') }
                    </b></td>
                  <td width="163">&nbsp; -&nbsp;&nbsp;</td>
                  <td width="141" align="center"> <b> 
                    {$forcast_change}
                    </b></td>
                </tr>
                <tr class="row1"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    quota 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {translate module=invoice}
                    today 
                    {/translate}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($quota_current, '') }
                    </b></td>
                  <td width="163">&nbsp; -</td>
                  <td width="141" align="center"><b>-</b></td>
                </tr>
                <tr class="row2"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    arcredits 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($ar_credits_current, '') }
                    </b></td>
                  <td width="163"> &nbsp; 
                    { $list->format_currency_num($ar_credits_previous, '')}
                  </td>
                  <td width="141" align="center"> <b> 
                    {$ar_credit_change}
                    </b></td>
                </tr>
                <tr class="row1"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    arbalance 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($ar_balance_current, '') }
                    </b></td>
                  <td width="163"> &nbsp; 
                    { $list->format_currency_num($ar_balance_last, '')}
                  </td>
                  <td width="141" align="center"><b>-</b></td>
                </tr>
                <tr class="row2"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    users 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    {$users_current}
                    </b></td>
                  <td width="163">&nbsp; 
                    {$users_previous}
                  </td>
                  <td width="141" align="center"> <b> 
                    {$users_change}
                    </b></td>
                </tr>
                {if $show_tickets == true}
                <tr class="row1"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    tickets 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    {$tickets_current}
                    </b></td>
                  <td width="163"> &nbsp; 
                    {$tickets_previous}
                  </td>
                  <td width="141" align="center"> <b> 
                    {$tickets_change}
                    </b></td>
                </tr>
                {/if}
                {if $show_affiliates == true}
                <tr class="row2"> 
                  <td width="163"><b>&nbsp; 
                    {translate module=invoice}
                    affiliatesales 
                    {/translate}
                    </b></td>
                  <td width="351">&nbsp; 
                    {if $VAR.period == '' || $VAR.period == 'm'}
                    {translate module=invoice}
                    thismonth 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastmonth 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'w'}
                    {translate module=invoice}
                    thisweek 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastweek 
                    {/translate}
                    {/if}
                    {if $VAR.period == 'y'}
                    {translate module=invoice}
                    thisyear 
                    {/translate}
                    {translate module=invoice}
                    vs 
                    {/translate}
                    {translate module=invoice}
                    lastyear 
                    {/translate}
                    {/if}
                  </td>
                  <td width="189"><b>&nbsp; 
                    { $list->format_currency_num($affiliate_sales_current, '') }
                    </b></td>
                  <td width="163"> &nbsp; 
                    { $list->format_currency_num($affiliate_sales_previous, '')}
                  </td>
                  <td width="141" align="center"> <b> 
                    {$affiliate_sales_change}
                    </b></td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
</table>
<p align="center"><a href="?_page=core:admin&period=w"> </a></p>
{/if}
{if $smarty.const.GD}
<p align="left"><img title="Invoice Trend" alt="[GD failed]" src="?_page=core:graphview&_escape=1&graph=invoice:compare&period={$VAR.period}"></p>
<table width="550" border="0" cellpadding="0" cellspacing="0">
  <tr valign="top"> 
    <td> 
      <p> <img title="Top Users" alt="[img]" src="?_page=core:graphview&_escape=1&graph=account_admin:top&period={$VAR.period}"> 
        {if $list->is_installed('affiliate') }
      </p>
      <p> <img src="?_page=core:graphview&_escape=1&graph=affiliate:top&period={$VAR.period}"> 
        {/if}
      </p>
    </td>
    <td> 
      <div align="right"><img title="Top Products" alt="[img]" src="?_page=core:graphview&_escape=1&graph=product:top&period={$VAR.period}"></div>
    </td>
  </tr>
</table>
{/if}

{else}
	{$block->display('account:account')}
{/if}

