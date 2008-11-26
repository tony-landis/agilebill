<HTML>
<HEAD>
<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css">
<title>Export Search Results</title></HEAD>
<body class="body">
<p>
  {literal}
  <SCRIPT LANGUAGE="JavaScript">
	<!-- START
	function export_start()
	{
		var module 		= document.export1.module.value;
		var format 		= document.export1.format.value;
		var type 		= document.export1.type.value;
		var search_id 	= document.export1.search_id.value;
		var page 		= document.export1.page.value;
		var order_by 	= document.export1.order_by.value;
		var sort1 		= document.export1.sort1.value;
		var win 		= 'ExportSearch' + format;
		var COOKIE_URL	= '{/literal}{literal}';
		var settings 	= 'height=800, width=800, resizable=no, toolbar=yes, status=yes';		
		var url 		= '?_escape=&_page=core:export_search_'+type+'&type='+type+'&format='+format+'&module='+module+'&search_id='+search_id+'&page='+page+'&order_by='+order_by+'&_escape=1&_escape_next=1&'+sort1 + COOKIE_URL;
	  
		document.location = url; 
		//window.close();
	}	
//  END -->
</SCRIPT>
  {/literal}
<form name="export1" method="post" action="">
  
   
  <table width="100%" border="0" cellspacing="5" cellpadding="1" class="body">
    <tr> 
      <td width="72%"><b> 
        {translate}
        export_format 
        {/translate}
        </b></td>
    </tr>
    <tr> 
      <td width="72%" valign="top"><b> 
        <select name="format" >
          <option value="excel"> 
          {translate}
          export_format_excel 
          {/translate}
          </option>
          {if $VAR.module == "invoice"}
          <option value="pdf"> 
          {translate}
          export_format_pdf 
          {/translate}
          </option>
          {/if}
          <option value="csv"> 
          {translate}
          export_format_csv 
          {/translate}
          </option>
          <option value="tab"> 
          {translate}
          export_format_tab 
          {/translate}
          </option>
          <option value="xml"> 
          {translate}
          export_format_xml 
          {/translate}
          </option>
        </select>
        </b> </td>
    </tr>
    <tr> 
      <td width="72%" valign="top"> <b>
        {translate}
        export_pages 
        {/translate}
        </b> </td>
    </tr>
    <tr> 
      <td width="72%" valign="top"> 
        <select name="page" >
          <option value="0" selected> 
          {translate}
          export_pages_all 
          {/translate}
          </option>
          <option value="{$VAR.page}"> 
          {translate}
          export_pages_current 
          {/translate}
          </option>
        </select>
      </td>
    </tr>
  </table>
  
    <input type="hidden" name="module" value="{$VAR.module}">
    <input type="hidden" name="search_id" value="{$VAR.search_id}">
    <input type="hidden" name="order_by" value="{$VAR.order}">
    <input type="hidden" name="sort1" value="{$VAR.sort}">
    <input type="hidden" name="type" value="display">
    <input type="hidden" name="_escape" value="1">
	<input type="hidden" name="_escape_next" value="1">
 
  </form>
<table width="50" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr> 
    <td><a href="#" onClick="export_start()">
      <input type="image" src="themes/{$THEME_NAME}/images/icons/forwd_24.gif" onClick="export_start()" name="image2">
      </a> </td>
    <td> 
      <div align="right"><a href="#" onClick="export_start()">
        <input type="image" src="themes/{$THEME_NAME}/images/icons/cancl_24.gif" onClick="window.close()" name="image">
        </a></div>
    </td>
  </tr>
</table>
