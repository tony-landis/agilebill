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
	          
			  
			   {foreach from=$alert item=alert} 
			   {if $alert != ''}
			   <tr> 		  			                
			    <td class="{cycle values="row1,row2"}"><img src="themes/{$THEME_NAME}/images/icons/about_16.gif" border="0" width="16" height="16" hspace="3"> 
                  {$alert}
                </td>
				</tr>
			   {/if}
			   {/foreach}
			   
              			                
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
