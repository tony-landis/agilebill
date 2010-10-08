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

class affiliate
{
	# Open the constructor for this mod
	function affiliate()
	{    	
		# name of affiliate id prefix
		$this->id_prefix = 'AB-';

		# name of this module:
		$this->module = "affiliate";

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


	###########################################
	### AJAX Auto-selector
	###########################################

	function autoselect($VAR)
	{

		$db = &DB();
		$p = AGILE_DB_PREFIX;

		if (empty($VAR['affiliate_search'])) {
			$where = "{$p}affiliate.id > 0";
			$type = 1;
		} elseif (is_numeric($VAR['affiliate_search'])) {
			$where = "{$p}affiliate.id LIKE ".$db->qstr($VAR['search']."%") ;   
			$type = 1;            
		} elseif (eregi(" ", $VAR['affiliate_search'])) {
			$arr = explode(" ", $VAR['affiliate_search']);
			$where = "{$p}account.first_name =    ".$db->qstr($arr[0])." AND ".
					 "{$p}account.last_name LIKE  ".$db->qstr($arr[1].'%') ;
			$type = 2;        
		} elseif (eregi("@", $VAR['affiliate_search'])) { 
			$where = "{$p}account.email LIKE ".$db->qstr('%'.$VAR['affiliate_search'].'%') ;
			$type = 3;                         
		} else {
			$where = "{$p}account.username LIKE   ".$db->qstr($VAR['affiliate_search'].'%')." OR ".
					 "{$p}account.first_name LIKE ".$db->qstr($VAR['affiliate_search'].'%')." OR ".
					 "{$p}account.last_name LIKE ".$db->qstr($VAR['affiliate_search'].'%');
			$type = 4;
		}

		$q = "SELECT DISTINCT
				{$p}affiliate.id,
				{$p}account.first_name,
				{$p}account.last_name,
				{$p}account.username,
				{$p}account.email
			  FROM 
				 {$p}account
			  LEFT JOIN
				{$p}affiliate              	
			  ON
				{$p}account.id = {$p}affiliate.account_id
			  WHERE 
				( $where )
			  AND
				{$p}affiliate.id IS NOT NULL AND 
				{$p}affiliate.site_id = " . DEFAULT_SITE . " AND
				{$p}account.site_id = " . DEFAULT_SITE."
			  ORDER BY {$p}account.first_name,{$p}account.last_name";   
		$result = $db->SelectLimit($q, 10);          

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
		return;        
	}        



	###########################################
	### Top Sales Affiliates Graph:
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
		if(!$C_auth->auth_method_by_name('affiliate','search'))  {
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
				$dow            = date('w');
				$start_str      = mktime(0,0,0,date('m'),      date('d')-$dow,             date('y'));
				$end_str        = mktime(23,59,59,date('m'),   date('d'),                  date('y'));
			break;

			# By Months:
			case 'm':
				$start_str      = mktime(0,0,0,date('m'), 1,                                date('y'));
				$end_str        = mktime(23,59,59,date('m'),   date('d'),                   date('y'));
			break;

			# By Years:
			case 'y':
				$start_str      = mktime(0,0,0,1,1,                            date('y'));
				$end_str        = mktime(23,59,59,     date('m'),  date('d'),  date('y'));
			break;
		}


		######################################
		# Get accounts & sales for this period
		######################################
		$db     = &DB();
		$sql    = 'SELECT affiliate_id,total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
				   date_orig    >=  ' . $db->qstr( $start_str ) . ' AND  date_orig    <=  ' . $db->qstr( $end_str ) . ' AND
				   affiliate_id != ' .$db->qstr( '0' ). ' AND affiliate_id != '.$db->qstr( '' ).' AND
				   site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		while(!$result->EOF)
		{
			$amt  = $result->fields['total_amt'];
			$aid  = $result->fields['affiliate_id'];
			if(!isset( $arr[$aid] )) $arr[$aid] = 0;
			$arr[$aid] += $amt;
			$result->MoveNext();
		}

		$i = 0;
		$_datay = false;
		$_lbl = false;

		if(isset($arr) && is_array($arr)) {
			while(list($key, $var) = each($arr)) {
				if($i<5)
				{
					$_lbl[]   = strtoupper($key);
					$_datay[] = $var;
					$i++;
				}
			}
		} else {
			$file = fopen( PATH_THEMES.'default_admin/images/invisible.gif', 'r');
			fpassthru($file);
			exit;
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
		$left   = 85;
		$right  = 15;
		$graph->Set90AndMargin($left,$right,$top,$bottom);

		// Label align for X-axis
		$graph->xaxis->SetLabelAlign('right','center','right');

		// Label align for Y-axis
		$graph->yaxis->SetLabelAlign('center','bottom');
		$graph->xaxis->SetTickLabels($lbl);

		// Titles
		$graph->title->SetFont(FF_FONT1,FS_BOLD,10);
		$title = $C_translate->translate('graph_top','affiliate','');
		$graph->title->Set($title);

		// Create a bar pot
		$bplot = new BarPlot($datay);
		$bplot->SetFillColor("#506DC7");
		$bplot->SetWidth(0.2);

		// Show the values
		$bplot->value->Show();
		$bplot->value->SetFont(FF_FONT1,FS_NORMAL,7);
		$bplot->value->SetAlign('center','center');
		$bplot->value->SetColor("black","darkred");
		$bplot->value->SetFormat($currency_iso.'%.2f');

		$graph->Add($bplot);
		$graph->Stroke();
		return;
	}


	##############################
	##    MAIL MULTI ACCOUNTS   ##
	##############################
	function mail_multi($VAR)
	{
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
			$field_list =   AGILE_DB_PREFIX."affiliate.account_id";

			$q = eregi_replace("%%fieldList%%", $field_list, $search->sql);
			$q = eregi_replace("%%tableList%%", AGILE_DB_PREFIX."affiliate", $q);
			$q = eregi_replace("%%whereList%%", "", $q);
			$q .= " site_id = '" . DEFAULT_SITE . "'";

			$db = &DB();
			$affiliate = $db->Execute($q);

			// check results
			if($affiliate->RecordCount() == 0) {
			   $C_debug->alert($C_translate->translate('account_non_exist','account_admin',''));
			   return;
			}

			// load the mail class
			require_once(PATH_CORE   . 'email.inc.php');
			$email = new CORE_email;

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
			while ( !$affiliate->EOF ) {

				// get the account details
				$q = "SELECT email,first_name,last_name FROM ".AGILE_DB_PREFIX."account ".
					  "WHERE site_id = ".
					  $db->qstr(DEFAULT_SITE).
					  "AND id = ".
					  $db->qstr($affiliate->fields['account_id']);
				$account = $db->Execute($q);

				$E['priority']      = $VAR['mail_priority'];
				$E['html']          = '0';
				$E['subject']       = $VAR['mail_subject'];
				$E['body_text']     = $VAR['mail_message'];
				$E['to_email']      = $account->fields['email'];
				$E['to_name']       = $account->fields['first_name'] . ' ' . $account->fields['last_name'];
				$E['from_name']     = $setup_email->fields['from_name'];
				$E['from_email']    = $setup_email->fields['from_email'];

				### Call the mail() or smtp() function to send
				if($type == 0)
					$email->PHP_Mail($E);
				else
					$email->SMTP_Mail($E);

				### Next record
				$affiliate->MoveNext();
			}

		}  else {
			## Error message:
			$C_debug->alert($C_translate->translate('validate_any','',''));
			return;
		 }

		## Success message:
		$C_debug->alert($C_translate->translate('mail_sent','account_admin',''));
	}



	##############################
	##		USER VIEW           ##
	##############################
	function user_view($VAR)
	{
		global $smarty;

		# check if this affiliate account exists
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND                        
					account_id = ' . $db->qstr(SESS_ACCOUNT);
		if(!empty($this->id_prefix)) 
		$sql .= " AND id LIKE ".$db->qstr($this->id_prefix."%");

		$result = $db->Execute($sql);
		if($result == true && $result->RecordCount() > 0)
		{
			### Get the name of the plugin file to include:
			$sql1 = "SELECT file FROM ".AGILE_DB_PREFIX."affiliate_plugin WHERE
					id                = ". $db->qstr($result->fields['affiliate_plugin_id'])." AND
					site_id           = ". $db->qstr(DEFAULT_SITE);
			$plgn = $db->Execute($sql1);
			$plugin = "affiliate:plugin_" . $plgn->fields["file"];


			### Get the static vars:
			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;
			$arr = $static_var->update_form('affiliate', 'update', $result->fields['id']);

			$smarty->assign('static_var',	$arr);
			$smarty->assign('affiliate',    $result->fields);
			$smarty->assign('plugin_data',  unserialize($result->fields['plugin_data']));
			$smarty->assign('affiliate_plugin_file', $plugin);
			$smarty->assign('affiliate_user_view', true);

			############################################################################
			### START AFFILIATE STATISTICS ##################

			### Get the sessions referred by this affiliate:            		
			$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart["stats_sessions"] = $result2->RecordCount();


			### Get the accounts referred by this affiliate:
			$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."account WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart["stats_accounts"] = $result2->RecordCount();            		

			### Get the invoices referred by this affiliate:
			$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."invoice WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart["stats_invoices"] = $result2->RecordCount();              		


			### Get the commissions issued to this affiliate:
			$sql2 = "SELECT commission FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$total = 0;
			while(!$result2->EOF) {
				$total += $result2->fields['commission'];
				$result2->MoveNext();	
			} 
			$smart["stats_commissions"] = $total;  

			### Get the commissions (outstanding) to be issued to this affiliate: 
			$smart["commissions_due"] = $this->commission_due($result->fields['id']) ;                

			### END AFFILIATE STATISTICS ####################    		
			#############################################################################


			### Get available affiliate campaign details:
			$campaigns = unserialize($result->fields["avail_campaign_id"]);				
			if(count($campaigns) > 0)
			{
				$where=' WHERE ';	
				$num=0;			
				foreach($campaigns as $i) {
					if(!empty($i))
					{
						if($num > 0) $where .= " OR ";
						$where .= " id = ". $db->qstr($i);
					}
					$num++;
				}

				if($num > 0)
				{
					$sql2 = "SELECT id,name FROM ".AGILE_DB_PREFIX."campaign  
								$where
								AND site_id  = ". $db->qstr(DEFAULT_SITE);
					$result2 = $db->Execute($sql2);
					$i=0;
					if($result2 != false && $result2->RecordCount() > 0) {
						while(!$result2->EOF) {
							$campaigns[$i] = $result2->fields;
							$i++;	
							$result2->MoveNext();
						}  
					}  
					$smarty->assign('affiliate_campaign', $campaigns);   
				}  
			}  
			$smarty->assign('affiliate_stats', $smart);
			return;
		}
		else
		{
			# Get the affiliate template settings for the template
			$db     = &DB();
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'affiliate_template WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr(DEFAULT_AFFILIATE_TEMPLATE);
			$temp = $db->Execute($sql);


			### Get the static vars:
			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;
			$arr = $static_var->generate_form('affiliate', 'add', 'update');
			if(gettype($arr) == 'array')
			$smarty->assign('static_var',	$arr); 	 	


			# assign the smarty vars:
			//$smarty->assign('affiliate_plugin', $plugin);                
			$smarty->assign('affiliate_template', $temp->fields);
			$smarty->assign('affiliate_user_view', false);
			return;
		}
		return;
	}


	##############################
	##	USER ADD  		        ##
	##############################
	function user_add($VAR)
	{
		global $C_debug, $C_translate;

		# check if this affiliate account exists
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					account_id = ' . $db->qstr(SESS_ACCOUNT);
		$result = $db->Execute($sql);
		if($result != false && @$result->RecordCount() > 0)
		{
			$C_debug->alert($C_translate->translate('error_acct_aff_exist','affiliate',''));
			return;
		}

		## Get the affiliate template details:
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'affiliate_template WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id  = ' . $db->qstr(DEFAULT_AFFILIATE_TEMPLATE);
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0)
		{
			$C_debug->alert($C_translate->translate('error_template_invalid','affiliate',''));
			return;
		}


		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('affiliate', $this->val_error);

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

		## Get the affiliate id that referred this account:
		$sql    = 'SELECT affiliate_id FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id  = ' . $db->qstr(SESS_ACCOUNT);
		$ref = $db->Execute($sql);

		## Create the record
		$this->record_id = $db->GenID(AGILE_DB_PREFIX . "" . 'affiliate_id');
		$this->record_id = $this->id_prefix . "" . $this->record_id;

		## Generate the full query
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."affiliate
					SET
					id                    = ". $db->qstr($this->record_id).",
					site_id               = ". $db->qstr(DEFAULT_SITE) . ",
					account_id            = ". $db->qstr(SESS_ACCOUNT) .",
					parent_affiliate_id   = ". $db->qstr($ref->fields['affiliate_id']) .",
					status                = ". $db->qstr($result->fields['status']) .",
					affiliate_plugin      = ". $db->qstr(@$VAR['affiliate_affiliate_plugin']) .",
					avail_campaign_id     = ". $db->qstr($result->fields['avail_campaign_id']) .",
					max_tiers             = ". $db->qstr($result->fields['max_tiers']) .",
					commission_minimum    = ". $db->qstr($result->fields['commission_minimum']) .",
					new_commission_type   = ". $db->qstr($result->fields['new_commission_type']) .",
					new_commission_rate   = ". $db->qstr($result->fields['new_commission_rate']) .",		        	  	
					recurr_commission_type =". $db->qstr($result->fields['recurr_commission_type']) .",
					recurr_commission_rate =". $db->qstr($result->fields['recurr_commission_rate']) .",
					recurr_max_commission_periods = ". $db->qstr($result->fields['recurr_max_commission_periods']) .",		        	  	
					date_orig             = ". $db->qstr(time()) .",
					date_last             = ". $db->qstr(time());
		$result2 = $db->Execute($sql);
		if($result2 != false)
		{
			## Insert the static vars:
			$static_var->add($VAR, $this->module, $this->record_id);

			## Load the affiliate plugin and run the "Add" method:
			$file = strtoupper(eregi_replace('[^0-9a-z_-]{1,}', '', @$VAR['affiliate_affiliate_plugin']));

			$pluginfile = PATH_PLUGINS . 'affiliate/' . $file . '.php';
			if(@include_once($pluginfile))
			{
				eval ( '$_PLGN_AFF = new plgn_aff_'. strtoupper ( $file ) . ';' );
				$_PLGN_AFF->add(SESS_ACCOUNT, $this->record_id);
			}

			## Send the affiliate e-mail:
			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
			if($result->fields['status'] == "1")
			{
				$my1 = new email_template;
				$my1->send('affiliate_user_add_active', SESS_ACCOUNT, '', '', $this->record_id);	
				$C_debug->alert($C_translate->translate('user_add_active','affiliate',''));
			}
			else
			{
				$my1 = new email_template;
				$my1->send('affiliate_user_add_pending', SESS_ACCOUNT, '', '', $this->record_id);
				$my2 = new email_template;
				$my2->send('affiliate_user_add_staff_notify', SESS_ACCOUNT, '', '', $this->record_id);
				$C_debug->alert($C_translate->translate('user_add_inactive','affiliate',''));
			}

			global $VAR;
			$VAR['id'] = $this->record_id;
			define('FORCE_PAGE', $VAR['_page_current']);
		}
		else
		{
			$C_debug->alert('There was an error and the affiliate account could not be added.');
			define('FORCE_PAGE', $VAR['_page_current']);
		}    		
	}



	##############################
	##	USER UPDATE		        ##
	##############################
	function user_update($VAR)
	{
		global $C_debug, $C_translate;

		# check if this affiliate account exists
		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					account_id = ' . $db->qstr(SESS_ACCOUNT);
		$result = $db->Execute($sql);
		if($result == false || @$result->RecordCount() == 0)
		{
			$C_debug->alert($C_translate->translate('error_not_authorized','affiliate',''));
			return;
		}

		$VAR["affiliate_id"] = $result->fields["id"];

		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('affiliate', $this->val_error);

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

			global $C_vars;
			$C_vars->strip_slashes_all();
			return;
		}

		# special handling for the affiliate data array
		if(isset($VAR['affiliate_plugin_data']) && is_array($VAR['affiliate_plugin_data'])) {
			while (list($key,$val) = each($VAR['affiliate_plugin_data'])) {
				if(get_magic_quotes_gpc()) {
					$VAR['affiliate_plugin_data']["$key"] = htmlentities(stripcslashes($val), ENT_QUOTES);
				}
			}
		}

		$type = "user_update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->update($VAR, $this, $type);

		### Update the static vars:
		$static_var->update($VAR, 'affiliate', $VAR['affiliate_id']);

	}






	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		global $C_debug, $C_translate;

		## Verify the account id passed:
		if(empty($VAR['affiliate_account_id']))
		{
			$C_debug->alert($C_translate->translate('error_acct_req','affiliate',''));
			return;
		}

		## Get the affiliate template details:
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'affiliate_template WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id  = ' . $db->qstr($VAR['affiliate_template_id']);
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0)
		{
			$C_debug->alert($C_translate->translate('error_template_invalid','affiliate',''));
			return;
		}

		## Verify that this account does not have an affiliate account already:
		$sql    = 'SELECT account_id FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					account_id  = ' . $db->qstr($VAR['affiliate_account_id']);
		$acctc = $db->Execute($sql);
		if($acctc->RecordCount() > 0)
		{
			$C_debug->alert($C_translate->translate('error_acct_aff_exist','affiliate',''));
			return;
		}

		## Get the affiliate id that referred this account:
		$sql    = 'SELECT affiliate_id FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id  = ' . $db->qstr($VAR['affiliate_account_id']);
		$ref = $db->Execute($sql);

		## Create the record
		$this->record_id = $db->GenID(AGILE_DB_PREFIX . "" . 'affiliate_id');
		$this->record_id = $this->id_prefix . "" . $this->record_id;

		## Generate the full query
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."affiliate
					SET
					id                    = ". $db->qstr($this->record_id).",
					site_id               = ". $db->qstr(DEFAULT_SITE) . ",
					account_id            = ". $db->qstr($VAR['affiliate_account_id']) .",
					parent_affiliate_id   = ". $db->qstr($ref->fields['affiliate_id']) .",
					status                = ". $db->qstr("1") .",
					affiliate_plugin      = ". $db->qstr($result->fields['affiliate_plugin']) .",
					avail_campaign_id     = ". $db->qstr($result->fields['avail_campaign_id']) .",
					max_tiers             = ". $db->qstr($result->fields['max_tiers']) .",
					commission_minimum    = ". $db->qstr($result->fields['commission_minimum']) .",
					new_commission_type   = ". $db->qstr($result->fields['new_commission_type']) .",
					new_commission_rate   = ". $db->qstr($result->fields['new_commission_rate']) .",		        	  	
					recurr_commission_type =". $db->qstr($result->fields['recurr_commission_type']) .",
					recurr_commission_rate =". $db->qstr($result->fields['recurr_commission_rate']) .",
					recurr_max_commission_periods = ". $db->qstr($result->fields['recurr_max_commission_periods']) .",		        	  	
					date_orig             = ". $db->qstr(time()) .",
					date_last             = ". $db->qstr(time());
		$result2 = $db->Execute($sql);
		if($result != false)
		{
			## Load the affiliate plugin and run the "Add" method:
			$sql = "SELECT file FROM ".AGILE_DB_PREFIX."affiliate_plugin WHERE
					id                    = ". $db->qstr($result->fields['affiliate_plugin'])." AND
					site_id               = ". $db->qstr(DEFAULT_SITE);
			$plgn = $db->Execute($sql);
			@$pluginfile = PATH_PLUGINS . 'affiliate/' . $plgn->fields['file'] . '.php';
			if(@include_once($pluginfile))
			{
				eval ( '$_PLGN_AFF = new plgn_aff_'. strtoupper ( $plgn->fields['file'] ) . ';' );
				$_PLGN_AFF->add(SESS_ACCOUNT, $this->record_id);
			}

			## Send the affiliate e-mail:
			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
			$my = new email_template;
			$my->send('affiliate_staff_add', $VAR['affiliate_account_id'], '', '', $this->record_id);		     		

			## Redirect
			global $VAR;
			$VAR['id'] = $this->record_id;
			define('FORCE_PAGE', $VAR['_page']);  
		}
		else
		{
			$C_debug->alert('There was an error and the affiliate account could not be added.');
			define('FORCE_PAGE', $VAR['_page_current']);
		}    		
	}



	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{	
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
				  ".AGILE_DB_PREFIX."affiliate
				  WHERE					
				  $id_list
				  AND site_id = '" . DEFAULT_SITE . "'";	
			$result = $db->Execute($q);

			# error reporting
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('affiliate.inc.php','view', $db->ErrorMsg() . ' ' . $q);		        	
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

				$plugin_data = unserialize($result->fields["plugin_data"]);

				### Get the name of the plugin file to include:
				$sql1 = "SELECT file FROM ".AGILE_DB_PREFIX."affiliate_plugin WHERE
						id                = ". $db->qstr($result->fields['affiliate_plugin_id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
				$plgn = $db->Execute($sql1);
				$smart[$i]["plugin_file"] = "affiliate:plugin_" . $plgn->fields["file"];


				### Get the static vars:
				require_once(PATH_CORE   . 'static_var.inc.php');
				$static_var = new CORE_static_var;
				$arr = $static_var->update_form('affiliate', 'update', $result->fields['id']);
				if(gettype($arr) == 'array')
				{ 		
					$smart[$i]["static_var"] =	 	$arr;
				}

				############################################################################
				### START AFFILIATE STATISTICS ##################

				### Get the sessions referred by this affiliate:            		
				$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
				$result2 = $db->Execute($sql2);
				$smart[$i]["stats_sessions"] = $result2->RecordCount();


				### Get the accounts referred by this affiliate:
				$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."account WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
				$result2 = $db->Execute($sql2);
				$smart[$i]["stats_accounts"] = $result2->RecordCount();            		

				### Get the invoices referred by this affiliate:
				$sql2 = "SELECT id,total_amt FROM ".AGILE_DB_PREFIX."invoice WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
				$result2 = $db->Execute($sql2);
				$smart[$i]["stats_invoices"] = $result2->RecordCount();   
				$total = 0;
				while(!$result2->EOF) {
					$total += $result2->fields['total_amt'];
					$result2->MoveNext();	
				} 
				$smart[$i]["stats_invoices_amt"] = $total;                                 		

				### Get the commissions issued to this affiliate:
				$sql2 = "SELECT commission FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
				$result2 = $db->Execute($sql2);
				$total = 0;
				while(!$result2->EOF) {
					$total += $result2->fields['commission'];
					$result2->MoveNext();	
				} 
				$smart[$i]["stats_commissions"] = $total;  

				### Get the commissions (outstanding) to be issued to this affiliate: 
				$smart[$i]["commissions_due"] = $this->commission_due($result->fields['id']) ;                                



				### END AFFILIATE STATISTICS ####################    		
				#############################################################################


				$i++;        		   							   	
				$result->MoveNext();			   	
			}

			# get the result count:
			$results = $i;

			### No results:
			if($i == 0)
			{
				global $C_debug;
				$C_debug->error("CORE:affiliate.inc.php", "view()", "
				The selected affiliate does not exist any longer!");
				return;
			}	




			global $smarty;
			$smarty->assign($this->table, $smart);
			$smarty->assign('plugin_data', $plugin_data);
			$smarty->assign('results', 	$search->results);
		}    		
	}	


	function commission_due($affiliate_id)
	{
		$db = &DB();
		$sql2 = "SELECT invoice_id FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
						affiliate_id      = ". $db->qstr($affiliate_id)." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
		$result2 = $db->Execute($sql2);
		$sqla = '';
		while(!$result2->EOF)
		{
			$sqla .= " AND id != " . $result2->fields['invoice_id'];
			$result2->MoveNext();
		}
		$sql = "SELECT total_amt FROM ".AGILE_DB_PREFIX."invoice WHERE
						affiliate_id    = ". $db->qstr($affiliate_id)." AND
						billing_status	= 1 
						{$sqla} 
						AND
						site_id         = ". $db->qstr(DEFAULT_SITE);
		$result2 = $db->Execute($sql);
		$total = 0;
		while(!$result2->EOF) {
			$total += $result2->fields['total_amt'];
			$result2->MoveNext();
		} 

		return $total;
	}    		





	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('affiliate', $this->val_error);

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

		# special handling for the affiliate data array
		if(isset($VAR['affiliate_plugin_data']) && is_array($VAR['affiliate_plugin_data'])) {
			while (list($key,$val) = each($VAR['affiliate_plugin_data'])) {
				if(get_magic_quotes_gpc()) {
					$VAR['affiliate_plugin_data']["$key"] = htmlentities(stripcslashes($val), ENT_QUOTES);
				}
			}
		}

		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->update($VAR, $this, $type);

		### Update the static vars:
		$static_var->update($VAR, 'affiliate', $VAR['affiliate_id']);

	}






	##############################
	##		 MASS DELETE        ##
	##############################
	function delete($VAR)
	{	
		$id_list = '';
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
				$ret = $this->delete_one($id[$i]);
			}					
		}

		if($ret)
		{    		
			# Alert delete message
			global $C_debug, $C_translate;
			$C_translate->value["CORE"]["module_name"] = $C_translate->translate('name',$this->module,"");
			$message = $C_translate->translate('alert_delete_ids',"CORE","");
			$C_debug->alert($message);	
		}
	}



	##############################
	##		 MASS DELETE        ##
	##############################
	function delete_one($id)
	{
		# get the parent affiliate id for this account, if any
		$db     = &DB();
		$sql    = 'SELECT parent_affiliate_id FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id= ' . $db->qstr($id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			if(!empty($result->fields['parent_affiliate_id']))
			{
				## update all affiliates one this affiliate's main tier
				## to assume the position this affiliate was in:
				$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'affiliate
							SET
							parent_affiliate_id = ' . $db->qstr($result->fields['parent_affiliate_id']) . '
							WHERE
							site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
							parent_affiliate_id= ' . $db->qstr($id);
				$db->Execute($sql);
			}
		}
		# delete any commission records for this affiliate:
		$sql    = 'DELETE FROM ' . AGILE_DB_PREFIX . 'affiliate
					WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($id);
		$db->Execute($sql);
		return true;
	}


	##############################
	##       SEARCH EXPORT        ##
	##############################
	function search_export($VAR)
	{
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
					if(eregi($pat, $key))
					{	 				
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " WHERE ".AGILE_DB_PREFIX."affiliate.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
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
											$where_list .= " WHERE ".AGILE_DB_PREFIX."affiliate.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
										   $where_list .= " AND ".AGILE_DB_PREFIX."affiliate.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										   $i++;
										}
								   }
								}
							}
							else
							{	
							   $where_list .= " WHERE ".AGILE_DB_PREFIX."affiliate.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
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
					if(eregi($pat, $key))
					{
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " AND ".AGILE_DB_PREFIX."affiliate.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
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

										$where_list .= " AND ".AGILE_DB_PREFIX."affiliate.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
								   }
								}
							}
							else
							{		
							   $where_list .=  " AND ".AGILE_DB_PREFIX."affiliate.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
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
		$q = "SELECT DISTINCT {$pre}affiliate.id  FROM  {$pre}affiliate "; 
		$q_save = "SELECT DISTINCT 
					{$pre}account.first_name,
					{$pre}account.last_name,
					{$pre}account.username,
					{$pre}account.email,
					%%fieldList%% 
			FROM 
				{$pre}affiliate 
			LEFT JOIN
				{$pre}account
			ON
				{$pre}affiliate.account_id = {$pre}account.id ";


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
					if(ereg("%", $value))
						$join_list .= " s{$idx}.value LIKE ".$db->qstr($VAR["static_relation"]["$idx"]);
					else
						$join_list .= " s{$idx}.value = ".$db->qstr($VAR["static_relation"]["$idx"]);
					$join_list .= " ) "; 
				}
			}  
		}  
		######## END STATIC VAR SEARCH ##################


		# standard where list
		$q .= $join_list . $where_list ." ".AGILE_DB_PREFIX."affiliate.site_id = " . $db->qstr(DEFAULT_SITE);
		$q_save .= $join_list . $where_list ." %%whereList%% ";


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
			$C_debug->error('affiliate.inc.php','search', $db->ErrorMsg());	  
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
				$field_list .= AGILE_DB_PREFIX . "affiliate" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "affiliate" . "." . $value;

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
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'affiliate.'.$VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'affiliate.'.$this->order_by;
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
			if (!eregi('date',$smarty_order)) {
				$order_by .= ' ASC';
				$smarty_sort = 'asc=';
			} else {
				$order_by .= ' DESC';
				$smarty_sort = 'desc=';
			}
		}

		# generate the full query 
		$db = &DB();
		$q = eregi_replace("%%fieldList%%", $field_list, $search->sql);
		$q = eregi_replace("%%tableList%%", AGILE_DB_PREFIX.$construct->table, $q);
		$q = eregi_replace("%%whereList%%", "", $q);
		$q .= " ".AGILE_DB_PREFIX . "affiliate."."site_id = " . $db->qstr(DEFAULT_SITE);
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
			$C_debug->error('affiliate.inc.php','search_show', $db->ErrorMsg());

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

			### Get the sessions referred by this affiliate:
			$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart[$i]["stats_sessions"] = $result2->RecordCount();


			### Get the accounts referred by this affiliate:
			$sql2 = "SELECT id FROM ".AGILE_DB_PREFIX."account WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart[$i]["stats_accounts"] = $result2->RecordCount();

			### Get the invoices referred by this affiliate:
			$sql2 = "SELECT id,total_amt FROM ".AGILE_DB_PREFIX."invoice WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$smart[$i]["stats_invoices"] = $result2->RecordCount();
			$total = 0;
			while(!$result2->EOF) {
				$total += $result2->fields['total_amt'];
				$result2->MoveNext();
			}
			$smart[$i]["stats_invoices_amt"] = $total;

			### Get the commissions issued to this affiliate:
			$sql2 = "SELECT commission FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
						affiliate_id      = ". $db->qstr($result->fields['id'])." AND
						site_id           = ". $db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql2);
			$total = 0;
			while(!$result2->EOF) {
				$total += $result2->fields['commission'];
				$result2->MoveNext();
			}
			$smart[$i]["stats_commissions"] = $total;

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
	##		STATIC VARS         ##
	##############################

	function static_var($VAR)
	{	
		global $smarty;

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;

		if(ereg('search', $VAR['_page']))
		$arr = $static_var->generate_form($this->module, 'add', 'search');
		else
		$arr = $static_var->generate_form($this->module, 'add', 'update'); 

		if(gettype($arr) == 'array')
		{ 	
			### Set everything as a smarty array, and return:
			$smarty->assign('show_static_var',		true);
			$smarty->assign('static_var',	$arr);
			return true;		 	
		}
		else
		{		 	
			### Or if no results:
			$smarty->assign('show_static_var',		false);
			return false;	           	 			 	
		}
	}     	
}
?>