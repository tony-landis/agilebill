	function view_jump(jmp_to,ids) 
	{
		var url = '?_page='+module+':view&id='+jmp_to +'&ids='+ids;
		if(pgescape==true) 
		url = url + '&_escape=true';
		window.location = url;	
	} 
	    	
	function view_nav_top(array,id,ids) 
	{  
		var rw = '';
		var ff = '';
		var jmp= '';		
		  
		var t=array.length-1; 
 
		
		var last = false;
		var next = false;
		var last_id = array[t];

		jmp = '<select name="search_nav" class="search_nav" onChange="view_jump(this.value,\''+ids+'\');">';
		for(i=0; i<t; i++) 
		{
			var i_actual = i+1;  
			jmp = jmp + '<option value="'+array[i]+'"';
			if(id==array[i]) 
			{
				jmp = jmp + " selected";
				last = i - 1;
				next = i + 1;
			}
			jmp = jmp + '>Record '+i_actual+' of '+t+'</option>';
			last_id = array[i];
		}	
		jmp = jmp + '</select> ';
				
		
		if (array[0] != id) { 
			rw =  	  ' <input title="First Result" type="image" src="themes/'+THEME_NAME+'/images/icons/rewnd_24.gif" onClick="view_jump(\''+array[0]+'\',\''+ids+'\');">';
			rw = rw + ' &nbsp; <input title="Last Result" type="image" src="themes/'+THEME_NAME+'/images/icons/back_24.gif" onClick="view_jump(\''+array[last]+'\',\''+ids+'\');">&nbsp;';
		} else {
			rw =  	  ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/rewnd_16.gif">';
			rw = rw + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/back_16.gif">&nbsp;';
		}
			
		if(last_id != id) {  
			ff =  	  ' &nbsp; <input title="Next Result" type="image" src="themes/'+THEME_NAME+'/images/icons/forwd_24.gif" onClick="view_jump(\'' +array[next]+ '\',\''+ids+'\');">';
			ff = ff + ' &nbsp; <input title="Last Result" type="image" src="themes/'+THEME_NAME+'/images/icons/fastf_24.gif" onClick="view_jump(\'' +last_id+ '\',\''+ids+'\');">';
		} else {
			ff =  	  ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/forwd_16.gif">';
			ff = ff + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/fastf_16.gif">';		
		} 
            									
        var ret = '<table width="350" border="0" cellspacing="5" cellpadding="1" valign="middle" align="center"><tr><td><table width="100%" border="0" cellpadding="1"><tr>';
        ret = ret + '<td valign="middle" align="right">'+ rw +'</td>';
        ret = ret + '<td valign="middle" align="center">'+ jmp +'</td><form name="search_nav" method="post" action="">';
        ret = ret + '</form><td valign="middle" align="left">'+ ff +'</td>';
        ret = ret + '</tr></table></td></tr></table>';
		return ret; 
	}
	
	
 