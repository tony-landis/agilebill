
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="160" valign="top"> <img src="themes/{$THEME_NAME}/images/invisible.gif" width="160" height="1"> 
    </td>
    <td width="1" bgcolor="#CCCCCC"> <img src="themes/{$THEME_NAME}/images/invisible.gif" width="1" height="1"><br>
    </td>
    <td  valign="top" width="600"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="8">
        <tr> 
          <td valign="middle" class="body" align="center"> <font color="#999999">CPU 
            time used: 
            {$smarty.const.EXECUTION_TIME|truncate:5:""}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 SQL Queries Used: 
            {$smarty.const.SQL_QUERIES}
            </font> </td>
        </tr>
      </table>
      <img src="themes/{$THEME_NAME}/images/invisible.gif" width="575" height="1"> 
    </td>
    <td width="1" bgcolor="#CCCCCC"> <img src="themes/{$THEME_NAME}/images/invisible.gif" width="1" height="1"><br>
    </td>
    <td width="50%"> </td>
  </tr>
  <tr> 
    <td width="160" valign="top"></td>
    <td width="1" bgcolor="#CCCCCC"></td>
    <td height="1" width="600" bgcolor="#CCCCCC"><img src="themes/{$THEME_NAME}/images/invisible.gif" width="1" height="1"></td>
    <td width="1" bgcolor="#CCCCCC"></td>
    <td width="50%"></td>
  </tr>
</table>
 