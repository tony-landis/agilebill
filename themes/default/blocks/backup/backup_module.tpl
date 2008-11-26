{ $block->display("core:top_clean") }
 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width=100% border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <div align="center"> 
              {translate}
              alert
              {/translate}
            </div>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellpadding="2"> 
			   <tr> 		  			                
			    <td class="{cycle values="row1,row2"}"><img src="themes/{$THEME_NAME}/images/icons/about_16.gif" border="0" width="16" height="16" hspace="3"> 
                  {$backup_message} 
                </td>
				</tr>  	                
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
 
<SCRIPT LANGUAGE="JavaScript"> 
{$javascript}
</SCRIPT>
 
