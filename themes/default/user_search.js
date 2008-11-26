	function search_nav_jump(jmp_to)
	{
		var url = '?_page='+module+':user_search_show&search_id='+search_id+'&page='+jmp_to +'&' + sort1 + '&order_by=' + order + p + COOKIE_URL;
		if(pgescape==true)
		{
		  url = url + '&_escape=true';
		}
		window.location = url;	
	}
	
	
	function search_nav_jump1(jmp_to)
	{
		//var jmp_to = document.search_nav.search_nav.selectedIndex;
		//jmp_to++;
		search_nav_jump(jmp_to);
	}	
	
    	
	function search_nav_top()
	{
		if(pages <= 1)
		{
			return "";
		} 
		else
		{
			var rw = '';
			var ff = '';
			var jmp= '';
			
			if(page > 1)
			{
				var last = page - 1;
				rw =  	  ' <input type="image" src="themes/'+THEME_NAME+'/images/icons/rewnd_24.gif" onClick="search_nav_jump(1);">';
				rw = rw + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/back_24.gif" onClick="search_nav_jump(' +last+ ');">&nbsp;';
			} else {
				rw =  	  ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/rewnd_24.gif">';
				rw = rw + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/back_24.gif">&nbsp;';
			}
			
			if(page < pages)
			{
				var next = page;
				next++;
				ff =  	  ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/forwd_24.gif" onClick="search_nav_jump(' +next+ ');">';
				ff = ff + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/fastf_24.gif" onClick="search_nav_jump(' +pages+ ');">';
			} else {
				ff =  	  ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/forwd_24.gif">';
				ff = ff + ' &nbsp; <input type="image" src="themes/'+THEME_NAME+'/images/icons/fastf_24.gif">';		
			}	
			
			jmp = '<select name="search_nav" class="search_nav" onChange="search_nav_jump1(this.value);">';		
			for(cc=1; cc <= pages; cc++)
			{		
				jmp = jmp + '<option value="' +cc+ '"';
				if(page==cc) jmp = jmp + " selected";
				jmp = jmp + '>Page '+cc+' of '+pages+'</option>';
			}	
			jmp = jmp + '</select> ';
            									
            var ret = '<table width="300" border="0" cellspacing="5" cellpadding="1" valign="middle" align="center"><tr><td><table width="100%" border="0" cellpadding="1"><tr>';
            ret = ret + '<td valign="middle" align="right">'+ rw +'</td>';
            ret = ret + '<td valign="middle" align="center">'+ jmp +'</td><form name="search_nav" method="post" action="">' + COOKIE_FORM;
            ret = ret + '</form><td valign="middle" align="left">'+ ff +'</td>';
            ret = ret + '</tr></table></td></tr></table>';
			return ret;
		}
	}
	
	
	
	// search heading handler
	function search_heading(title,field)
	{		
		if(order == field)
		{
			if(sort1 == "desc=")
			{
				var returns = '<a href="?_page='+module+':user_search_show&search_id='+search_id+'&page='+page+'&asc=&order_by='+field+''+p+COOKIE_URL+'" class="table_heading_txt"><u>'+title+'</u> <img src="themes/' + THEME_NAME + '/images/db_desc.gif" border="0"></a>';
				return returns;
			}
			if(sort1 == "asc=")
			{
				var returns = '<a href="?_page='+module+':user_search_show&search_id='+search_id+'&page='+page+'&desc=&order_by='+field+''+p+COOKIE_URL+'" class="table_heading_txt"><u>'+title+'</u> <img src="themes/' + THEME_NAME + '/images/db_asc.gif" border="0"></a>';
				return returns;
			}
		}
		else
		{
			var returns = '<a href="?_page='+module+':user_search_show&search_id='+search_id+'&page='+page+'&asc=&order_by='+field+''+p+COOKIE_URL+'" class="table_heading_txt">'+title+'</a>';
		}
		return returns;
	}
	
		
	
	// Function to handle select/unselect of values
	function row_sel(id,type,style)
	{		
		if(type == 0)
		{
			eval('document.form1.record'+id+'.checked = false;');
			class_change("row"+id, style);	
			return;		
		}
		
		eval('var checked = document.form1.record'+id+'.checked;');
		if(checked == false)
		{
			eval('document.form1.record'+id+'.checked = true;');			
			class_change("row"+id, "row_select");	
			return;								
		} else {
			eval('document.form1.record'+id+'.checked = false;');	
			class_change("row"+id, "row_select");	
			return;
		}
	}
	
	
	
	// handles the mouseover event for row...
	function row_mouseover(id,style1,style2)
	{
		eval('var checked = document.form1.record'+id+'.checked;');
		if(checked == false)
		{		
			class_change("row"+id, style2);	
			return;								
		} else {
			class_change("row"+id, style1);	
			return;
		}
	}
	
	
	
	// handles the mouseover event for row...
	function row_mouseout(id,style1,style2)
	{
		eval('var checked = document.form1.record'+id+'.checked;');
		if(checked == false)
		{		
			class_change("row"+id, style1);
			return;									
		} else {
			class_change("row"+id, style2);	
			return;
		}
	}	
	
	
		
	// selects all the records
	function all_select()
	{
		for(i=0; i < limit; i++)
		{
			if(record_arr[i] != undefined)
			{
				eval('document.form1.record'+record_arr[i]+'.checked = true;');
				class_change("row"+record_arr[i],"row_select");
			}
		}
	}
		
	
	
	
	
	// unselects all the records
	function all_deselect()
	{
		var c = true;
		for(i=0; i < limit; i++)
		{
			if(record_arr[i] != undefined)
			{
				eval('document.form1.record'+record_arr[i]+'.checked = false;');
				if(c)
				{
					class_change("row"+record_arr[i],"row1");
					c = false;
				}
				else
				{
					class_change("row"+record_arr[i],"row2");
					c = true;
				}
			}
		}
	}
	
	


	// select all the records between the first & last selected rows	
	function all_range_select()
	{
		var start = false;
		var end   = false;
		
		for(i=0; i < limit; i++)
		{
			if(record_arr[i] != undefined)
			{
				eval('var checked = document.form1.record'+record_arr[i]+'.checked;');
				if(checked != "")
				{
					if(!start)
					{
						start = true;
					} 
					else 
					{
						end   = true;
					}
				} 
				else 
				{
					if((start == true) && (end == false))
					{
						eval('document.form1.record'+record_arr[i]+'.checked = true;');
						class_change("row"+record_arr[i],"row_select");					
					}
					else
					{
						start = false;
						end   = false;
					}
				}
			}
		}	
	}
	
	
	



	// Mass update, view, and delete controller
	function mass_do(doit, page, limit, module)
	{		
		var count = 0;
		var id = "";
		
		for(i=0; i < limit; i++)
		{
			if(record_arr[i] != undefined)
			{
				eval('var checked = document.form1.record'+record_arr[i]+'.checked;');
				eval('var this_id = document.form1.record'+record_arr[i]+'.value;');
				if(checked != "")
				{
					count++;
					id = id + this_id + ","; 				
				}
			}
		}	
			
   		if(count == 0)
   		{
   			alert("You must first select some records for this action!");
   			return;
   		}

		
		if(count > 0)
		{
			var url = '?_page=' + page + '&id=' + id + '' + COOKIE_URL;
			if(doit != '')
			{
				url = url + '&do[]=' + module + ':' + doit;
						
				if(doit == "delete")
				{
					if(count == 1)
					{
						temp = window.confirm("Are you sure you wish to delete this record?");
						window.status=(temp)?'confirm:true':'confirm:false';
					}
					else
					{
						temp = window.confirm("Are you sure you wish to delete these records?");
						window.status=(temp)?'confirm:true':'confirm:false';
					}
					if(temp == false) return;
					
				}
			}
			window.location = url;	
		}
	}
		
	
	var nav4 = document.search_results ? true : false;	
	function key_handler(e) 
	{
		// Navigator 4.0x
	  	if (nav4) 
		{
	    var whichCode = e.which;
	  	}
		else
		{
			// Internet Explorer 4.0x
	    	if (e.type == "keypress") 
			{
	      		var whichCode = e.keyCode;
			}
			else
			{
				var whichCode = e.button;
			}
		}
	
	 	if (e.type == "keypress")
		{
			var pressed = String.fromCharCode(whichCode);
		}
		else
		{
			var pressed = whichCode;
		}
		
		// determine the function to run, if any...
		if(pressed == 's' || pressed == 'S')
		{
			all_select(record_arr,limit);
		}
		
		if(pressed == 'd' || pressed == 'D')
		{
			all_deselect(record_arr,limit);
		}	
		
		if(pressed == 'r' || pressed == 'R')
		{
			all_range_select(record_arr,limit);
		}	
			
		if(pressed == 'x' || pressed == 'X')
		{
			mass_do('delete', module+ ":user_search_show&search_id=" +search_id+ "&page=" +page+ "&order_by=" +order+ "&" +sort1, limit, module);
		}	
		
		if(pressed == 'v' || pressed == 'V')
		{
			mass_do('',module+":view", limit, module);
		}					
	}		
