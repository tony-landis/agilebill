{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr valign="top" class="row2">
    <td width="34%">Text for ticket </td>
    <td width="66%"><textarea name="product_prod_plugin_data[message]" cols="60" rows="10">{$plugin_data.message}</textarea></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Department to Create Ticket In </td>
    <td>{html_menu name=product_prod_plugin_data[department_id] assoc_table="ticket_department" assoc_field="name" size=5 default=$plugin_data.department_id}</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Extra E-mails to Notify (comma separated)</td>
    <td><input type="text" name="product_prod_plugin_data[emails]" value="{$plugin_data.emails}"  size="32"></td>
  </tr>
  <!--
  <tr valign="top"> 
    <td width="49%"> MeetMe Conference Limit (Combined Calling Minute Usage)</td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_prod_plugin_data[meetme_min_limit]" value="{$plugin_data.meetme_min_limit}"  size="10">
      0-Unlimited</td>
  </tr>
  -->

</table>
