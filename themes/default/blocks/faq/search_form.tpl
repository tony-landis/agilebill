
{ $method->exe("faq","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="faq_search" method="post" action="">
  {$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=faq}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=faq}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="faq_name" value="{$VAR.faq_name}" {if $faq_name == true}class="form_field_error"{else}class="form_field"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    
                  <td width="35%"> 
                    {translate module=faq}
                    field_faq_category_id 
                    {/translate}
                  </td>
                    
                  <td width="65%"> 
                    { $list->menu("", "faq_faq_category_id", "faq_category", "name", "all", "form_menu") }
                  </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=faq}
                            field_status
                        {/translate}</td>
                    
                  <td width="65%"> 
                    { $list->bool("faq_status", "all", "form_menu") }
                  </td>
                  </tr>				  				  			
                   <tr valign="top">
                    <td width="35%">
                        {translate module=faq}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("faq_date_orig", $VAR.faq_date_orig, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=faq}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("faq_date_last", $VAR.faq_date_last, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=faq}
                            field_date_expire
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("faq_date_expire", $VAR.faq_date_expire, "form_field", "") }
                    </td>
                  </tr>


                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" class="form_field" name="limit" size="5" value="{$faq_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$faq item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="faq">
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
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
