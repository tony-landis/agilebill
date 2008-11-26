{$list->unserial($product.host_provision_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">&nbsp; </td>
    <td width="50%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">IP Based Plan? </td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[ip_based]", $plugin_data.ip_based, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">&nbsp; </td>
    <td width="50%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"><b>Client/Domain Limits</b></td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of subdomains</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_subdom]" value="{$plugin_data.max_subdom}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Disk space</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[disk_space]" value="{$plugin_data.disk_space}" class="form_field" size="12">
      MB </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum amount of traffic</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_traffic]" value="{$plugin_data.max_traffic}" class="form_field" size="12">
      MB/Month </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of web users </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_wu]" value="{$plugin_data.max_wu}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of databases </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_db]" value="{$plugin_data.max_db}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of mailboxes </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_box]" value="{$plugin_data.max_box}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Mailbox quota</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[mbox_quota]" value="{$plugin_data.mbox_quota}" class="form_field" size="12">
      KB </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of mail redirects </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_redir]" value="{$plugin_data.max_redir}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of mail groups </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_mg]" value="{$plugin_data.max_mg}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of mail autoresponders </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_resp]" value="{$plugin_data.max_resp}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of mailing lists </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_maillists]" value="{$plugin_data.max_maillists}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Maximum number of Java applications </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[max_webapps]" value="{$plugin_data.max_webapps}" class="form_field" size="4">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">FTP Quota </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[ftp_quota]" value="{$plugin_data.ftp_quota}" class="form_field" size="12">
      <input type="hidden" name="product_host_provision_plugin_data[max_dom]" value="1" class="form_field" size="4">
      MB </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">&nbsp; </td>
    <td width="50%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"><b>Client Permissions:</b></td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Domain creation</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[create_domains]", $plugin_data.create_domains, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Physical hosting management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_phosting]", $plugin_data.manage_phosting, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Management of shell access to server</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_sh_access]", $plugin_data.manage_sh_access, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Non-chrooted shell management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_not_chroot_shell]", $plugin_data.manage_not_chroot_shell, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Hard disk quota assignment</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_quota]", $plugin_data.manage_quota, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Subdomains management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_subdomains]", $plugin_data.manage_subdomains, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Log rotation management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_log]", $plugin_data.manage_log, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Anonymous FTP management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_anonftp]", $plugin_data.manage_anonftp, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Crontab management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_crontab]", $plugin_data.manage_crontab, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Use of Mambo content management system (Site Builder)</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[site_builder]", $plugin_data.site_builder, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Domain limits adjustment</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[change_limits]", $plugin_data.change_limits, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">DNS zone management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_dns]", $plugin_data.manage_dns, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Java applications management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_webapps]", $plugin_data.manage_webapps, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Mailing lists management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_maillists]", $plugin_data.manage_maillists, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Dr.Web antivirus management</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[manage_drweb]", $plugin_data.manage_drweb, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Backup/restore functions</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[make_dumps]", $plugin_data.make_dumps, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">&nbsp;</td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"><b>Domain Permissions:</b></td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Enable Frontpage</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[fp]", $plugin_data.fp, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Enable Frontpage SSL</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[fp_ssl]", $plugin_data.fp_ssl, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Enable Frontpage Authentication</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[fp_auth]", $plugin_data.fp_auth, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">SSL</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[ssl]", $plugin_data.ssl, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Shell</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[shell]", $plugin_data.shell, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">PHP</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[php]", $plugin_data.php, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">SSI</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[ssi]", $plugin_data.ssi, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">CGI</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[cgi]", $plugin_data.cgi, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">MOD Perl</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[mod_perl]", $plugin_data.mod_perl, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">MOD Python</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[mod_python]", $plugin_data.mod_python, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">ASP</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[asp]", $plugin_data.asp, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">ASP.net</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[asp_dot_net]", $plugin_data.asp_dot_net, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">ColdFusion</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[coldfusion]", $plugin_data.coldfusion, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Web Stats</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[webstat]", $plugin_data.webstat, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Error Docs</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[errdocs]", $plugin_data.errdocs, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">@domains</td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[at_domains]", $plugin_data.at_domains, "form_menu") }
    </td>
  </tr>
</table>  
<input type="hidden" name="product_host_provision_plugin_data[account_id]" value="{$plugin_data.account_id}">
<input type="hidden" name="product_host_provision_plugin_data[domain_id]" value="{$plugin_data.domain_id}">
 
