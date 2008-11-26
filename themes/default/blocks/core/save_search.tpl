<HTML>
<HEAD>
<TITLE>{translate}search_save{/translate}</TITLE>
<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css">
{literal}<SCRIPT LANGUAGE="JavaScript">
<!-- START
	// MINI WINDOW CONTROLLER
	function search_save(search_id,module)
	{
		var search_id 	= document.savesearch.search_id.value;
		var module		= document.savesearch.module.value;	
		var save_name	= document.savesearch.save_name.value;
		if(save_name == '')
		{
			alert('You must enter a nickname for this saved search!');
			return;
		}
		var url = '?_page=core:save_search_done&_escape=&do[]='+module+'&search_id='+search_id+'&save_name='+save_name;
		settings = 'toolbar=no,status=no,width=200,height=150';
		NewWindow('SearchSavedWindow',settings,url);
			
		// close the current window
		window.close();
	}
	
	// MINI WINDOW CONTROLLER
	function NewWindow(win,settings,url)
	{
		var eval1;
		eval1 = win + '=window.open("' + url + '","' + win + '","' + settings + '");';
		eval(eval1);
	}
		
//  END -->
</SCRIPT>{/literal}
</HEAD>
<form name="savesearch" method="post" action="" >
  <p>{translate}search_save_nickname{/translate}<br>
    <a href="#" onClick="export_start()"> </a> 
    <input type="hidden" name="module" value="{$VAR.module}">
    <input type="hidden" name="search_id" value="{$VAR.search_id}">
 
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr> 
      <td> 
        <input type="text" name="save_name" >
        <a href="#" onClick="export_start()"> </a> <a href="#" onClick="export_start()"> 
        </a> </td>
    </tr>
    <tr> 
      <td><a href="#" onClick="export_start()"> 
        <input type="submit" name="Submit" value="{translate}search_save{/translate}" onClick="search_save();" class="form_button">
        </a></td>
    </tr>
    <tr> 
      <td> 
        <input type="button" name="cancel" value="{translate}cancel{/translate}" onClick="window.close()" class="form_button">
      </td>
    </tr>
  </table>
</form>
