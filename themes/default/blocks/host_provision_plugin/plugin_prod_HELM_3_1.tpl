{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="55%"> Helm Hosting Plan ID</td>
    <td width="45%"> 
      <input type="text" name="product_host_provision_plugin_data[plan]" value="{$plugin_data.plan}" class="form_field" size="32">
    </td>
  </tr>
  <tr valign="top">
    <td width="55%">To get the hosting Plan Id, go to your reseller account in 
      Helm, view your Hosting Plans, then click one to view it. In the URL field 
      of your web browser, you will see the URL and the Plan Id will be displayed 
      at the end: PlanID=XXX - Where XXX is the Plan Id. Also, the plans you enter 
      here must have no setup fees or recurring fees, otherwise, AB will not be 
      able to create the domain in the user's account after adding the Plan to 
      their account.</td>
    <td width="45%">&nbsp;</td>
  </tr>
</table>
  