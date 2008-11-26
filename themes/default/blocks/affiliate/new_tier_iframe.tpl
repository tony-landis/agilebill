<HTML>
<HEAD>
	<TITLE>{$smarty.const.SITE_NAME}</TITLE>
	<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css"> 
</head>

<body style="background-color: transparent" ALLOWTRANSPARENCY="true">
<form name="form1" method="post">
  
  <table width="215" border="0" cellspacing="0" cellpadding="0" class="table_background" align="left">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=affiliate}
                field_new_commission_rate 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
			
			 
			{literal}<SCRIPT LANGUAGE="JavaScript">
			<!-- START
			{/literal}
			var tiers = {$VAR.tiers};		
			{literal}
			function gval(i) { return document.getElementById(i).value; }						
			for (i=0; i<tiers; i++)
			{
			var ii = i + 1;
			              
			  document.write('<table width="275" border="0" cellspacing="1" cellpadding="1" class="row1" align="center">');
                document.write('<tr valign="top"> ');
                  document.write('<td width="70%"> Tier '+ii+':</td>');
                  document.write('<td width="30%">'); 
                    document.write('<input type="text" id="'+i+'" name="'+i+'" value="'+parent.GetTierValueNew(i)+'" ');
					document.write(' onChange="parent.UpdateTierValueNew('+i+',gval('+i+'));"  size="8">');
                  document.write('</td>');
                document.write('</tr>');
             document.write(' </table>');
			  
			  }
			 //  END -->
			</SCRIPT>
			{/literal}
			
			 
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
 </body>
 </html>
 
