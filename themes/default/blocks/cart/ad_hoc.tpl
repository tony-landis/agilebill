 
<form name="adhoc" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=cart}
                ad_hoc_heading 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    ad_hoc_sku 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" id="ad_hoc_sku" name="ad_hoc_sku">
                  </td>
                </tr> 
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    ad_hoc_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ad_hoc_name">
                  </td> 
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    ad_hoc_amount 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="ad_hoc_amount" size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    quantity 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="quantity" size="3" value="1">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    ad_hoc_taxable 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="checkbox" name="ad_hoc_taxable" value="1" checked>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=cart}
                    ad_hoc_attr
                    {/translate}
                  </td>
                  <td width="65%"> 
                    
					<div id="attr1" style="display:block">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr2').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr2" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr3').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr3" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr4').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
							
					<div id="attr4" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr5').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr5" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr6').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr6" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr7').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr7" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr8').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr8" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr9').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr9" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22" onChange="javascript:document.getElementById('attr10').style.display='block'">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>
					
					<div id="attr10" style="display:none">
						<input type="text" name="ad_hoc_attr_var[]" size="22">
						= 
						<input type="text" name="ad_hoc_attr_val[]" size="40">
                    </div>																																													
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="do[]" value="cart:ad_hoc">
                    <input type="hidden" name="_page" value="cart:admin_view">
                    <input type="hidden" name="_page_current" value="cart:ad_hoc">
                    <input type="hidden" name="account_id" value="{$VAR.account_id}">
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
{literal}
<script language="JavaScript"> 
document.getElementById('ad_hoc_sku').focus();
</script>
{/literal} 