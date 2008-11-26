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
      ipinfo_namebased 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <select name="product_host_provision_plugin_data[ipinfo_namebased]" class="form_menu">
        <option value="1" {if $plugin_data.ipinfo_namebased == "1"} selected{/if}>Name 
        Based</option>
        <option value="0" {if $plugin_data.ipinfo_namebased == "0"} selected{/if}>IP 
        Based</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      diskquota 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[diskquota]" value="{$plugin_data.diskquota}" class="form_field" size="10">
      MB </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      maxusers 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[maxusers]" value="{$plugin_data.maxusers}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      bandwidth_threshold 
      {/translate}
    </td>
    <td width="17%"> ---</td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[bandwidth_threshold]" value="{if $plugin_data.bandwidth_threshold == ""}0{else}{$plugin_data.bandwidth_threshold}{/if}" class="form_field" size="15">
      Bytes (0=unlimited)</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      bandwidth_rollover 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[bandwidth_rollover]" value="{if $plugin_data.bandwidth_rollover == ""}0{else}{$plugin_data.bandwidth_rollover}{/if}" class="form_field" size="10">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      mailscanner 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mailscanner]", $plugin_data.mailscanner, "form_menu") }
    </td>
    <td width="34%"> 
      {translate module=host_provision_plugin}
      mailscanner_out 
      {/translate}
      { $list->bool("product_host_provision_plugin_data[mailscanner_out]", $plugin_data.mailscanner_out, "form_menu") }
      {translate module=host_provision_plugin}
      mailscanner_in 
      {/translate}
      { $list->bool("product_host_provision_plugin_data[mailscanner_in]", $plugin_data.mailscanner_in, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      aspmgr 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[aspmgr]", $plugin_data.aspmgr, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      scriptsmgr 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[scriptsmgr]", $plugin_data.scriptsmgr, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      files 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[files]", $plugin_data.files, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      analog 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[analog]", $plugin_data.analog, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      frontpage 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[frontpage]", $plugin_data.frontpage, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
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
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      allowanonymous 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[anonftp]", $plugin_data.anonftp, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      mysql 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mysql]", $plugin_data.mysql, "form_menu") }
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[dbasenum]" value="{if $plugin_data.dbasenum == ""}1{else}{$plugin_data.dbasenum}{/if}" class="form_field" size="10">
      Max DBs</td>
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
    <td width="34%">&nbsp;</td>
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
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      develenv 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[develenv]", $plugin_data.develenv, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      telnet 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[telnet]", $plugin_data.telnet, "form_menu") }
    </td>
    <td width="34%">Jail? 
      { $list->bool("product_host_provision_plugin_data[telnet_jail]", $plugin_data.telnet_jail, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ssh
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[ssh]", $plugin_data.ssh, "form_menu") }
    </td>
    <td width="34%">Jail? 
      { $list->bool("product_host_provision_plugin_data[ssh_jail]", $plugin_data.ssh_jail, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      logrotate 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[logrotate]", $plugin_data.logrotate, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      vhbackup 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[vhbackup]", $plugin_data.vhbackup, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      bind 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[bind]", $plugin_data.bind, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      imap 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[imap]", $plugin_data.imap, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      tomcat4 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[tomcat4]", $plugin_data.tomcat4, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      openssl 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[openssl]", $plugin_data.openssl, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      weblogs 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[weblogs]", $plugin_data.weblogs, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      anonftp 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[anonftp]", $plugin_data.anonftp, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      majordomo 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[majordomo]", $plugin_data.majordomo, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      vacation 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[vacation]", $plugin_data.vacation, "form_menu") }
    </td>
    <td width="34%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      spam_filter
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[spam_filter]", $plugin_data.spam_filter, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      sqmail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[sqmail]", $plugin_data.sqmail, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      mivamerchant 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[mivamerchant]", $plugin_data.mivamerchant, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      webalizer 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[webalizer]", $plugin_data.webalizer, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      proftpd 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[proftpd]", $plugin_data.proftpd, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      sendmail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[sendmail]", $plugin_data.sendmail, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      subdomain 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[subdomain]", $plugin_data.subdomain, "form_menu") }
    </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[max_subdomain]" value="{if $plugin_data.max_subdomain == ""}0{else}{$plugin_data.max_subdomain}{/if}" class="form_field" size="10">
      {translate module=host_provision_plugin}
      max_subdomain 
      {/translate}
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      apache_jail 
      {/translate}
    </td>
    <td width="17%"> 
      { $list->bool("product_host_provision_plugin_data[apache_jail]", $plugin_data.apache_jail, "form_menu") }
    </td>
    <td width="34%">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td width="49%">&nbsp;</td>
    <td width="17%">&nbsp;</td>
    <td width="34%">&nbsp;</td>
  </tr>
</table>
