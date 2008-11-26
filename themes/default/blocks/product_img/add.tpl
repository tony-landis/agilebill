{ $block->display("core:top_clean") }

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_img_add" name="product_img_add" method="post" action="" enctype="multipart/form-data">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%" height="23"> 
                    {translate module=product_img}
                    upload 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%"> 
                    <input type="file" name="upload_file1" size="50" >
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%"> 
                    {translate module=product_img}
                    url 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%"> 
                    <input type="text" name="img_url" {if $product_img_url == true}class="form_field_error"{/if} size="80">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%" align="right"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="product_img:view">
                    <input type="hidden" name="_page_current" value="product_img:add">
                    <input type="hidden" name="do[]" value="product_img:add">
                    <input type="hidden" name="product_img_product_id" value="{$VAR.product_img_product_id}">
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
