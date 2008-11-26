{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top" class="row2"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      service 
      {/translate}
    </td>
    <td width="17%"> 
      {translate module=host_provision_plugin}
      enabled 
      {/translate}
    </td>
    <td width="34%"> 
      {translate module=host_provision_plugin}
      options 
      {/translate}
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      hst_type 
      {/translate}
    </td>
    <td width="17%"> 
      <select name="product_host_provision_plugin_data[hst_type]" class="form_menu">
        <option value="1" {if $plugin_data.hst_type == "1"} selected{/if}>Name 
        Based</option>
        <option value="0" {if $plugin_data.hst_type == "0"} selected{/if}>IP Based</option>
      </select>
    </td>
    <td width="34%"> ---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      hard_quota 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[hard_quota]" value="{$plugin_data.hard_quota}" class="form_field" size="10">
      MB</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      disk_space 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[disk_space]" value="{$plugin_data.disk_space}" class="form_field" size="10">
      MB</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      traffic 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_traffic]" value="{$plugin_data.max_traffic}" class="form_field" size="10">
      MB/month </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_box 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_box]" value="{if $plugin_data.max_box == ""}-1{else}{$plugin_data.max_box}{/if}" class="form_field" size="10">
      -1=unlimited</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_redir 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_redir]" value="{if $plugin_data.max_redir == ""}-1{else}{$plugin_data.max_redir}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_mg 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_mg]" value="{if $plugin_data.max_mg == ""}-1{else}{$plugin_data.max_mg}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_resp 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_resp]" value="{if $plugin_data.max_resp == ""}-1{else}{$plugin_data.max_resp}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_wu 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_wu]" value="{if $plugin_data.max_wu == ""}-1{else}{$plugin_data.max_wu}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_db 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_db]" value="{if $plugin_data.max_db == ""}-1{else}{$plugin_data.max_db}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_maillists 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_maillists]" value="{if $plugin_data.max_maillists == ""}-1{else}{$plugin_data.max_maillists}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      max_webapps 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_webapps]" value="{if $plugin_data.max_webapps == ""}-1{else}{$plugin_data.max_webapps}{/if}" class="form_field" size="10">
      -1=unlimited </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      keep_traf_stat 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[keep_traf_stat]" value="{if $plugin_data.keep_traf_stat == ""}0{else}{$plugin_data.keep_traf_stat}{/if}" class="form_field" size="10">
      Months (0=unlimited)</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ssi 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ssi]", $plugin_data.ssi, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      fp 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[fp]", $plugin_data.fp, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      fp_ssl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[fp_ssl]", $plugin_data.fp_ssl, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      fpauth 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[fpauth]", $plugin_data.fpauth, "form_menu") }
    </td>
    <td width="34%">--- </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      php 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[php]", $plugin_data.php, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      cgi 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[cgi]", $plugin_data.cgi, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      perl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[perl]", $plugin_data.perl, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      asp 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[asp]", $plugin_data.asp, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      python 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[python]", $plugin_data.python, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      webstat 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[webstat]", $plugin_data.webstat, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      webmail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[webmail]", $plugin_data.webmail, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      log_bysize 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[log_rotate]", $plugin_data.log_rotate, "form_menu") }
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[log_bysize]" value="{$plugin_data.log_bysize}" class="form_field" size="10">
      KB</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      log_max_num 
      {/translate}
    </td>
    <td width="17%"> 
      <select name="product_host_provision_plugin_data[log_bytime]"  class="form_menu">
        <option value="daily" {if $plugin_data.log_bytime == "daily"} selected{/if}>Daily</option>
        <option value="weekly" {if $plugin_data.log_bytime == "weekly"} selected{/if}>Weekly</option>
        <option value="monthly" {if $plugin_data.log_bytime == "monthly"} selected{/if}>Monthly</option>
      </select>
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[log_max_num]" value="{$plugin_data.log_max_num}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      log_compress 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[log_compress]", $plugin_data.log_compress, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      err_docs 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[err_docs]", $plugin_data.err_docs, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      wuscripts 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[wuscripts]", $plugin_data.wuscripts, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ssl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ssl]", $plugin_data.ssl, "form_menu") }
    </td>
    <td width="34%">---</td>
  </tr>
</table>
 