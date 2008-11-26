{ $block->display("core:top_clean") }

{ $method->exe("product_translate","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}<script language=javascript>
function editTranslations(product_id)
{    
	var language_id = document.product_translate_view.language_id.value;
	var url = '?_page=core:search_iframe&module=product_translate&product_translate_language_id='+
			   language_id+'&product_translate_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=edit&_next_page_none=add&name_id1=product_translate_product_id&val_id1='
			   +product_id+'&name_id2=product_translate_language_id&val_id2='+language_id; 	   
	window.open(url,'ProductLanguage','scrollbars=yes,toolbar=no,status=no,width=700,height=650'); 
} 

function viewTranslations(product_id)
{    
	var language_id = document.getElementById('language_id_1').value;
	var url = '?_page=core:search_iframe&module=product_translate&product_translate_language_id='+
			   language_id+'&product_translate_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=product_translate_product_id&val_id1='
			   +product_id+'&name_id2=product_translate_language_id&val_id2='+language_id; 	   
	document.location = url;
} 
</script>{/literal}

<!-- Loop through each record -->
{foreach from=$product_translate item=product_translate}

<!-- Display each record -->
<form name="product_translate_view" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"><b> </b> 
              <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <tr> 
                  <td class="body"><b> 
                    { $list->menu_files("1", "language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "\" onChange=\"viewTranslations(`$product_translate.product_id`);") }
                    <a href="javascript:editTranslations({$product_translate.product_id});">Edit</a> 
                    </b></td>
                </tr>
              </table>
              <b> </b></td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                <tr> 
                  <td class="row2" align="center"><b> </b> </td>
                </tr>
                <td class="row2"><b> 
                  {translate module=product_translate}
                  field_name 
                  {/translate}
                  </b> </td>
                </tr>
                <tr> 
                  <td> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                      <tr> 
                        <td width="5">&nbsp;</td>
                        <td width="98%"> 
                          {$product_translate.name}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                <tr> 
                  <td class="row2" align="center"><b> </b> </td>
                </tr>
                <tr> 
                  <td class="row2"><b> 
                    {translate module=product_translate}
                    field_description_short 
                    {/translate}
                    </b></td>
                </tr>
                <tr> 
                  <td> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                      <tr> 
                        <td width="5">&nbsp;</td>
                        <td width="98%"> 
                          {$product_translate.description_short}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="3" class="body">
                <tr> 
                  <td class="row2"><b> 
                    {translate module=product_translate}
                    field_description_full 
                    {/translate}
                    </b></td>
                </tr>
                <tr> 
                  <td> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
                      <tr> 
                        <td width="5">&nbsp;</td>
                        <td width="98%"> 
                          {$product_translate.description_full}
                        </td>
                      </tr>
                    </table>
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
  
{/foreach}
{/if}
