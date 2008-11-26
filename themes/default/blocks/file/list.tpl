{ $method->exe("file","file_list")} 		{ if ($method->result == FALSE) } { $block->display("core:method_error") } {/if}
{if $file_display == true}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
		   {translate module=file}
              available_files
			  {/translate}
          </td>
        </tr>
        <tr> 
          <td class="table_background">   
		  {foreach from=$file_results item=record}
		  
            <table width="100%" border="0" cellpadding="5" class="row2" cellspacing="1" onMouseOver="document.getElementById('desc{$record.id}').style.display='block';" OnMouseOut="document.getElementById('desc{$record.id}').style.display='none';">
              
			  <tr> 
                <td class="body" width="26%">
				  <b><a href="{$URL}?_page=file:download&_escape=1&id={$record.id}" target="_blank"><u> 
                  {$record.name}
                  </u></a> </b> </td>
                <td class="body" width="31%"> </td>
                <td class="body" width="21%"> 
                  {$record.size|number_format}
                  KB</td>
                <td class="body" width="22%">
				 <a href="{$URL}?_page=file:download&_escape=1&id={$record.id}" target="_blank"><u>
				 {translate module=file}
                  download 
                  {/translate}</u>
                  </a></td>
              </tr> 
            </table>  
			<div id="desc{$record.id}" style="display:none">
				<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
				  <tr> 
					<td> 
					  <table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr> 
						  <td class="table_background"> 
							<table width="100%" border="0" cellpadding="5" class="row1" cellspacing="0"> 
							  <tr> 
								<td class="body">  
								  {$record.description|nl2br}
								</td>
							  </tr> 
							</table>
						  </td>
						</tr>
					  </table>
					</td>
				  </tr>
				</table> 				  
			  </div> 
			{/foreach} 
          </td>
        </tr>
      </table>  
    </td>
  </tr>
</table>
<p><br>
  {else}
  {translate module=file}
  no_files 
  {/translate}
  {/if}
</p> 

