<?php
	
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */
	
class CORE_search
{

	var $recent_js;
	var $recent_menu;
	var $saved_js;
	var $saved_menu;



	/**
	* Create a new search record.
	*
	* @return 	void
	* @since 	Version 1.0
	* @param	array Contains the elements of the search query
	*/	

	function add($arr)
	{
		$db = &DB(); 
		# determine the search id:
		$this->id = $db->GenID(AGILE_DB_PREFIX . 'search_id');

		# safely store the SQL Query:
		$sql = $db->qstr($arr['sql']);


		# set the time when this record expires
		$date_expire = (time() + (SEARCH_EXPIRE*60));

		# create the search record
		$q  = "INSERT INTO " . AGILE_DB_PREFIX . "search SET
			id			= '" . $this->id . "',
			site_id 	= '" . DEFAULT_SITE . "',
			session_id 	= '" . SESS . "',
			account_id 	= '" . SESS_ACCOUNT . "',
			module		= '" . $arr['module'] . "',
			date_orig	= '" . time() . "',
			date_expire = '" . $date_expire . "',
			full_sql	= $sql,
						order_by	= '" . $arr['order_by'] . "',
			limit_no	= '" . $arr['limit'] . "',
			results		= '" . $arr['results'] . "'";
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('search.inc.php','add', $db->ErrorMsg());
		}
	}


	/**
	* Retrieves a specific search record, and sets the values to the object.
	* 
	* @return 	void
	* @since 	Version 1.0
	* @todo		Complete the search refresh feature
	* @param	int Contians the Search Id to be retrieved
	*/	

	function get($id)
	{
		# get the details for this search
		$db = &DB();
		$q  = "SELECT *
				FROM " . AGILE_DB_PREFIX . "search
				WHERE
				id			= '" . $id . "'
				AND
				site_id 	= '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('search.inc.php','get', $db->ErrorMsg());
		}

		# get the search values
		$this->id			=   $id;
		$this->account      =   $result->fields['account_id'];
		$this->session      =   $result->fields['session_id'];  
		$this->date_orig	=	$result->fields['date_orig'];
		$this->date_expire	=	$result->fields['date_expire'];
		$this->sql			=	$result->fields['full_sql'];
		$this->order_by		=	$result->fields['order_by'];
		$this->limit		=	$result->fields['limit_no'];


		# check if this search has expired:
		if($this->date_expire <= time())
		{
			# refresh the search
			# $this->results 	=	$this->refresh($id);
			# echo "<BR> this search has expired! Refreshing.... <BR>";
			$this->results		=	$result->fields['results'];
		}
		else
		{
			# use the existing result count
			$this->results		=	$result->fields['results'];
		}
		return;
	}


	/**
	* Refreshes the result count of a specific search and stores the new results in the search record,
	* and returns the new search result count. 
	* 
	* @return 	int Contains the new search results count
	* @since 	Version 1.0
	* @todo		Complete the search refresh code
	* @param	int Contians the Search Id to be refreshed
	* @return	int	The new search results count
	*/

	function refresh($id)
	{

	}



	/**
	* Saves the current search for later retreival.
	* 
	* @return 	void
	* @since 	Version 1.0
	* @todo		Add some error checking for previously used nicknames, identical searches, etc.
	* @param	int Contians the Search Id to be saved
	* @param	string Contains the name of the Module this search was for
	* @param	string Contains search nickname to remember this search as
	*/

	 function save($search_id,$module,$name)
	 {
		# save the search
		$db = &DB();

		# determine the search id:
		$this->id = $db->GenID('search_saved');

		$n = $db->qstr($name);

		# generate the insert statement	 	
		$q  = "INSERT INTO " . AGILE_DB_PREFIX . "search_saved SET
				id 			= '$this->id',
				site_id 	= '" . DEFAULT_SITE . "',
				search_id   = '$search_id',
				account_id	= '" . SESS_ACCOUNT . "',
				session_id	= '" . SESS . "',
				date_orig	= '" . time() . "',
				date_last	= '" . time() . "',
				date_expire	= '',
				module		= '$module',
				name		= $n";
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('search.inc.php','refresh', $db->ErrorMsg());
		}	 
	 }



	/**
	* Build the recent search menu and JavaScript
	* 
	* @return 	void
	* @since 	Version 1.0
	* @param	string Contains the name of the Module to find recent searches for
	*/

	function build_recent($module)
	{
		# disable for now
		return 0;

		if(isset($this->arr)) unset ($this->arr);

		# get the recent searches
		$db = &DB();
		$q  = "SELECT id, date_orig, date_expire, full_sql, order_by, limit_no
				FROM " . AGILE_DB_PREFIX . "search
				WHERE
				session_id	= '" . SESS . "'
				OR
				account_id	= '" . SESS_ACCOUNT . "'
				AND
				module		= '$module'
				AND
				date_expire >= '" . time() . "'
				AND
				site_id 	= '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('search.inc.php','build_recent', $db->ErrorMsg());
		}

		$results = $result->RecordCount();


			$i  = 0;
			while (!$result->EOF) {
				# get the fields for this loop
				$sql = $result->fields['full_sql'];

				# remove the WHERE
				$sql = trim($sql);
				$sql = preg_replace("/WHERE/i","",$sql);
				$sql = preg_replace("/AND$/i","",$sql);
				$sql = trim($sql);

				# replace any sql statements before we split the string
				$sql = preg_replace("/ = /","===",$sql);
				$sql = preg_replace("/ LIKE /","===",$sql);


				# determine the number of fields

				$ii=0;
				if(preg_match("/ AND /", $sql))
				{
					$sql = explode(" AND ",$sql);
					$this_fields = count($sql);

					# loop
					for($count=0; $count < $this_fields; $count++)
					{
						# do each field
						$sqls = explode("==",$sql[$count]);
						$field[$count][name]  = $sqls[0];
						$field[$count][value] = preg_replace("/'/","",$sqls[1]);
						$field[$count][value] = preg_replace("/=/","",$field[$count][value]);

						# check that the name & value are both set...
						if($field[$count][value] != '' && $field[$count][name] != '')
						{
							if(!isset($this->arr[$i][$ii][limit]))
							{
								$this->arr[$i][$ii][search] = $result->fields['id'];
								$this->arr[$i][$ii][limit] = $result->fields['limit_no'];
								$this->arr[$i][$ii][order] = $result->fields['order_by'];
							}

						   $this->arr[$i][$ii][0] = $field[$count][name];
						   $this->arr[$i][$ii][1] = $field[$count][value];

							# echo "<BR><BR>Field/Name: " . $this->arr[$i][$ii][0] . " -> "  . $this->arr[$i][$ii][1];
							$ii++;

							# set the menu up for Smarty
							$this->recent_menu[$i] = $result->fields;
						}
					}
				}
				else
				{
					# field count
					$this_fields = 1;

					# do this one field
					$sqls = explode("==",$sql);
					$field[name]  = $sqls[0];
					$field[value] = preg_replace("/'/","",$sqls[1]);
					$field[value] = preg_replace("/=/","",$field[value]);

					# check that the name & value are both set...
					if($field[value] != '' && $field[name] != '')
					{
						if(!isset($this->arr[$i][$ii][limit]))
						{
							$this->arr[$i][$ii][search] = $result->fields['id'];
							$this->arr[$i][$ii][limit] = $result->fields['limit_no'];
							$this->arr[$i][$ii][order] = $result->fields['order_by'];
						}

						$this->arr[$i][$ii][0] = $field[name];
						$this->arr[$i][$ii][1] = $field[value];

						# echo "<BR><BR>Field/Name: " . $field[name] . " -> "  . $field[value];
						$ii++;

						# set the menu up for Smarty
						$this->recent_menu[$i] = $result->fields;
					}

				}


				# continue loop
				$result->MoveNext();
				if ($ii > 0) $i++;
			}

		# finish the JS:
		if($i > 0 && $ii > 0)
		{
			# build the JavaScript
			$this->recent_js = '
			<script language="JavaScript"> 

			// SEARCH FORM CONTROLLER
			function fill_search_recent(mod,fields,field_count,limit,order,s,c)
			{
				document.search.reset();
				var id = document.search_recent.search_id.selectedIndex;
				if(id == 0) return "";
				var idx = document.search_recent.search_id.options[id].value;       
				for(loop=0; loop <= c; loop++)
				{
					if(s[loop] == idx) 
					{
						var i = loop;
					}
				}
				document.search.limit.value    = limit[i];
				document.search.order_by.value = order[i];  
				for(loop=0; loop < field_count[i]; loop++)
				{
					var fill = "document.search." +  mod + "_" + fields[i][loop][0] + ".value = fields[i][loop][1];"
					eval(fill);
				}                           
			}'; 

			$this->recent_js .= "
			var mod = '$module';
			var c = $i;
			var fields = new Array($i);
			var limit = new Array($i);
			var order = new Array($i);
			var field_count = new Array($i);
			var s = new Array($i);
			";

			# loop through the searches
			for ($ix = 0; $ix <= count($this->arr); $ix++)
			{
				# loop through the fields
				for ($iix = 0; $iix <= count($this->arr[$ix]); $iix++)
				{

				# check that the name/value is set...
				if( $this->arr[$ix][$iix][0] != "" &&  $this->arr[$ix][$iix][1] != "")
				{
					$count = count($this->arr[$ix]);

					# setup the arrays:
					if($iix==0)
					{
					$this->recent_js .= "
					s[$ix]         		= '" . $this->arr[$ix][$iix][search] . "';
					limit[$ix]          = '" . $this->arr[$ix][$iix][limit] . "';
					order[$ix]          = '" . $this->arr[$ix][$iix][order] . "';
					field_count[$ix]= '" . $count . "';
					fields[$ix]         = new Array(field_count[$ix]);
					";
					}

					# set the field settings
					$this->recent_js .=
					"
					fields[$ix][$iix]       = new Array(2);
					fields[$ix][$iix][0]    = '" . $this->arr[$ix][$iix][0] . "';
					fields[$ix][$iix][1]    = '" . $this->arr[$ix][$iix][1] . "';
					";
					}
				}
			}

			# finish the js
			$this->recent_js .= "
			</script>
			";
		}
		else
		{
			$this->recent_js = FALSE;
		}
	} # end of functino




	/**
	* Build the saved search menu and JavaScript
	* 
	* @return 	void
	* @since 	Version 1.0
	* @param	string Contains the name of the Module to find saved searches for
	*/

	function build_saved($module)
	{
		 # disable for now
		return 0;

	if(isset($this->arr)) unset ($this->arr);

	# get the saved searches
	# get the recent searches
	$db1 = &DB();
	$q  = "SELECT id, search_id, name
			FROM " . AGILE_DB_PREFIX . "search_saved
			WHERE
			session_id	= '" . SESS . "'
			OR
			account_id	= '" . SESS_ACCOUNT . "'
			AND
			module		= '$module'
			AND
			site_id 	= '" . DEFAULT_SITE . "'
			ORDER BY name ASC";
	$result1 = $db1->Execute($q);

	# error reporting
	if ($result1 === false)
	{
		global $C_debug;
		$C_debug->sql_error($db1->ErrorMsg());
	}

	$i=0; 
	while (!$result1->EOF) 
	{	
		# get the information for this search
		$db = &DB();
		$q  = "SELECT id, full_sql, order_by, limit_no
				FROM " . AGILE_DB_PREFIX . "search
				WHERE
				id			= '" . $result1->fields['search_id'] . "'
				AND
				site_id 	= '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('search.inc.php','build_saved', $db->ErrorMsg());
		}

				# get the fields for this loop
				$sql = $result->fields['full_sql'];

				# remove the WHERE
				$sql = trim($sql);
				$sql = preg_replace("/WHERE/i","",$sql);
				$sql = preg_replace("/AND$/i","",$sql);
				$sql = trim($sql);

				# replace any sql statements before we split the string
				$sql = preg_replace("/ = /","===",$sql);
				$sql = preg_replace("/ LIKE /","===",$sql);


				# determine the number of fields

				$ii=0;
				if(preg_match("/ AND /", $sql))
				{
					$sql = explode(" AND ",$sql);
					$this_fields = count($sql);

					# loop
					for($count=0; $count < $this_fields; $count++)
					{
						# do each field
						$sqls = explode("==",$sql[$count]);
						$field[$count][name]  = $sqls[0];
						$field[$count][value] = preg_replace("/'/","",$sqls[1]);
						$field[$count][value] = preg_replace("/=/","",$field[$count][value]);

						# check that the name & value are both set...
						if($field[$count][value] != '' && $field[$count][name] != '')
						{
							if(!isset($this->arr[$i][$ii][limit]))
							{
								$this->arr[$i][$ii][search] = $result->fields['id'];
								$this->arr[$i][$ii][limit] = $result->fields['limit_no'];
								$this->arr[$i][$ii][order] = $result->fields['order_by'];
							}

							$this->arr[$i][$ii][0] = $field[$count][name];
							$this->arr[$i][$ii][1] = $field[$count][value];

							# echo "<BR><BR>Field/Name: " . $this->arr[$i][$ii][0] . " -> "  . $this->arr[$i][$ii][1];
							$ii++;
							$this->saved_menu[$i] = $result->fields;
							$this->saved_menu[$i]["name"] = $result1->fields["name"];
						}
					}
				}
				else
				{
					# field count
					$this_fields = 1;

					# do this one field
					$sqls = explode("==",$sql);
					$field[name]  = $sqls[0];
					$field[value] = preg_replace("/'/","",$sqls[1]);
					$field[value] = preg_replace("/=/","",$field[value]);

					# check that the name & value are both set...
					if($field[value] != '' && $field[name] != '')
					{
						if(!isset($this->arr[$i][$ii][limit]))
						{
							$this->arr[$i][$ii][search] = $result->fields['id'];
							$this->arr[$i][$ii][limit] = $result->fields['limit_no'];
							$this->arr[$i][$ii][order] = $result->fields['order_by'];
						}

						$this->arr[$i][$ii][0] = $field[name];
						$this->arr[$i][$ii][1] = $field[value];

						#	echo "<BR><BR>Field/Name: " . $field[name] . " -> "  . $field[value];
						$ii++;

						# set the menu up for Smarty
						$this->saved_menu[$i] = $result->fields;
						$this->saved_menu[$i]["name"] = $result1->fields["name"];
					}

				}
		$result1->MoveNext();  
		if ($ii > 0) $i++;              
	}




		# finish the JS:
		if($i > 0 && $ii > 0)
		{
			# build the JavaScript
			$this->saved_js = '
			<script language="JavaScript"> 

			// SEARCH FORM CONTROLLER
			function fill_search_saved(s_mod,s_fields,s_field_count,s_limit,s_order,s_s,s_c)
			{
				document.search.reset();
				var id = document.search_saved.search_id.selectedIndex;
				if(id == 0) return "";
				var idx = document.search_saved.search_id.options[id].value;       
				for(loop=0; loop <= s_c; loop++)
				{
					if(s_s[loop] == idx) 
					{
						var i = loop; 
					}
				}
				document.search.limit.value    = s_limit[i];
				document.search.order_by.value = s_order[i];  
				for(loop=0; loop < s_field_count[i]; loop++)
				{
					var fill = "document.search." +  s_mod + "_" + s_fields[i][loop][0] + ".value = s_fields[i][loop][1];"
					eval(fill);
				}
			}'; 

			$this->saved_js .= "
			var s_mod = '$module';
			var s_c = $i;
			var s_fields = new Array($i);
			var s_limit = new Array($i);
			var s_order = new Array($i);
			var s_field_count = new Array($i);
			var s_s = new Array($i);
			";

			# loop through the searches
			for ($ix = 0; $ix <= count($this->arr); $ix++)
			{
				# loop through the fields
				for ($iix = 0; $iix <= count($this->arr[$ix]); $iix++)
				{

				# check that the name/value is set...
				if( $this->arr[$ix][$iix][0] != "" &&  $this->arr[$ix][$iix][1] != "")
				{
					$count = count($this->arr[$ix]);

					# setup the arrays:
					if($iix==0)
					{
					$this->saved_js .= "
					s_s[$ix]         		= '" . $this->arr[$ix][$iix][search] . "';
					s_limit[$ix]          = '" . $this->arr[$ix][$iix][limit] . "';
					s_order[$ix]          = '" . $this->arr[$ix][$iix][order] . "';
					s_field_count[$ix]= '" . $count . "';
					s_fields[$ix]         = new Array(s_field_count[$ix]);
					";
					}

					# set the field settings
					$this->saved_js .=
					"
					s_fields[$ix][$iix]       = new Array(2);
					s_fields[$ix][$iix][0]    = '" . $this->arr[$ix][$iix][0] . "';
					s_fields[$ix][$iix][1]    = '" . $this->arr[$ix][$iix][1] . "';
					";
					}
				}
			}

			# finish the js
			$this->saved_js .= "
			</script>
			";
		}
		else
		{
			$this->saved_js = FALSE;
		}
	}
}
?>