 
<p><b>Step 3:</b> Installing modules and associated tables...
  <?php if($database === true) { ?>
done!<br>
<br>
The installation installation is now complete, you can login with the following account:<br>
  <br>
  <b>Admin Acccount:</b> admin/admin <br>
  <br>
Please <a href="?_page=core:search&module=setup&_login=1&_username=admin&_password=admin&tid=default_admin&_next_page_one=view&_escape=1"><b>CLICK HERE</b></a> to edit your site settings. This is required, you will need to edit your site URL, or your installation may not work!<br>
  <br>
  <?php echo @$dbinst->tables_created; ?> <?php echo @$dbinst->errors; ?> <br>
  <br>
  <?php } else { ?>
  <b>failed!</b> <?php echo $database; ?> <br>
  <br>
  <b>To Resolve This Issue:</b><br>
[ERROR-3] The user you specified must have adequate permission to create the database in question, or you must manually create the database yourself. Most web hosts either offer a feature to create new databases in your hosting control panel, or will create the database for you if you request that they do so. When resolved, <a href="?do=step1">start the installation process over</a>.</p>
<p>[ERROR-4] The user you specified is not authorized for the database you have specified, please double check with your host. When resolved, <a href="?do=step1">start the installation process over</a>.</p>
<p>[ERROR-5] Other database error, paste the error generated and contact your hosting company for them to resolve the problem. When resolved, <a href="?do=step1">start the installation process over</a>.</p>
<p>[ERROR-6] The user you specified is not authorized to create new tables in the database, please update your database permissions to include this priviledge, or contact your hosting company to do so if you do not know how. Then refesh this page or <a href="?do=step1">start the installation process over</a>.</p>
<p>[ERROR-7] The user you specified is not authorized to create indexes on tables in the database, please update your database permissions to include this priviledge, or contact your hosting company to do so if you do not know how. Then refesh this page or <a href="?do=step1">start the installation process over</a>.<br>
  <?php } ?>
</p>
