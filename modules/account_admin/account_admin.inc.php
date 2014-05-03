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
	
class account_admin
{

	# Open the constructor for this mod
	function account_admin()
	{
		# name of this module:
		$this->module = "account_admin";

		if(!defined('AJAX')) 
		{            	
			# location of the construct XML file:
			$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

			# open the construct file for parsing 
			$C_xml = new CORE_xml;
			$construct = $C_xml->xml_to_array($this->xml_construct);

			$this->method   = $construct["construct"]["method"];
			$this->trigger  = $construct["construct"]["trigger"];
			$this->field    = $construct["construct"]["field"];
			$this->table 	= $construct["construct"]["table"];
			$this->module 	= $construct["construct"]["module"];
			$this->cache	= $construct["construct"]["cache"];
			$this->order_by = $construct["construct"]["order_by"];
			$this->limit	= $construct["construct"]["limit"];
		}
	}

	/**
	* Check account limitations
	*/
	function checkLimits() { 
		if(!defined('AGILE_RST_ACCOUNT') || AGILE_RST_ACCOUNT <= 0) return true; 
		$sql="SELECT count(*) as totalacct from ".AGILE_DB_PREFIX."account WHERE site_id=".DEFAULT_SITE;
		$db=&DB();
		$rs=$db->Execute($sql);  
		if($rs && $rs->RecordCount() && $rs->fields['totalacct'] <= AGILE_RST_ACCOUNT) {
			return true;
		} else { 
			echo "Licensed user limit of ".AGILE_RST_ACCOUNT." exceeded, operation failed.";
			return false;
		}
		return true;
	}        

	/* BEGIN: custom product/group searching method */

	function group_search($VAR) {
		$sql = '';
		echo "<pre>"; 

		// get date ranges:
		foreach($VAR['dates']['val'] as $cond => $val) 
		{
			if($val > 0) {
				$exp = $VAR['dates']['expr'][$cond]; 
				$val = $this->convert_date($val,false);

				if(!empty($sql)) $sql .= " AND "; else $sql = " ";
				$sql .= " A.date_orig $exp $val ";
			} 
		}        
		if(!empty($sql)) $sql = " ( $sql ) ";	

		// get group(s)
		if(!empty($VAR['groups'])) {
			foreach($VAR['groups'] as $group ) 
			{  
				if($group != 0) {
					if(!empty($sql2)) $sql2 .= " OR "; else $sql2 = " ";
					$sql2 .= " B.group_id = $group ";  
				}
			}
		}
		if(!empty($sql2)) {
			if(!empty($sql)) $sql .= " AND \r\n";
			$sql .= " ( $sql2 ) AND ( A.id = B.account_id AND B.active = 1 ) ";
		}

		// Assemble SQL:
		$q = "SELECT DISTINCT A.* FROM 
			". AGILE_DB_PREFIX ."account as A,
			". AGILE_DB_PREFIX ."account_group as B
			WHERE (
			A.site_id = ". DEFAULT_SITE ." AND
			B.site_id = ". DEFAULT_SITE ." ) ";        	
		if(!empty($sql)) $q .= " AND " . $sql;
		$db = &DB();
		$rs = $db->Execute($q);	 

		// print results in text format      	
		if($rs && $rs->RecordCount() > 0) {
			while(!$rs->EOF) {        			
				echo $rs->fields['first_name'] .', '.$rs->fields['last_name'] .', '.$rs->fields['email'] .', '.$rs->fields['company'] .",\r\n";        		
				$rs->MoveNext();
			}
		} else {
			echo "<B>No matches</B>!";
		}
		echo "</pre>";
	} 



	function product_search($VAR) {
		$sql = '';
		echo "<pre>"; 

		// get date ranges:
		if(!empty($VAR["dates"]))
		{
			foreach($VAR['dates']['val'] as $cond => $val) 
			{
				if($val > 0) {
					$exp = $VAR['dates']['expr'][$cond]; 
					$val = $this->convert_date($val,false);

					if(!empty($sql)) $sql .= " AND "; else $sql = " ";
					$sql .= " B.date_orig $exp $val ";
				} 
			}
		}        
		if(!empty($sql)) $sql = " ( $sql ) ";	

		// get group(s)
		if(!empty($VAR['products'])) {
			foreach($VAR['products'] as $prod ) 
			{  
				if($prod != 0) {
					if(!empty($sql2)) $sql2 .= " OR "; else $sql2 = " ";
					$sql2 .= " B.product_id = $prod ";  
				}
			}
		}
		if(!empty($sql2)) {
			if(!empty($sql)) $sql .= " AND \r\n";
			$sql .= " ( $sql2 ) AND ( A.id = C.account_id AND C.id =  B.invoice_id ) ";
		}

		// Assemble SQL:
		$q = "SELECT DISTINCT A.* FROM 
			". AGILE_DB_PREFIX ."account as A,
			". AGILE_DB_PREFIX ."invoice_item as B,
			". AGILE_DB_PREFIX ."invoice as C
			WHERE (
			A.site_id = ". DEFAULT_SITE ." AND
			C.site_id = ". DEFAULT_SITE ." AND
			B.site_id = ". DEFAULT_SITE ." ) ";        	
		if(!empty($sql)) $q .= " AND " . $sql;
		$db = &DB();
		$rs = $db->Execute($q);	 


		// print results in text format      	
		if($rs && $rs->RecordCount() > 0) {
			while(!$rs->EOF) {        			
				echo $rs->fields['first_name'] .', '.$rs->fields['last_name'] .', '.$rs->fields['email'] .', '.$rs->fields['company'] .",\r\n";        		
				$rs->MoveNext();
			}
		} else {
			echo "<B>No matches</B>!";
		}
		echo "</pre>";
	} 

	function convert_date ($date,$field)
	{
		if($date == '0' || $date == '')
		  return '';

		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);

		for($i=0; $i<3; $i++)
		{
			if($Arr_format[$i] == 'd')
				$day = $Arr_date[$i];

			if($Arr_format[$i] == 'm')
				$month = $Arr_date[$i];

			if($Arr_format[$i] == 'Y')
				$year = $Arr_date[$i];
		}

		$timestamp = mktime(0, 0, 0, $month, $day, $year);
		return $timestamp;	
	}        

	/* END: custom product/group searching method */








	###########################################
	### AJAX Auto-selector
	###########################################

	function autoselect($VAR)
	{                 	
		if(!$this->checkLimits()) return false; // check account limits

		$db = &DB();
		$p = AGILE_DB_PREFIX;

		if (empty($VAR['account_search'])) {
		   $where = "id > 0";
		   $type = 1;
		} elseif (is_numeric($VAR['account_search'])) {
			$where = "id LIKE ".$db->qstr($VAR['account_search']."%");               
			$type = 1;
		} elseif (preg_match("/ /", $VAR['account_search'])) {
			$arr = explode(" ", $VAR['account_search']);
			$where = "first_name =    ".$db->qstr($arr[0])." AND ".
					 "last_name LIKE  ".$db->qstr($arr[1].'%') ;
			$type = 2;
		} elseif (preg_match("/@/", $VAR['account_search'])) { 
			$where = "email LIKE ".$db->qstr('%'.$VAR['account_search'].'%') ;
			$type = 3;

		} else {
			$where = "username LIKE   ".$db->qstr($VAR['account_search'].'%')." OR ".
					 "first_name LIKE ".$db->qstr($VAR['account_search'].'%')." OR ".
					 "last_name LIKE ".$db->qstr($VAR['account_search'].'%') ;
			$type = 4;
		}

		$q = "SELECT id,email,first_name,last_name,username FROM {$p}account WHERE 
				( $where )
			  AND  
				site_id = " . DEFAULT_SITE."
			  ORDER BY first_name,last_name";   
		$result = $db->SelectLimit($q,10);          

		# Create the alert for no records found
		echo '<ul>';            
		# Create the alert for no records found
		if ($result->RecordCount() > 0)  { 
			$i=0;  
			while(!$result->EOF) 
			{ 
				echo '<li><div class="name"><b>' . $result->fields['first_name'].' '.$result->fields['last_name'] . '</b></div>'.
					'<div class="email"><span class="informal">'.$result->fields['email']. '</span></div>'.
					'<div class="index" style="display:none">'.$result->fields['id']. '</div></li>' . "\r\n"; 
				$result->MoveNext();
				$i++;
			} 
		}  
		echo "</ul>";

	} 


	###########################################
	### Login as user
	###########################################

	function login($VAR)
	{
		global $C_auth;

		# Check for target user
		$display_this=false;
		if(!empty($VAR['account_id']))
		{
//			var_dump($VAR);
			### Get any authorized groups of the target account
			$dba = &DB();
			$sql = 'SELECT group_id FROM ' . AGILE_DB_PREFIX . 'account_group WHERE
					site_id      = ' . $dba->qstr(DEFAULT_SITE) . ' AND
					account_id   = ' . $dba->qstr($VAR['account_id']) . ' AND
					active       = ' . $dba->qstr("1") . '
					ORDER BY group_id';
			$groups = $dba->Execute($sql);
			while (!$groups->EOF) {
				$group[] = $groups->fields['group_id'];
				$groups->MoveNext();
			}

			### Verify the user has access to view this account:
			if(SESS_ACCOUNT != $VAR['account_id']) {
				$display_this = true;
				for($ix=0; $ix<count($group); $ix++)
				{
					if(!$C_auth->auth_group_by_id($group[$ix]))
						$display_this = false;
				}
			} else  {
				return false;
			}

		} else {
			return false;
		}

		# Logout current user and login as the target user
		if($display_this)
		{
			$db = &DB();
			$sql = 'SELECT username,password FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id      = ' . $dba->qstr(DEFAULT_SITE) . ' AND
					id   = ' . $dba->qstr($VAR['account_id']);
			$acct = $db->Execute($sql);
			$arr['_username'] = $acct->fields['username'];
			$arr['_password'] = $acct->fields['password'];
			include_once(PATH_CORE.'login.inc.php');
			$login = new CORE_login_handler;
//			$login->logout($VAR);
			$login->login($arr, $md5=false);
			define('REDIRECT_PAGE', '?_page=account:account&tid='.DEFAULT_THEME);
		}


		####################################################################
		### Do any db_mapping
		####################################################################
		$db 	= &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr('db_mapping') . ' AND
					status      = ' . $db->qstr("1");
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->login ( $VAR['account_id'] );
		}            
	}


	###########################################
	### Account selector list search
	###########################################

	function popup_search($VAR)
	{
		$db = &DB();
		if (empty($VAR['search'])) {
		   $where = '';
		} elseif (preg_match("/ /", $VAR['search'])) {
			$arr = explode(" ", $VAR['search']);
			$where = "first_name =    ".$db->qstr($arr[0])." AND ".
					 "last_name LIKE  ".$db->qstr('%'.$arr[1].'%')." AND ";
		} else {
			$where = "username LIKE   ".$db->qstr('%'.$VAR['search'].'%')." OR ".
					 "first_name LIKE ".$db->qstr('%'.$VAR['search'].'%')." OR ".
					 "first_name LIKE ".$db->qstr('%'.$VAR['search'].'%')." OR ".
					 "company LIKE ".   $db->qstr('%'.$VAR['search'].'%')." AND ";
		}

		$q = "SELECT id,first_name,last_name
			  FROM ".AGILE_DB_PREFIX."account
			  WHERE $where
			  site_id = '" . DEFAULT_SITE . "'";

		$q_save = "SELECT * FROM ".AGILE_DB_PREFIX."account  WHERE $where %%whereList%% ";
		$result = $db->Execute($q);

		/// DEBUG ////
		// echo "<PRE>$q</PRE>";

		# get the result count:
		$results = $result->RecordCount();

		# Create the alert for no records found
		if ($results == 0)
		{
			$id     = $result->fields['id'];
			$name   = $result->fields['first_name'].' '.$result->fields['last_name'];
			$val = $id.'|'.$name;
			$res = '
				<script language=\'javascript\'>
					window.parent.popup_clear_'.$VAR['field'].'(true);
					alert("No matches found");
					window.close();
				</script> ';
			echo $res;
		}
		else if ($results == 1)
		{
			$id     = $result->fields['id'];
			$name   = $result->fields['first_name'].' '.$result->fields['last_name'];
			$val = $id.'|'.$name;
			$res = '
				<script language=\'javascript\'>
					window.parent.popup_fill_'.$VAR['field'].'("'.$val.'");
					window.close();
				</script> ';
			echo $res;
		}
		else
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module']     = $this->module;
			$arr['sql']        = $q_save;
			$arr['limit']      = '30';
			$arr['order_by']   = 'last_name';
			$arr['results']    = $results;
			$search->add($arr);

			global $smarty;
			$smarty->assign('search_id', $search->id);
			$smarty->assign('page', '1');
			$smarty->assign('limit', $limit);
			$smarty->assign('order_by', $order_by);
			$smarty->assign('results', $results);

			$res = '
				<script language=\'javascript\'>
					 function popup_fill(val) {
						window.parent.popup_fill_'.$VAR['field'].'(val);
					 }
					 window.open("?_page=account_admin:iframe_search_show&_escape=1&search_id='.$search->id.'&page=1","account_select_popup","toolbar=no,status=no,width=400,height=500");
				</script> ';

			echo $res;

		}
	}


	###########################################
	### Top Accounts Graph:
	###########################################
	function top($VAR)
	{
		global $smarty, $C_translate, $C_auth;

		# Get the period type, default to month
		if (empty($VAR['period']))
			$p = 'm';
		else
			$p = $VAR['period'];

		# Load the jpgraph class
		include (PATH_GRAPH."jpgraph.php");
		include (PATH_GRAPH."jpgraph_bar.php");

		# check the validation for this function
		if(!$C_auth->auth_method_by_name($this->module,'search'))  {
			$error = $C_translate->translate('module_non_auth','','');
			include (PATH_GRAPH."jpgraph_canvas.php");
			$graph = new CanvasGraph(460,55,"auto");
			$t1 = new Text($error);
			$t1->Pos(0.2,0.5);
			$t1->SetOrientation("h");
			$t1->SetBox("white","black",'gray');
			$t1->SetFont(FF_FONT1,FS_NORMAL);
			$t1->SetColor("black");
			$graph->AddText($t1);
			$graph->Stroke();
			exit;
		}

		# Get the period start & end
		switch ($p)
		{
			# By Weeks:
			case 'w':
				$interval       = "1";
				$width          = ".9";
				$title          = 'Top Accounts for Last Last Week';
				$dow            = date('w');
				$start_str      = mktime(0,0,0,date('m'),      date('d')-$dow,             date('y'));
				$end_str        = mktime(23,59,59,date('m'),   date('d'),                  date('y'));
			break;

			# By Months:
			case 'm':
				$interval       = "3";
				$width          = ".6";
				$title          = 'Top Accounts for Last Last Month';
				$start_str      = mktime(0,0,0,date('m'), 1,                                date('y'));
				$end_str        = mktime(23,59,59,date('m'),   date('d'),                   date('y'));
			break;

			# By Years:
			case 'y':
				$interval       = "1";
				$width          = ".8";
				$title          = 'Top Accounts for Last Last Year';
				$start_str      = mktime(0,0,0,1,1,                            date('y'));
				$end_str        = mktime(23,59,59,     date('m'),  date('d'),  date('y'));
			break;
		}


		##############################@@@@@@@@
		# Get accounts & sales for this period
		##############################@@@@@@@@
		$db         = &DB();
		$sql   = 'SELECT account_id,total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
				   date_orig    >=  ' . $db->qstr( $start_str ) . ' AND  date_orig    <=  ' . $db->qstr( $end_str ) . ' AND
				   site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		if(@$result->RecordCount() == 0) {
			$file = fopen( PATH_THEMES.'default_admin/images/invisible.gif', 'r');
			fpassthru($file);
			exit;
		}

		while(!$result->EOF)
		{
			$amt  = $result->fields['total_amt'];
			$acct = $result->fields['account_id'];
			if(!isset( $arr[$acct] )) $arr[$acct] = 0;
			$arr[$acct] += $amt;
			$result->MoveNext();
		}

		$i = 0;
		while(list($key, $var) = each(@$arr)) { 
			# Get the user name
			$sql    = 'SELECT first_name,last_name FROM ' . AGILE_DB_PREFIX . 'account WHERE
						   id           =  ' . $db->qstr( $key ) . ' AND
						   site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs     = $db->Execute($sql);

			$_lbl[]   = strtoupper(substr($rs->fields['first_name'],0,1)) . ". " . $rs->fields['last_name'];
			$_datay[] = $var;
			$i++;
		}


		### Sort the arrays           
		array_multisort($_datay,SORT_DESC, SORT_NUMERIC, $_lbl);

		### Limit the results to 10 or less
		for($i=0; $i<count($_lbl); $i++) {
			$lbl[$i] = $_lbl[$i];
			$datay[$i] = $_datay[$i]; 
			if($i>=9) $i = count($_lbl); 
		} 

		$i = count($lbl);


		# Get the Currency
		$sql    = 'SELECT symbol FROM ' . AGILE_DB_PREFIX . 'currency WHERE
					id           =  ' . $db->qstr( DEFAULT_CURRENCY ) . ' AND
					site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs     = $db->Execute($sql);
		$currency_iso = $rs->fields['symbol'];

		// Size of graph
		$width=265;
		$height=75 + ($i*15);

		// Set the basic parameters of the graph
		$graph = new Graph($width,$height,'auto');
		$graph->SetScale("textlin");
		$graph->yaxis->scale->SetGrace(50);
		$graph->SetMarginColor('#F9F9F9');
		$graph->SetFrame(true,'#CCCCCC',1);
		$graph->SetColor('#FFFFFF');

		$top    = 45;
		$bottom = 10;
		$left   = 95;
		$right  = 15;
		$graph->Set90AndMargin($left,$right,$top,$bottom);

		// Label align for X-axis
		$graph->xaxis->SetLabelAlign('right','center','right');

		// Label align for Y-axis
		$graph->yaxis->SetLabelAlign('center','bottom');
		$graph->xaxis->SetTickLabels($lbl);

		// Titles
		$graph->title->SetFont(FF_FONT1,FS_BOLD,9.5);
		$title = $C_translate->translate('graph_top','account_admin','');
		$graph->title->Set($title);

		// Create a bar pot
		$bplot = new BarPlot($datay);
		$bplot->SetFillColor("#506DC7");
		$bplot->SetWidth(0.2);

		// Show the values
		$bplot->value->Show();
		$bplot->value->SetFont(FF_FONT1,FS_NORMAL,8);
		$bplot->value->SetAlign('center','center');
		$bplot->value->SetColor("black","darkred");
		$bplot->value->SetFormat($currency_iso.'%.2f');

		$graph->Add($bplot);
		$graph->Stroke();

		return;
	}



	##############################
	##	MAIL ONE ACCOUNT        ##
	##############################
	function mail_one($VAR)
	{
		global $C_translate, $C_debug;

		## Validate the required vars (account_id, message, subject)
		if(@$VAR['mail_account_id'] != "" && @$VAR['mail_subject'] != "" && @$VAR['mail_message'] != "")
		{
			## Verify the specified account:
			$db     = &DB();
			$sql    = 'SELECT email,first_name,last_name FROM ' . AGILE_DB_PREFIX . 'account WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr($VAR['mail_account_id']);
			$account = $db->Execute($sql);

			if($account->RecordCount() == 0)
			{
				## Error message:
				$C_debug->alert($C_translate->translate('account_non_exist','account_admin',''));
				return;
			}

			################################################################
			## OK to send the email:

			$db = &DB();
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
					site_id     = ".$db->qstr(DEFAULT_SITE)." AND
					id          = ".$db->qstr($VAR['mail_email_id']);
			$setup_email        = $db->Execute($q);

			$E['priority']      = $VAR['mail_priority'];
			$E['html']          = '0';
			$E['subject']       = $VAR['mail_subject'];
			$E['body_text']     = $VAR['mail_message'];
			$E['to_email']      = $account->fields['email'];
			$E['to_name']       = $account->fields['first_name'] . ' ' . $account->fields['last_name'];


			if($setup_email->fields['type'] == 0)
			{
				$type = 0;
			}
			else
			{
				$type = 1;
				$E['server']    = $setup_email->fields['server'];
				$E['account']   = $setup_email->fields['username'];
				$E['password']  = $setup_email->fields['password'];
			}

			$E['from_name']     = $setup_email->fields['from_name'];
			$E['from_email']    = $setup_email->fields['from_email'];

			if($setup_email->fields['cc_list'] != '')
				$E['cc_list']   = explode(',', $setup_email->fields['cc_list']);

			if($setup_email->fields['bcc_list'] != '')
				$E['bcc_list']  = explode(',', $setup_email->fields['bcc_list']);


			### Call the mail class
			require_once(PATH_CORE   . 'email.inc.php');
			$email = new CORE_email;
			if($type == 0)
			$email->PHP_Mail($E);
			else
			$email->SMTP_Mail($E);


		}
		else
		{
			## Error message:
			$C_debug->alert($C_translate->translate('validate_any','',''));

			## Stripslashes
			global $C_vars;
			$C_vars->strip_slashes_all();
			return;
		}

		## Success message:
		$C_debug->alert($C_translate->translate('mail_sent','account_admin',''));

		## Stripslashes
		global $C_vars;
		$C_vars->strip_slashes_all();
	}


	##############################
	##    MAIL MULTI ACCOUNTS   ##
	##############################
	function mail_multi($VAR)
	{
		if(!$this->checkLimits()) return false; // check account limits

		global $C_translate, $C_debug;

		## Validate the required vars (account_id, message, subject)
		if(@$VAR['search_id'] != "" && @$VAR['mail_subject'] != "" && @$VAR['mail_message'] != "")
		{

			## Get the specified accounts:
			# get the search details:
			if(isset($VAR['search_id'])) {
				include_once(PATH_CORE   . 'search.inc.php');
				$search = new CORE_search;
				$search->get($VAR['search_id']);
			} else {
				# invalid search!
				echo '<BR> The search terms submitted were invalid!';       # translate... # alert
				return;
			}

			# generate the full query
			$field_list =   AGILE_DB_PREFIX."account.email,         ".
							AGILE_DB_PREFIX."account.first_name,    ".
							AGILE_DB_PREFIX."account.last_name      ";

			$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
			$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX."account", $q);
			$q = preg_replace("/%%whereList%%/i", "", $q);
			$q .= " ".AGILE_DB_PREFIX."account.site_id = '" . DEFAULT_SITE . "'";
			$db = &DB();
			$account = $db->Execute($q);

			// check results
			if($account->RecordCount() == 0) {
			   $C_debug->alert($C_translate->translate('account_non_exist','account_admin',''));
			   return;
			}

			// get the selected email setup details
			$db = &DB();
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				id          = ".$db->qstr($VAR['mail_email_id']);
			$setup_email    = $db->Execute($q);
			if($setup_email->fields['type'] == 0) {
				$type = 0;
			} else {
				$type = 1;
				$E['server']    = $setup_email->fields['server'];
				$E['account']   = $setup_email->fields['username'];
				$E['password']  = $setup_email->fields['password'];
			}

			// loop to send each e-mail
			while ( !$account->EOF )
			{
				$E['priority']      = $VAR['mail_priority'];
				$E['html']          = '0';
				$E['subject']       = $VAR['mail_subject'];
				$E['body_text']     = $VAR['mail_message'];
				$E['to_email']      = $account->fields['email'];
				$E['to_name']       = $account->fields['first_name'] . ' ' . $account->fields['last_name'];
				$E['from_name']     = $setup_email->fields['from_name'];
				$E['from_email']    = $setup_email->fields['from_email'];

				### Call the mail class
				require_once(PATH_CORE   . 'email.inc.php');
				$email = new CORE_email;
				$email = new CORE_email;
				if($type == 0)
				$email->PHP_Mail($E);
				else
				$email->SMTP_Mail($E);

				### Next record
				$account->MoveNext();
			}


		}  else {
			## Error message:
			$C_debug->alert($C_translate->translate('validate_any','',''));

			## Stripslashes
			global $C_vars;
			$C_vars->strip_slashes_all();
			return;
		 }

		## Success message:
		$C_debug->alert($C_translate->translate('mail_sent','account_admin',''));

		## Stripslashes
		global $C_vars;
		$C_vars->strip_slashes_all();
	} 


	##############################
	##	SEND PASSWORD CHANGE    ##
	##############################
	function send_password_email($VAR)
	{
		global $C_translate, $C_debug;
		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;
		$my->send('password_change_instructions', @$VAR['id'], '', '', '');
		echo $C_translate->translate("password_change_instructions","account_admin","");
	}


	##############################
	##	SEND VERIFY E-MAIL      ##
	##############################
	function send_verify_email($VAR)
	{
		global $C_translate, $C_debug;

		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template; 
		$db = &DB();
		$dbm = new CORE_database;
		echo $sql = $dbm->sql_select('account','date_orig',"id = {$VAR['id']}",'', $db);
		$result = $db->Execute($sql);         	
		$validation_str = strtoupper($result->fields['date_orig']. ':' . $VAR['id']);   		    
		$my->send('account_registration_inactive', @$VAR['id'], @$VAR['id'], '', $validation_str);
		echo $C_translate->translate("account_verify_instructions","account_admin","");
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{ 
		if(!$this->checkLimits()) return false; // check account limits

		global $C_translate, $C_debug, $smarty;

		### Set the hidden values:
		$VAR['account_admin_date_orig']   = time();
		$VAR['account_admin_date_last']   = time();
		if(!empty($VAR["account_admin_date_expire"])) {
			include_once(PATH_CORE.'validate.inc.php');
			$val = new CORE_validate;
			$date_expire = $val->DateToEpoch(false, $VAR["account_admin_date_expire"]);
		} else {
			$date_expire = 0;
		}


		### Determine the proper account status:
		if(!isset($VAR['account_admin_status'])  || $VAR['account_admin_status'] != "1")
		$status = 0;
		else
		$status = 1;


		### DEFINE A USERNAME:
		if(empty($VAR['account_admin_username'])) {
			$length = 4;
			srand((double)microtime()*1000000);
			$vowels = array("a", "e", "i", "o", "u");
			$cons   = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p",
					"r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th",
					"dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
			$num_vowels = count($vowels);
			$num_cons = count($cons);
			for($i = 0; $i < $length; $i++){
				@$VAR['account_admin_username'] .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
			}
		}  

		## Single field login:
		if(defined('SINGLE_FIELD_LOGIN') && SINGLE_FIELD_LOGIN==true && empty($VAR['account_admin_password'])) { 
			$VAR['account_admin_password']='none';      
			$passwd = 'none';
		}

		### DEFINE A PASSWORD:      
		if(empty($VAR['account_admin_password']))
		{
			srand((double)microtime() * 1000000);
			$UniqID = md5(uniqid(rand())); 
			@$VAR['account_admin_password'] = substr(md5(uniqid(rand())), 0, 10);
			$passwd = '********';
		} else {
			$passwd = $VAR['account_admin_password']; 

			/* hash the password */
			if(defined('PASSWORD_ENCODING_SHA'))  
				$VAR['account_admin_password'] = sha1($VAR['account_admin_password']);
			else  
				$VAR['account_admin_password'] = md5($VAR['account_admin_password']);				
		}


		####################################################################
		### loop through the field list to validate the required fields
		####################################################################

		$type = 'add';
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$arr = $this->method["$type"];
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;		
		$this->validated = true;			

		while (list ($key, $value) = each ($arr))
		{
			# get the field value
			$field_var  	= $this->module . '_' . $value;
			$field_name 	= $value;

			####################################################################
			### perform any field validation...
			####################################################################

			# check if this value is unique
			if(isset($this->field["$value"]["unique"]) && isset($VAR["$field_var"]))
			{		
				if(!$validate->validate_unique($this->table, $field_name, "record_id", $VAR["$field_var"]))
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->table . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),							# translate
												'error' 		=> $C_translate->translate('validate_unique',"", ""));	 				
				}
			}

			# check if the submitted value meets the specifed requirements
			if(isset($this->field["$value"]["validate"]))
			{
				if(isset($VAR["$field_var"]))
				{
					if($VAR["$field_var"] != '')
					{
						if(!$validate->validate($field_name, $this->field["$value"], $VAR["$field_var"], $this->field["$value"]["validate"]))
						{
							$this->validated = false;
							$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
														'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
														'error' 		=> $validate->error["$field_name"] );								
						}
					}
					else
					{
						$this->validated = false;
						$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
													'error' 		=> $C_translate->translate('validate_any',"", "")); 	
					}
				}
				else
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
												'error' 		=> $C_translate->translate('validate_any',"", "")); 		 																		
				}
			}
		}


		// validate the tax_id 
		require_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;  
		$tax_arr = @$VAR['account_admin_tax_id'];  
		if(is_array($tax_arr)) {
			foreach($tax_arr as $country_id => $tax_id) {
				if ($country_id == $VAR['account_admin_country_id']) { 
					$exempt = @$VAR["account_tax_id_exempt"][$country_id];
					if(!$taxObj->TaxIdsValidate($country_id, $tax_id, $exempt)) {            
						$this->validated = false; 
						$this->val_error[] =  array(
							'field'         => 'account_admin_tax_id',
							'field_trans' 	=> $taxObj->errField,							
							'error' 		=> $C_translate->translate('validate_general', "", "")); 					
					} 
					if($exempt) 
					$account_admin_tax_id=false;
					else
					$account_admin_tax_id=$tax_id;							 								
				}
			}
		} 			

		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;

		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('account', $this->val_error);

		if($all_error != false && gettype($all_error) == 'array')
		$this->validated = false;
		else
		$this->validated = true;


		####################################################################
		### If validation was failed, skip the db insert &
		### set the errors & origonal fields as Smarty objects,
		### and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;	

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $all_error);

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
				define('FORCE_PAGE', $VAR['_page_current']);
			}

			# Stripslashes
			global $C_vars;
			$C_vars->strip_slashes_all();


			return;
		}

		# Get default invoice options
		$db=&DB();
		$invopt=$db->Execute(sqlSelect($db,"setup_invoice","*","")); 
		if($invopt && $invopt->RecordCount()) { 
			$invoice_delivery=$invopt->fields['invoice_delivery'];
			$invoice_format=$invopt->fields['invoice_show_itemized'];			
		}	

		####################################################################
		### Insert the account record
		#################################################################### 
		$this->account_id = $db->GenID(AGILE_DB_PREFIX . 'account_id'); 
		$validation_str = time();

		/** get parent id */
		@$parent_id = @$VAR["account_admin_parent_id"];
		if(empty($parent_id)) $parent_id = $this->account_id;

		$sql = '
			INSERT INTO ' . AGILE_DB_PREFIX . 'account SET
			id              = ' . $db->qstr ( $this->account_id ) . ',
			site_id         = ' . $db->qstr ( DEFAULT_SITE ) . ',
			date_orig       = ' . $db->qstr ( $validation_str ) . ',
			date_last       = ' . $db->qstr ( time()) . ',
			date_expire     = ' . $db->qstr ( $date_expire ) . ',
			language_id     = ' . $db->qstr ( $VAR["account_admin_language_id"] ) . ',
			country_id      = ' . $db->qstr ( $VAR["account_admin_country_id"] ) . ',
			parent_id	    = ' . $db->qstr ( $parent_id ) . ',
			affiliate_id    = ' . $db->qstr ( @$VAR["account_admin_affiliate_id"] ) . ',
			reseller_id     = ' . $db->qstr ( @$VAR["account_admin_reseller_id"] ) . ',
			currency_id     = ' . $db->qstr ( $VAR["account_admin_currency_id"] ) . ',
			theme_id        = ' . $db->qstr ( $VAR["account_admin_theme_id"] ) . ',
			username        = ' . $db->qstr ( $VAR["account_admin_username"] ) . ',
			password        = ' . $db->qstr ( $VAR["account_admin_password"] ) . ',
			status          = ' . $db->qstr ( $status ) . ',
			first_name      = ' . $db->qstr ( $VAR["account_admin_first_name"] ) . ',
			middle_name     = ' . $db->qstr ( $VAR["account_admin_middle_name"] ) . ',
			last_name       = ' . $db->qstr ( $VAR["account_admin_last_name"] ) . ',
			company         = ' . $db->qstr ( $VAR["account_admin_company"] ) . ',
			title           = ' . $db->qstr ( $VAR["account_admin_title"] ) . ',
			email           = ' . $db->qstr ( $VAR["account_admin_email"] ) . ',
			address1		= ' . $db->qstr ( $VAR["account_admin_address1"] ) . ',
			address2		= ' . $db->qstr ( $VAR["account_admin_address2"] ) . ',
			city			= ' . $db->qstr ( $VAR["account_admin_city"] ) . ',
			state			= ' . $db->qstr ( $VAR["account_admin_state"] ) . ',
			zip				= ' . $db->qstr ( $VAR["account_admin_zip"] ) . ',
			misc			= ' . $db->qstr ( $VAR["account_admin_misc"] ) . ',
			email_type      = ' . $db->qstr ( $VAR["account_admin_email_html"] ) . ',
			invoice_delivery= ' . $db->qstr ( @$invoice_delivery ) . ',
			invoice_show_itemized=' . $db->qstr ( @$invoice_format ) . ',
			invoice_advance_gen	= ' . $db->qstr ( MAX_INV_GEN_PERIOD ) . ',
			invoice_grace		= ' . $db->qstr ( GRACE_PERIOD ) . ',
			tax_id			= ' . $db->qstr ( @$account_tax_id );
		$result = $db->Execute($sql);

		### error reporting: 
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('account_admin.inc.php','add', $db->ErrorMsg());

			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
			return;
		}

		/* password logging class */ 
		global $C_list;
		if($C_list->is_installed('account_password_history')) {
			include_once(PATH_MODULES.'account_password_history/account_password_history.inc.php');
			$accountHistory = new account_password_history();
			$accountHistory->setNewPassword($this->account_id, $VAR["account_admin_password"]);
		}

		### Add the account to the default group:  
		$this->add_account_groups($VAR['groups'], $this->account_id, $VAR['account_admin_date_expire']);

		### Insert the static vars:  
		$static_var->add($VAR, 'account', $this->account_id);


		### Mail the new user  
		if(!empty($VAR['welcome_email'])) {
			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
			$my = new email_template;
			if($status == "1") {
				$my->send('account_add_staff_active', $this->account_id, '', '', $passwd);
			} else {
				$validation_str = strtoupper($validation_str. ':' .$this->account_id);
				$my->send('account_add_staff_inactive', $this->account_id, $this->account_id, '', $validation_str);
			}
		}

		### Do any db_mapping  
		if($C_list->is_installed('db_mapping'))
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping; 
			if(!empty($passwd))
				$db_map->plaintext_password  = $passwd;
			else
				$db_map->plaintext_password  = false; 
			$db_map->account_add ( $this->account_id );
		}

		### Display the welcome message 
		if($status == "1")
		{
			$C_debug->alert($C_translate->translate("staff_add_active","account_admin",""));

		} else {
			$C_debug->alert($C_translate->translate("staff_add_inactive","account_admin",""));    		
		}	

		#$VAR["id"] = $this->account_id;
		$url = '?_page=' . $VAR['_page'] . '&id=' . $this->account_id;
		if(!empty($VAR['id']))      $url.= '&_escape=1';
		if(!empty($VAR['field']))
		{
			$url  .= '&field='.$VAR['field'];
			$url  .= '&name='.$VAR['account_admin_first_name'].' '.$VAR['account_admin_last_name'];
		}

		define('REDIRECT_PAGE', $url);

		### Affiliate Auto Creation 
		if(AUTO_AFFILIATE == 1 && $C_list->is_installed("affiliate"))
		{
			$VAR['affiliate_account_id'] = $this->account_id;
			$VAR['affiliate_template_id'] = DEFAULT_AFFILIATE_TEMPLATE;
			@$VAR['affiliate_parent_affiliate_id'] = $VAR['account_admin_affiliate_id'];            	

			include_once(PATH_MODULES . 'affiliate/affiliate.inc.php');
			$affiliate = new affiliate;
			$affiliate->add($VAR, $affiliate);
		}

		return;
	}



	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{
		if(!$this->checkLimits()) return false; // check account limits

		global $C_auth;

		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		# set the field list for this method:
		$db = &DB();
		$arr = $this->method[$type];				
		if(isset($VAR["id"]))
		{
			$id = explode(',',$VAR["id"]);
			for($i=0; $i<count($id); $i++)
			{
				if($id[$i] != '')
				{
					if($i == 0)
					{				 			
						$id_list .= " id = " .$db->qstr($id[$i])." ";
						$ii++;
					}
					else
					{
						$id_list .= " OR id = " .$db->qstr($id[$i]). " ";
						$ii++;
					}
				}					
			}
		}

		if($ii>0)
		{ 	
			# generate the full query
			$q = "SELECT * FROM
				  ".AGILE_DB_PREFIX."account
				  WHERE					
				  $id_list
				  AND site_id = '" . DEFAULT_SITE . "'";

			$result = $db->Execute($q);

			# error reporting
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('account_admin.inc.php','view', $db->ErrorMsg() . ' ' . $q);		        	
				return;   		        	
			}

			# put the results into a smarty accessable array
			$i=0;
			$class_name = TRUE;
			while (!$result->EOF)
			{
				$smart[$i] = $result->fields;

				if($class_name)
				{
					$smart[$i]["i"] = $i;
				} else {
					$smart[$i]["i"] = $i;
				}

				### Get any authorized groups:
				$dba    = &DB();
				$sql    = 'SELECT service_id,group_id FROM ' . AGILE_DB_PREFIX . 'account_group WHERE 
							site_id      = ' . $dba->qstr(DEFAULT_SITE) . ' AND
							account_id   = ' . $dba->qstr($result->fields['id']) . ' AND 
							active       = ' . $dba->qstr("1") . '
							ORDER BY group_id';

				$groups = $dba->Execute($sql);

				while (!$groups->EOF)
				{
					if($groups->fields['service_id'] == '') $group[] = $groups->fields['group_id'];
					$groups->MoveNext();
				} 
				$smart[$i]["groups"] = $group;

				### Verify the user has access to view this account:                            				   	    			   	
				if(SESS_ACCOUNT != $result->fields['id'])
				{
					$smart[$i]['own_account'] = false;
					$display_this = true;
					for($ix=0; $ix<count($group); $ix++)
					{
						if(!$C_auth->auth_group_by_id($group[$ix]))
						$display_this = false;
					}
				}
				else
				{
					$display_this = true;
					$smart[$i]['own_account'] = true;
				}




				### Get the static vars:
				require_once(PATH_CORE   . 'static_var.inc.php');
				$static_var = new CORE_static_var;
				$arr = $static_var->update_form('account', 'update', $result->fields['id']);
				if(gettype($arr) == 'array')
				{ 		
					$smart[$i]["static_var"] =	 	$arr;
				}


				### Get the last activity date/IP
				$sql = "SELECT * FROM ".AGILE_DB_PREFIX."login_log
						WHERE account_id = {$result->fields['id']}
						AND site_id = ".DEFAULT_SITE."
						ORDER BY date_orig DESC  ";
				$rslast = $db->SelectLimit($sql, 1);
				if($rslast != false && $rslast->RecordCount() == 1)  {
					$smart[$i]["last_activity"] = $rslast->fields['date_orig'];	
					$smart[$i]["last_ip"] 		= $rslast->fields['ip'];	
				} else {
					$smart[$i]["last_activity"] = $result->fields['date_orig'];	
					$smart[$i]["last_ip"] 		= '';                   		
				}


				### Get invoice details for this account:
				$sql = "SELECT id,date_orig,total_amt,billed_amt,process_status FROM ".AGILE_DB_PREFIX."invoice
						WHERE account_id = {$result->fields['id']}
						AND site_id = ".DEFAULT_SITE."
						ORDER BY id DESC ";
				$inv = $db->SelectLimit($sql, 10);
				if($inv != false && $inv->RecordCount() > 0)  {
					while(!$inv->EOF) {
						if($inv->fields['total_amt'] > $inv->fields['billed_amt'] && $inv->fields['suspend_billing'] != 1) {
							$inv->fields['due'] = $inv->fields['total_amt'] - $inv->fields['billed_amt'];
						}
						$smart[$i]["invoice"][] = $inv->fields;	
						$inv->MoveNext();	
					}
				}   


				### Get service details for this account:
				$sql = "SELECT id,sku,active,type,domain_name,domain_tld FROM ".AGILE_DB_PREFIX."service
						WHERE account_id = {$result->fields['id']}
						AND site_id = ".DEFAULT_SITE."
						ORDER BY id DESC ";
				$svc = $db->SelectLimit($sql, 10);
				if($svc != false && $svc->RecordCount() > 0)  {
					while(!$svc->EOF) {
						$smart[$i]["service"][] = $svc->fields;	
						$svc->MoveNext();	
					}
				}  


				# define the results
				if(!$display_this)
				{
					unset($smart["$i"]);
					echo "You have selected an account for which you are not authorized,
						  your permission settings are to low!<br><br>";
				}
				else                         		
				{
					$i++;        		   							   	
				}                    
				unset($group);
				$result->MoveNext();
			}

			# get the result count:
			$results = $i;

			### No results:
			if($i == 0)
			{
				global $C_debug;
				$C_debug->error("CORE:account_admin.inc.php", "view()", "
				The selected record does not exist any longer, or your account is not authorized to view it");
				return;
			}


			global $smarty;
			$smarty->assign($this->table, $smart);
			$smarty->assign('results', 	$search->results);
		}    		
	}		




	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{        	
		global $C_list, $C_debug;

		if(!$this->checkLimits()) return false; // check account limits

		// validate the tax_id
		global $VAR;
		require_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;  
		$tax_arr = @$VAR['account_admin_tax_id'];  
		if(is_array($tax_arr)) {
			foreach($tax_arr as $country_id => $tax_id) {
				if ($country_id == $VAR['account_admin_country_id']) { 
					$exempt = @$VAR["account_tax_id_exempt"][$country_id];
					if(!$txRs=$taxObj->TaxIdsValidate($country_id, $tax_id, $exempt)) {            
						$this->validated = false; 
						global $C_translate;
						$this->val_error[] =  array(
							'field'         => 'account_admin_tax_id',
							'field_trans' 	=> $taxObj->errField,							
							'error' 		=> $C_translate->translate('validate_general', "", "")); 					
					}  
					if($exempt) 
					$VAR['account_admin_tax_id']=false;
					else
					$VAR['account_admin_tax_id']=$tax_id;						
				}
			}
		}

		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('account', $this->val_error);

		if($all_error != false && gettype($all_error) == 'array')
		$this->validated = false;
		else
		$this->validated = true;

		####################################################################
		# If validation was failed, skip the db insert &
		# set the errors & origonal fields as Smarty objects,
		# and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $all_error);	

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
				define('FORCE_PAGE', $VAR['_page_current']);
			}		
			return;
		} 

		### Get the old username ( for db mapping )
		$db     = &DB();
		$sql    = 'SELECT username FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($VAR['account_admin_id']);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$old_username = $result->fields['username'];
		}

		### Update the password:
		$update_password=false;
		if(!empty($VAR['_password'])) {	
			$VAR['account_admin_password'] = $VAR['_password'];

			/* check if new password is ok */
			if($C_list->is_installed('account_password_history')) {
				include_once(PATH_MODULES.'account_password_history/account_password_history.inc.php');
				$accountHistory = new account_password_history();
				if(!$accountHistory->getIsPasswordOk($VAR['account_admin_id'], $VAR['account_admin_password'], false)) {
					$C_debug->alert("The password you have selected has been used recently and cannot be used again at this time for security purposes.");
					unset($VAR['account_admin_password']); 
				} else {
					$update_password=true;
				}
			}
		}

		### Update the record
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$ok = $db->update($VAR, $this, $type);        		

		if($ok) 
		{ 
			/* password logging class */  
			if($update_password && is_object($accountHistory)) $accountHistory->setNewPassword($VAR['account_admin_id'], $VAR["account_admin_password"], false);

			### Update the static vars:
			$static_var->update($VAR, 'account', $VAR['account_admin_id']);

			### Do any db_mapping 
			if($C_list->is_installed('db_mapping'))
			{
				include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
				$db_map = new db_mapping;

				if(!empty($VAR['account_admin_password']))
					$db_map->plaintext_password  = $VAR['account_admin_password'];
				else
					$db_map->plaintext_password  = false;

				$db_map->account_edit ( $VAR['account_admin_id'], $old_username );
			} 

			// remove login lock
			if($VAR['account_admin_status']) {
				$db=&DB();
				$delrs = $db->Execute($sql=sqlDelete($db,"login_lock","account_id={$VAR['account_admin_id']}")); 
				$delrs = $db->Execute($sql=sqlDelete($db,"login_log","account_id={$VAR['account_admin_id']} AND status=0")); 
			} 
			return true;
		}
	}



	##############################
	##		 MERGE	            ##
	##############################
	function merge($VAR)
	{	
		$db = &DB();
		global $C_auth, $C_list, $C_translate, $C_debug;

		if(empty($VAR['id']) || empty($VAR['merge_acct_id'])) {
			$C_debug->alert($C_translate->translate('merge_err','account_admin',''));
			return false;
		}

		$acct_id 		= $VAR['id'];
		$merge_acct_id 	= $VAR['merge_acct_id'];

		# Get merged account_group
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."account_group WHERE  (
				service_id = '' OR
				service_id = 0 OR
				service_id IS NULL
				) AND account_id = $acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false)  { 
			$C_debug->error('account_admin.inc.php','merge :: account_group', $db->ErrorMsg()); 
		} else {
			while(!$rs->EOF) {
				$Cauth = new CORE_auth(true);          		        
				if($Cauth->auth_group_by_account_id($merge_acct_id, $rs->fields['group_id'])) {    	
					# duplicate group, delete
					$q = "DELETE FROM ".AGILE_DB_PREFIX."account_group WHERE id = {$rs->fields['id']} AND site_id = ".DEFAULT_SITE;
					$db->Execute($q);          	              		
				}
				$rs->MoveNext();  
			}
		}

		# account_group
		$q = "UPDATE ".AGILE_DB_PREFIX."account_group SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: account_group', $db->ErrorMsg()); 

		# account_billing
		$q = "UPDATE ".AGILE_DB_PREFIX."account_billing SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: account_billing', $db->ErrorMsg()); 

		# cart
		$q = "UPDATE ".AGILE_DB_PREFIX."cart SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: cart', $db->ErrorMsg()); 

		# charge
		$q = "UPDATE ".AGILE_DB_PREFIX."charge SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: charge', $db->ErrorMsg()); 

		# discount
		$q = "UPDATE ".AGILE_DB_PREFIX."discount SET avail_account_id = $acct_id WHERE avail_account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: charge', $db->ErrorMsg()); 

		# invoice
		$q = "UPDATE ".AGILE_DB_PREFIX."invoice SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: invoice', $db->ErrorMsg()); 

		# log_error
		$q = "UPDATE ".AGILE_DB_PREFIX."log_error SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: log_error', $db->ErrorMsg()); 

		# login_lock
		$q = "DELETE FROM ".AGILE_DB_PREFIX."login_lock WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: login_lock', $db->ErrorMsg()); 

		# login_log
		$q = "UPDATE ".AGILE_DB_PREFIX."login_log SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: login_log', $db->ErrorMsg()); 

		# search
		$q = "UPDATE ".AGILE_DB_PREFIX."search SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: search', $db->ErrorMsg()); 

		# service
		$q = "UPDATE ".AGILE_DB_PREFIX."service SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: service', $db->ErrorMsg()); 

		# session
		$q = "DELETE FROM ".AGILE_DB_PREFIX."session WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: session', $db->ErrorMsg()); 

		# staff
		$q = "UPDATE ".AGILE_DB_PREFIX."staff SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: staff', $db->ErrorMsg()); 

		# affiliate
		if($C_list->is_installed('affiliate'))
		{            	
			$q = "UPDATE ".AGILE_DB_PREFIX."affiliate SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
			$rs = $db->Execute($q);          	  
			if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: affiliate', $db->ErrorMsg()); 
		}

		# ticket 
		if($C_list->is_installed('ticket'))
		{          	
			$q = "UPDATE ".AGILE_DB_PREFIX."ticket SET account_id = $acct_id WHERE account_id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
			$rs = $db->Execute($q);          	  
			if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: ticket', $db->ErrorMsg()); 
		}

		# DB Mapping 
		if($C_list->is_installed('db_mapping'))
		{          	
			$dbsql = "SELECT username FROM ".AGILE_DB_PREFIX."account WHERE
								  site_id = ".$db->qstr(DEFAULT_SITE)." AND
								  id      = ".$db->qstr($merge_acct_id);
			$resultdb  = $db->Execute($dbsql);
			$old_username = $resultdb->fields['username']; 
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->account_delete ( $merge_acct_id, $old_username );
		}

		# Delete account  
		$q = "DELETE FROM ".AGILE_DB_PREFIX."account WHERE id = $merge_acct_id AND site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($q);          	  
		if ($rs === false) $C_debug->error('account_admin.inc.php','merge :: account', $db->ErrorMsg()); 

		$C_debug->alert($C_translate->translate('merge_ok','account_admin','')); 
		return;

	}        


	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$db = &DB();
		global $C_auth, $C_list;

		# set the id
		$id = $this->table . '_id';

		# generate the list of ID's
		$id_list = '';
		$account_id_list = '';
		$discount_id_list = '';
		$ii=0;

		if(isset($VAR["delete_id"]))
		{
			$id = explode(',',$VAR["delete_id"]);
		}
		elseif (isset($VAR["id"]))
		{
			$id = explode(',',$VAR["id"]);
		}

		for($i=0; $i<count($id); $i++)
		{
			if($id[$i] != '')
			{
				### is current account auth to delete this account?
				### Get any authorized groups: 
				$db 	= &DB();
				$sql    = 'SELECT group_id FROM ' . AGILE_DB_PREFIX . 'account_group WHERE
							site_id      = ' . $db->qstr(DEFAULT_SITE) . ' AND
							account_id   = ' . $db->qstr($id[$i]) . '
							ORDER BY group_id';                                    			
				$groups = $db->Execute($sql);            		
				while (!$groups->EOF)
				{
					$group[] = $groups->fields['group_id'];
					$groups->MoveNext();
				}

				### Verify the user has access to view this account:
				$delete_this = true;
				if(!empty($group) && is_array($group)) {
					for($ix=0; $ix<count($group); $ix++)
					{
						if(!$C_auth->auth_group_by_id($group[$ix]))
						{
							$delete_this = false;
							$ix = count($group);
						}
					}
				}
				unset($group);

				### Verify this is not the admin account or the current user's account:
				if(SESS_ACCOUNT == $id[$i] || $id[$i] == '1')
					$delete_this = false;

				### Generate the SQL
				if($delete_this)
				{    			
					if($i == 0)   {
						$id_list .= " id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$account_id_list .= " account_id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$discount_id_list .= " account_id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$ii++;
					} else {
						$id_list .= " OR id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$account_id_list .= " OR account_id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$discount_id_list .= " OR account_id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
						$ii++;
					}

					####################################################################
					### Do any db_mapping
					####################################################################

					$dbsql = "SELECT username FROM ".AGILE_DB_PREFIX."account WHERE
							  site_id = ".$db->qstr(DEFAULT_SITE)." AND
							  id      = ".$db->qstr($id[$i]);
					$resultdb  = $db->Execute($dbsql);
					$old_username = $resultdb->fields['username'];

					if($C_list->is_installed('db_mapping'))
					{
						include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
						$db_map = new db_mapping;
						$db_map->account_delete ( $id[$i], $old_username );
					}
				}
			}					
		}

		$db = &DB();
		if($ii>0)
		{
			# generate the full query (account)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."account
				  WHERE $id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($q);

			# generate the full query (sessions)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."session
				  WHERE $account_id_list AND site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (account_billing)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."account_billing
				  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (account_group)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."account_group
				  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (cart)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."cart
				  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (search)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."search
				  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (staff)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."staff
				  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (ticket)
			if($C_list->is_installed('ticket'))
			{
				$q = "SELECT id FROM  ".AGILE_DB_PREFIX."ticket
					  WHERE $account_id_list AND site_id = ".$db->qstr(DEFAULT_SITE);
				$ticket = $db->Execute($q);
				if($ticket != false && $ticket->RecordCount() > 0) {
					while( !$ticket->EOF ) {
						include_once(PATH_MODULES.'ticket/ticket.inc.php');
						$tk = new ticket;
						$arr['id'] = $ticket->fields['id'];
						$tk->delete($arr, $tk);
						$ticket->MoveNext();
					}
				}
			}

			# generate the full query (affiliate)
			if($C_list->is_installed('affiliate'))
			{
				$q = "DELETE FROM ".AGILE_DB_PREFIX."affiliate
					  WHERE $account_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
				$db->Execute($q);
			}

			# generate the full query (discount)
			$q = "DELETE FROM  ".AGILE_DB_PREFIX."discount
				  WHERE $discount_id_list  AND  site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			# generate the full query (invoice)
			$q = "SELECT id FROM  ".AGILE_DB_PREFIX."invoice
				  WHERE $account_id_list AND site_id = ".$db->qstr(DEFAULT_SITE);
			$invoice = $db->Execute($q);
			if($invoice != false && $invoice->RecordCount() > 0 ) {
				while( !$invoice->EOF ) {
					include_once(PATH_MODULES.'invoice/invoice.inc.php');
					$inv = new invoice;
					$arr['id'] = $invoice->fields['id'];
					$inv->delete($arr, $inv);
					$invoice->MoveNext();
				}
			}

			# error reporting
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('account_admin.inc.php','delete', $db->ErrorMsg());

			}
			else
			{
				# Alert delete message
				global $C_debug, $C_translate;
				$C_translate->value["CORE"]["module_name"] = $C_translate->translate('name','account_admin',"");
				$message = $C_translate->translate('alert_delete_ids',"CORE","");
				$C_debug->alert($message);	             		
			}
		}    		
	}		



	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_form($VAR, $this, $type);
	}





	##############################
	##		    SEARCH		    ##
	##############################
	function search($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		$db = &DB();	

		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;

		# set the search criteria array
		$arr = $VAR;

		# loop through the submitted field_names to get the WHERE statement
		$where_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(preg_match('/'.$pat.'/i', $key))
					{	 				
						$field = preg_replace('/'.$pat.'/i',"",$key);
						if(preg_match('/%/',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " WHERE ".AGILE_DB_PREFIX."account.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										$VAR['field_option']["$pat_field"]["$i_arr"];
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];
										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}


										if($i_arr == 0)
										{
											$where_list .= " WHERE ".AGILE_DB_PREFIX."account.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
										   $where_list .= " AND ".AGILE_DB_PREFIX."account.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										   $i++;
										}
								   }
								}
							}
							else
							{	
							   $where_list .= " WHERE ".AGILE_DB_PREFIX."account.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
			else
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(preg_match('/'.$pat.'/i', $key))
					{
						$field = preg_replace('/'.$pat.'/i',"",$key);
						if(preg_match('/%/',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " AND ".AGILE_DB_PREFIX."account.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];

										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}

										$where_list .= " AND ".AGILE_DB_PREFIX."account.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
								   }
								}
							}
							else
							{		
							   $where_list .=  " AND ".AGILE_DB_PREFIX."account.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
		}

		#### finalize the WHERE statement
		if($where_list == '')
		{
			$where_list .= ' WHERE ';
		}
		else
		{
			$where_list .= ' AND ';
		}


		# get limit type
		if(isset($VAR['limit']))
		{
			$limit = $VAR['limit'];
		}
		else
		{
			$limit = $this->limit;
		}

		# get order by
		if(isset($VAR['order_by']))
		{
			$order_by = $VAR['order_by'];
		}
		else
		{
			$order_by = $this->order_by;
		}

		$pre = AGILE_DB_PREFIX;	

		$q = "SELECT DISTINCT ".AGILE_DB_PREFIX."account.id,".AGILE_DB_PREFIX."account.last_name,".AGILE_DB_PREFIX."account.first_name,".AGILE_DB_PREFIX."account.username FROM ".AGILE_DB_PREFIX."account ";
		$q_save = "SELECT DISTINCT %%fieldList%% FROM ".AGILE_DB_PREFIX."account ";

		# Code for group searches:
		if(!empty($VAR['account_group']))
		$q .= " LEFT JOIN ".AGILE_DB_PREFIX."account_group ON ".AGILE_DB_PREFIX."account_group.account_id = ".AGILE_DB_PREFIX."account.id";


		######## GET ANY STATIC VARS TO SEARCH ##########
		$join_list = ''; 
		if(!empty($VAR["static_relation"]) && count( $VAR["static_relation"] > 0 )) {  
			while(list($idx, $value) = each ($VAR["static_relation"])) {
				if($value != "") {

					$join_list .= " INNER JOIN {$pre}static_var_record AS s{$idx} ON 
						( 
							s{$idx}.record_id = {$pre}{$this->table}.id
							AND
							s{$idx}.static_var_relation_id = '{$idx}'
							AND
							s{$idx}.site_id = ".$db->qstr(DEFAULT_SITE)."		        				
							AND";
					if(preg_match("/%/", $value))
						$join_list .= " s{$idx}.value LIKE ".$db->qstr($VAR["static_relation"]["$idx"]);
					else
						$join_list .= " s{$idx}.value = ".$db->qstr($VAR["static_relation"]["$idx"]);
					$join_list .= " ) "; 
				}
			}  
		}  
		######## END STATIC VAR SEARCH ##################


		# standard where list
		$q .= $join_list . $where_list ." ".AGILE_DB_PREFIX."account.site_id = " . $db->qstr(DEFAULT_SITE);

		# Code for member group:
		if(!empty($VAR['account_group'])) {
			$q .= " AND ".AGILE_DB_PREFIX."account_group.group_id = " . $db->qstr($VAR['account_group'])."
					AND ".AGILE_DB_PREFIX."account_group.site_id  = " . $db->qstr(DEFAULT_SITE);
		} 
		if(!empty($VAR['account_group']))
		{
			$q_save .= " LEFT JOIN ".AGILE_DB_PREFIX."account_group ON ".AGILE_DB_PREFIX."account_group.account_id = ".AGILE_DB_PREFIX."account.id  ";

			if(!empty($join_list))
				$q_save .= $join_list;

			$q_save .= $where_list ." %%whereList%% "; 
			$q_save .= AGILE_DB_PREFIX."account_group.group_id = " . $db->qstr($VAR['account_group'])." AND ";
		}
		else
		{
			if(!empty($join_list))
				$q_save .= $join_list;

			$q_save .= $where_list ." %%whereList%% ";
		}

		################## DEBUG ##################
		#echo "<pre>" . $q;
		#echo "<BR><BR>" . $q_save;
		#exit;

		# run the database query
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','search', $db->ErrorMsg());	  
			return false;      	                    	       
		}

		# get the result count:
		$results = $result->RecordCount();

		# get the first record id:
		if($results == 1)  $record_id = $result->fields['id'];

		# define the DB vars as a Smarty accessible block
		global $smarty; 

		# Create the definition for fast-forwarding to a single record:
		if ($results == 1 && !isset($this->fast_forward))
		{
			$smarty->assign('record_id', $record_id);
		}

		# create the search record:
		if($results > 0)
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module'] 	= $this->module;
			$arr['sql']		= $q_save;
			$arr['limit']  	= $limit;
			$arr['order_by']= $order_by;
			$arr['results']	= $results;
			$search->add($arr);

			# define the search id and other parameters for Smarty
			$smarty->assign('search_id', $search->id);

			# page:
			$smarty->assign('page', '1');

			# limit:
			$smarty->assign('limit', $limit);

			# order_by:
			$smarty->assign('order_by', $order_by);
		}

		# define the result count
		$smarty->assign('results', $results);         	    
	}






	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		# set the field list for this method:
		$arr = $this->method[$type];

		$field_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= AGILE_DB_PREFIX . "account" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "account" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			$i++;
		}  


		# get the search details:
		if(isset($VAR['search_id'])) {
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		} else {
			# invalid search!
			echo '<BR> The search terms submitted were invalid!';       # translate... # alert

			if(isset($this->trigger["$type"])) {
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "") {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'account.'.$VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'account.'.$this->order_by;
			$smarty_order =  $search->order_by;
		}


		# determine the sort order
		if(isset($VAR['desc'])) {
			$order_by .= ' DESC';
			$smarty_sort = 'desc=';
		} else if(isset($VAR['asc']))  {
			$order_by .= ' ASC';
			$smarty_sort = 'asc=';
		} else {
			if (!preg_match('/date/i',$smarty_order)) {
				$order_by .= ' ASC';
				$smarty_sort = 'asc=';
			} else {
				$order_by .= ' DESC';
				$smarty_sort = 'desc=';
			}
		}

		# generate the full query

		$db = &DB();
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . "account."."site_id = " . $db->qstr(DEFAULT_SITE);
		$q .= $order_by;

		//////////////////
		#echo "<BR><pre> $q </pre><BR>";
		$current_page=1;
		$offset=-1;
		if (!empty($VAR['page'])) $current_page = $VAR['page'];
		if (empty($search->limit)) $search->limit=25; 
		if($current_page>1) $offset = (($current_page * $search->limit) - $search->limit);            
		$result = $db->SelectLimit($q, $search->limit, $offset);


		# error reporting
		if ($result === false)
		{		
			global $C_debug;
			$C_debug->error('database.inc.php','search', $db->ErrorMsg());

			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			} 
			return;                    	        	
		}


		# put the results into a smarty accessable array  
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF) {
			$smart[$i] = $result->fields;

			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}
			$result->MoveNext();
			$i++;
		}


		# get any linked fields
		if($i > 0)
		{
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($smart, $this->linked);
		}
		else
		{
			$this->result = $smart;
		} 

		# get the result count:
		$results = $result->RecordCount();

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# define the results
		$smarty->assign($this->table, $this->result);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	$smarty_order);
		$smarty->assign('sort',		$smarty_sort);
		$smarty->assign('limit',	$search->limit);
		$smarty->assign('search_id',$search->id);
		$smarty->assign('results', 	$search->results);

		# get the total pages for this search:
		if(empty($search->limit))
			$this->pages = 1;
		else
			$this->pages = intval($search->results / $search->limit);
		if ($search->results % $search->limit) $this->pages++;

		# total pages
		$smarty->assign('pages', 	$this->pages);

		# current page
		$smarty->assign('page', 	$current_page);
		$page_arr = '';
		for($i=0; $i <= $this->pages; $i++)
		{
			if ($this->page != $i) 	$page_arr[] = $i;
		}

		# page array for menu
		$smarty->assign('page_arr',	$page_arr); 
	}	



	##############################
	##	   SEARCH EXPORT        ##
	##############################
	function search_export($VAR)
	{
		if(!$this->checkLimits()) return false; // check account limits

	   # require the export class    	
	   require_once (PATH_CORE   . "export.inc.php");

	   # Call the correct export function for inline browser display, download, email, or web save.
	   if($VAR["format"] == "excel")
	   {
		   $type = "export_excel";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
		   $export->search_excel($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "pdf")
	   {
		   $type = "export_pdf";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
		   $export->search_pdf($VAR, $this, $type);      	
	   }

	   else if ($VAR["format"] == "xml")
	   {
		   $type = "export_xml";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
		   $export->search_xml($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "csv")
	   {
		   $type = "export_csv";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
		   $export->search_csv($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "tab")
	   {
		   $type = "export_tab";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
		   $export->search_tab($VAR, $this, $type);
	   }
	}




	##############################
	##	   ADD GROUPS           ##
	##############################

	function add_account_groups($groups, $account, $expire)
	{
		global $C_auth;
		$ii = 0;

		#loop through the array to add each account_group record
		for($i=0; $i<count($groups); $i++)
		{
			# verify the admin adding this account is authorized
			# for this group themselves, otherwise skip


			if($C_auth->auth_group_by_id($groups[$i]))
			{

				# add the account to the selected groups...
				$dba = &DB();

				# determine the record id:
				$this->new_id = $dba->GenID(AGILE_DB_PREFIX . "" . 'account_group_id');

				# generate the full query
				$q = "INSERT INTO ".AGILE_DB_PREFIX."account_group
					  SET
					  id          = ". $dba->qstr($this->new_id).",
					  date_orig   = ". $dba->qstr(time()).",
					  date_expire = ". $dba->qstr($expire).",
					  group_id    = ". $dba->qstr($groups[$i]).",
					  account_id  = ". $dba->qstr($account).",
					  active      = ". $dba->qstr('1').",
					  site_id     = ". $dba->qstr(DEFAULT_SITE);

				# execute the query
				$result = $dba->Execute($q);
				$ii++;

				# error reporting:
				if ($result === false) { 
					global $C_debug;
					$C_debug->error('account_admin.inc.php','add_account_groups', $dba->ErrorMsg());		
				}
			}
		}

		### Add default group
		if($ii == 0)
		{
			# add the account to the selected groups...
			$dba = &DB();

			# determine the record id:
			$this->new_id = $dba->GenID(AGILE_DB_PREFIX . "" . 'account_group_id');

			# generate the full query
			$q = "INSERT INTO ".AGILE_DB_PREFIX."account_group
					SET
					id          = ". $dba->qstr($this->new_id).",
					date_orig   = ". $dba->qstr(time()).",
					date_expire = ". $dba->qstr($expire).",
					group_id    = ". $dba->qstr(DEFAULT_GROUP).",
					account_id  = ". $dba->qstr($account).",
					active      = ". $dba->qstr('1').",
					site_id     = ". $dba->qstr(DEFAULT_SITE);

			# execute the query
			$result = $dba->Execute($q);	
			if ($result === false) { 
				global $C_debug;
				$C_debug->error('account_admin.inc.php','add_account_groups', $dba->ErrorMsg());
			}					
		}
	}




	##############################
	##	UDPATE GROUPS           ##
	##############################

	function update_account_groups($VAR)
	{		
		global $C_auth;
		$ii = 0;
		@$groups = $VAR['groups'];
		@$account = $VAR['account_admin_id'];

		# admin accounts groups cannot be altered
		# user cannot modify their own groups
		if($account == "1" || SESS_ACCOUNT == $account)
		return false;

		### Drop the current groups for this account:
		# generate the full query
		$dba = &DB();
		$q = "DELETE FROM ".AGILE_DB_PREFIX."account_group
			  WHERE
			  service_id IS NULL AND
			  account_id  = ". $dba->qstr($account)." AND 
			  site_id     = ". $dba->qstr(DEFAULT_SITE);			
		# execute the query
		$result = $dba->Execute($q);

		#loop through the array to add each account_group record
		for($i=0; $i<count($groups); $i++)
		{
			# verify the admin adding this account is authorized
			# for this group themselves, otherwise skip


			if($C_auth->auth_group_by_id($groups[$i]))
			{
				# add the account to the selected groups...
				$dba = &DB();

				# determine the record id:
				$this->new_id = $dba->GenID(AGILE_DB_PREFIX . "" . 'account_group_id');

				# determine the expiration
				if(!empty($VAR['account_admin_date_expire']))
				{
					include_once(PATH_CORE.'validate.inc.php');
					$validate 	= new CORE_validate;
					$expire 	= $validate->DateToEpoch(DEFAULT_DATE_FORMAT,$VAR['account_admin_date_expire']); 
				} else {
					$expire 	= 0;
				}

				# generate the full query
				$q = "INSERT INTO ".AGILE_DB_PREFIX."account_group
					  SET
					  id          = ". $dba->qstr($this->new_id).",
					  date_orig   = ". $dba->qstr(time()).",
					  date_expire = ". $dba->qstr($expire).",
					  group_id    = ". $dba->qstr($groups[$i]).",
					  account_id  = ". $dba->qstr($account).",
					  active      = ". $dba->qstr('1').",
					  site_id     = ". $dba->qstr(DEFAULT_SITE);

				# execute the query
				$result = $dba->Execute($q);
				$ii++;

				# error reporting:
				if ($result === false) { 
					global $C_debug;
					$C_debug->error('account_admin.inc.php','update_account_groups', $dba->ErrorMsg());
				}
			}
		}

		### Add default group
		if($ii == 0)
		{
			# add the account to the selected groups...
			$dba = &DB();

			# determine the record id:
			$this->new_id = $dba->GenID(AGILE_DB_PREFIX . "" . 'account_group_id');

			# generate the full query
			$q = "INSERT INTO ".AGILE_DB_PREFIX."account_group
					SET
					id          = ". $dba->qstr($this->new_id).",
					date_orig   = ". $dba->qstr(time()).",
					date_expire = ". $dba->qstr(@$expire).",
					group_id    = ". $dba->qstr(DEFAULT_GROUP).",
					account_id  = ". $dba->qstr($account).",
					active      = ". $dba->qstr('1').",
					site_id     = ". $dba->qstr(DEFAULT_SITE); 
			$result = $dba->Execute($q);	 
			if ($result === false) { 
				global $C_debug;
				$C_debug->error('account_admin.inc.php','update_account_groups', $dba->ErrorMsg());
			}					
		}

		### Remove the user's session_auth_cache so it is regenerated on user's next pageview 
		$db = &DB();
		$q = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
			  account_id  = ".$db->qstr($account)." AND
			  site_id     = ".$db->qstr(DEFAULT_SITE);
		$rss = $db->Execute($q);
		while(!$rss->EOF)
		{
			$q = "DELETE FROM ".AGILE_DB_PREFIX."session_auth_cache WHERE
				  session_id = ".$db->qstr($rss->fields['id'])." AND 
				  site_id 	 = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);	
			$rss->MoveNext();
		}	

		### Do any db_mapping
		global $C_list;
		if($C_list->is_installed('db_mapping'))
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->account_group_sync ( $account );
		}  								 
	}        	         	
}
?>
