<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<title>AgileCo Product Installer</title>
<head> 
<style type="text/css">
	@import url('themes/default/style.css');	
</style> 
</head> 
<body>
<div id="content">
<table width="750" border="0" align="center">
  <tr>
    <td width="100%" align="center"><a href="http://www.agileco.com/" style="background: none;"><img src='themes/default/images/logo.gif' border='0' /></a></td>
  </tr>
  <tr>
    <td>
	  <div id="menu">
	    
            <?php if($do=='step1') { ?>
            <b>Step 1</b> 
            <?php } else { ?>
            Step 1 
            <?php } ?>
            | 
            <?php if($do=='step2') { ?>
            <b>Step 2</b> 
            <?php } else { ?>
            Step 2 
            <?php } ?>
            | 
            <?php if($do=='step3') { ?>
            <b>Step 3</b> 
            <?php } else { ?>
            Step 3 
            <?php } ?>
		

	  </div>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
 	  <!-- Display the main block --> 
	  <div id="block">	


    
                  <?php 
                  if(is_file($s_file))
                  	include($s_file);
                  elseif(is_file('install/'.$s_file))
                  	include('install/'.$s_file);
                  elseif(is_file($path.$s_file)) 
                  	include($path.$s_file); 
                  ?>
                
	  
	  
	  </div>
	</td>
  </tr>
  <tr>
    <td><div id="copywrite">Copyright 2004-<?php echo date('Y') ?> <a href="http://www.agileco.com/" style="color: black; background: none; text-decoration: none; font-weight: normal;">Agileco, LLC</a>.</div></td>
  </tr>
</table>
</div> 
</body>
</html>
