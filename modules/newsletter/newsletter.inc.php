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
	
class newsletter
{

	# Open the constructor for this mod
	function newsletter_construct()
	{
		# name of this module:
		$this->module = "newsletter";

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



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->newsletter_construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{	
		$this->newsletter_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}	


	##############################
	##		POPUP VIEW  	    ##
	##############################
	function popup($VAR)
	{	
		$this->newsletter_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}	        	

	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$this->newsletter_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$this->newsletter_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->newsletter_construct();
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
		$this->newsletter_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{	
		$this->newsletter_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}



	##############################
	##	 SEND NEWSLETTERS       ##
	##############################

	function send($VAR)
	{
		global $C_debug, $C_translate, $C_vars;

		$C_vars->strip_slashes_all();

		#####################################################
		### Get the setup email settings:

		$db = &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				id          = ".$db->qstr($VAR["setup_email_id"]);
		$setup_email        = $db->Execute($q);

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


		#####################################################
		### Priority:

		if(isset($VAR['newsletter_priority']) && $VAR['newsletter_priority'] == '1')
		$E['priority']      = 1;


		#####################################################
		### Subject:

		if(isset($VAR['newsletter_subject']) && $VAR['newsletter_subject'] != '')
		{
			$E['subject'] = $VAR['newsletter_subject'];
		}
		else
		{
			### ERROR!
			$C_debug->alert($C_translate->translate('send_alert_message','newsletter',''));
			$C_debug->alert($C_translate->translate('send_alert_fail','newsletter',''));
			return;
		}


		#####################################################
		### Body (TEXT version):

		if(isset($VAR['newsletter_body_text']) && $VAR['newsletter_body_text'] != '')
		{

			$E['body_text'] = $VAR['newsletter_body_text'];

		}
		else
		{
			### ERROR!
			$C_debug->alert($C_translate->translate('send_alert_body_text','newsletter',''));
			$C_debug->alert($C_translate->translate('send_alert_fail','newsletter',''));
			return;
		}

		#####################################################
		### Body (HTML version):

		if(isset($VAR['newsletter_body_html']) && $VAR['newsletter_body_html'] != '')
		{
			$E['body_html'] = $VAR['newsletter_body_html'];
		}
		 else
		{
			### ERROR!
			$C_debug->alert($C_translate->translate('send_alert_body_html','newsletter',''));
			$C_debug->alert($C_translate->translate('send_alert_fail','newsletter',''));
			return;
		}




		#####################################################
		### TEST - Get current user account details

		$db = &DB();
		$q  = "SELECT * FROM ".AGILE_DB_PREFIX."account WHERE
			   site_id  = ".$db->qstr(DEFAULT_SITE)." AND
			   id       = ".$db->qstr(SESS_ACCOUNT);
		$account = $db->Execute($q);

		$E['to_email'] = $account->fields['email'];
		$E['to_name']  = $account->fields['first_name'] . ' ' . $account->fields['last_name'];

		### Call the mail() or smtp() function to send
		require_once(PATH_CORE   . 'email.inc.php');
		$email = new CORE_email;

		if($type == 0)
		{
			if(isset($E['body_html']))
			{
				### SEND HTML VERSION
				$E['html'] = '1';
				$email->PHP_Mail($E);
			}

			### SEND TEXT VERSION
			$E['html'] = '0';
			$email->PHP_Mail($E);

			$C_debug->alert("Send success!");
		}
		else
		{
			if(isset($E['body_html']))
			{
				### SEND HTML VERSION
				$E['html'] = '1';
				$email->SMTP_Mail($E);
			}

			### SEND TEXT VERSION
			$E['html'] = '0';
			$email->SMTP_Mail($E);
		}





		#####################################################
		###  LIVE - Get all subscribers
		if(!isset($VAR['newsletter_test']))
		{

			### SET THE BODY & SUBJECT
			$body_text = $E['body_text'];
			$body_html = $E['body_html'];
			$subject   = $E['subject'];

			### Delete the cc/bcc lists...
			unset($E['cc_list']);
			unset($E['bcc_list']);

			$newsletters = $VAR['newsletter_id'];
			for($i=0; $i<count($newsletters); $i++)
			{

				$db = &DB();
				$q = "SELECT * FROM ".AGILE_DB_PREFIX."newsletter_subscriber WHERE
					  site_id     = ".$db->qstr(DEFAULT_SITE)." AND
					  newsletter_id          = ".$db->qstr($newsletters[$i]);
				$subs             = $db->Execute($q);

				### LOOP through the results:
				while(!$subs->EOF)
				{
					$id = $subs->fields['email'];
					# Check that this subscription is not already in the array
					if(!isset($arr_s[$id]))
						$arr_s[$id] = Array('html' => $subs->fields["html"],
											'fName'=> $subs->fields["first_name"],
											'lName'=> $subs->fields["last_name"]);
					$subs->MoveNext();
				}
			}


			### LOOP through the final results
			if(!isset($arr_s) || gettype($arr_s) != 'array')
			{
				### NO RESULTS... RETURN NOW!
				$C_debug->alert($C_translate->translate('send_alert_no_results','newsletter',''));
				$C_debug->alert($C_translate->translate('send_alert_fail','newsletter',''));
				return;

			}

			$i = 0;
			while (list ($key, $val) = each ($arr_s))
			{
				$i++;

				$E['to_email'] = $key;
				$E['to_name']  = $arr_s["$key"]["fName"] . ' ' . $arr_s["$key"]["lName"];

				$remove = URL . '?_page=newsletter:unsubscribe&email='.$key;

				if($arr_s["$key"]["html"] == '1')
				{
					### replace %name%, %email%, and %remove% with correct vars: (for html)
					$E['body_html'] = eregi_replace('%name%',   $arr_s["$key"]["fName"], $body_html);
					$E['body_html'] = eregi_replace('%email%',  $key,                    $E['body_html']);
					$E['body_html'] = eregi_replace('%remove%', $remove,                 $E['body_html']);
					$E['html'] = '1';
				}
				else
				{
					### replace %name%, %email%, and %remove% with correct vars: (for text)
					$E['body_text'] = eregi_replace('%name%',   $arr_s["$key"]["fName"], $body_text);
					$E['body_text'] = eregi_replace('%email%',  $key,                    $E['body_text']);
					$E['body_text'] = eregi_replace('%remove%', $remove,                 $E['body_text']);
					$E['html'] = false;
				}


				### replace %name%, %email%, and %remove% with correct vars: (for subject)
				$E['subject'] = eregi_replace('%name%',         $arr_s["$key"]["fName"],   $subject);
				$E['subject'] = eregi_replace('%email%',        $key,                      $E['subject']);


				if($type == 0)
				{
					### SEND THE MESSAGE
					$email = new CORE_email;
					$email->PHP_Mail($E);
				}
				else
				{
					### SEND TEXT VERSION
					$email = new CORE_email;
					$email->SMTP_Mail($E);
				}
			}

			### Completion notice
			$C_debug->alert($C_translate->translate('send_alert_success','newsletter',''));
			## Sent $i messages...
		}
	}


	##############################
	##	 SHOW NEWSLETTER LIST   ##
	##############################

	function check_list($VAR)
	{

		$name     = "newsletter_id";
		$table    = 'newsletter';
		$field    = 'name';
		$id       = '';
		$input_id = '';

		global $C_translate, $C_auth;

		# get the records
		$db = &DB();
		$sql= "SELECT id,group_avail,name FROM ".AGILE_DB_PREFIX."$table
				WHERE site_id = '" . DEFAULT_SITE . "' AND
				active        = " . $db->qstr('1') . "
				ORDER BY $field";
		$result = $db->Execute($sql);

		# error handling
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','check', $db->ErrorMsg());
		}			


		# loop through the records
		$i = 0;
		while (!$result->EOF) {

			$groups = unserialize($result->fields['group_avail']);

			$auth = false;
			for($ii=0; $ii<count($groups); $ii++)
			{
				if($C_auth->auth_group_by_id($groups[$ii])) $auth = true;
			}

			if($auth)
			{
				#  Create the return code for Smarty
				$return .= '<input id="'. $input_id .'" type="checkbox" name="'.
							$name .'[]" value="' . $result->fields["id"] . '" checked> ' .
							$result->fields["$field"] .
							'<a href="#" onClick="window.open(\'?_page=newsletter:popup&id='.$result->fields["id"].
							'&_escape=1\', \'newsletter_popup\', \'height=400, width=400, resizable=yes, toolbar=no, status=no\')">&nbsp;&nbsp;[ ? ]</a>
							<BR>';
				$i++;
			}
			$result->MoveNext();
		}

		if($i==0)
		$return .= 'No Newsletters Available to Your Account';

		echo $return;
	}	





	##############################
	##	 SHOW NEWSLETTER LIST   ##
	##    FOR REGISTRATION      ##
	##############################

	function check_list_registration($VAR)
	{

		$name     = "newsletter_id";
		$table    = 'newsletter';
		$field    = 'name';
		$id       = '';
		$input_id = '';

		global $C_translate, $C_auth;

		# get the records
		$db = &DB();
		$sql= "SELECT id,group_avail,name FROM ".AGILE_DB_PREFIX."$table
				WHERE site_id = '" . DEFAULT_SITE . "' AND
				active        = " . $db->qstr('1') . " AND
				display_signup = " . $db->qstr('1') . "
				ORDER BY $field";
		$result = $db->Execute($sql);

		# error handling
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','check', $db->ErrorMsg());
		}			


		# loop through the records
		$i = 0;
		while (!$result->EOF) {

			$groups = unserialize($result->fields['group_avail']);

			$auth = false;
			for($ii=0; $ii<count($groups); $ii++)
			{
				if($C_auth->auth_group_by_id($groups[$ii])) $auth = true;
			}

			if($auth)
			{
				#  Create the return code for Smarty
				$return .= '<input id="'. $input_id .'" type="checkbox" name="'.
							$name .'[]" value="' . $result->fields["id"] . '" checked> ' .
							$result->fields["$field"] .
							'<a href="#" onClick="window.open(\'?_page=newsletter:popup&id='.$result->fields["id"].
							'&_escape=1\', \'newsletter_popup\', \'height=400, width=400, resizable=yes, toolbar=no, status=no\')">&nbsp;&nbsp;[ ? ]</a>
							<BR>';
				$i++;
			}
			$result->MoveNext();
		}

		if($i==0)
		$return .= 'No Newsletters Available to Your Account';

		echo $return;
	}	







	##############################
	##	 SUBSCRIBE              ##
	##############################

		/* we need the following vars:

		'newsletter_id'         (array)
		'newsletter_type'       (0/1) ( standalone / with new account)
		'newsletter_html'       (0/1)

		'newsletter_email'      (email_required)
		'newsletter_first_name' (first name, required)
		'newsletter_last_name'  (last_name, optional)
			or
		'account_email'         (email_required)
		'account_first_name'    (from the signup form)
		'account_last_name'     (from the singup form)

		*/


	function subscribe($VAR)
	{
		$LIMIT_SECONDS = 120;

		global $C_debug, $C_translate;

		### Include the validation class
		include_once(PATH_CORE . 'validate.inc.php');

		### store the details in a temporary database, and email the user
		### a link with the time() string from the creation date of the
		### record



		### Check that the required variables are set:
		if(!isset($VAR['newsletter_id']) || gettype($VAR['newsletter_id']) != 'array')
		{
			if(isset($VAR['newsletter_type'])) {
				#ERROR!
				$C_debug->alert($C_translate->translate('subscribe_newsletter_req','newsletter',''));
				return;
			} else {
				return;
			}
		}

		$newsletter_id = @$VAR['newsletter_id'];

		if(isset($VAR['newsletter_html']))
		$html = 1;
		else
		$html = 0;

		if(isset($VAR['newsletter_type']))
		{
			if(empty($VAR['newsletter_first_name']))
			{
				#### ERROR!
				if (isset($VAR['newsletter_type']))
				$C_debug->alert($C_translate->translate('subscribe_name_req','newsletter',''));
				return;
			}

			$validate = new CORE_validate;
			if(empty($VAR['newsletter_email']) || !$validate->validate_email($VAR['newsletter_email'], ''))
			{ 
				### ERROR!
				if (isset($VAR['newsletter_type']))
				$C_debug->alert($C_translate->translate('subscribe_email_req','newsletter',''));
				return;
			}

			$first_name = @$VAR['newsletter_first_name'];
			$last_name  = @$VAR['newsletter_last_name'];
			$email      = @$VAR['newsletter_email'];
		}
		else
		{
			if(!isset($VAR['account_first_name']) || $VAR['account_first_name'] == '')
			return;

			$validate = new CORE_validate;
			if(!isset($VAR['account_email'])      || $validate->validate_email($VAR['account_email'], '') == false)
			return;


			$first_name = @$VAR['account_first_name'];
			$last_name  = @$VAR['account_last_name'];
			$email      = @$VAR['account_email'];
		}


		### Check that this email has not been requested already
		### In the last 60 seconds

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					field1      = ' . $db->qstr($email);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$limit = $result->fields['date_orig'] + $LIMIT_SECONDS;

			if($limit > time())
			{
				### ERROR!
				if(isset($VAR['newsletter_type']))
				{
					$error1 = $C_translate->translate("subscribe_spam_limit","newsletter","");
					$error = ereg_replace('%limit%', "$LIMIT_SECONDS", $error1);
					$C_debug->alert( $error );
				}
				return;
			}
			else
			{
				### Delete the old request
				$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						field1      = ' . $db->qstr($email);
				$db->Execute($sql);
			}

		}

		#####################################################
		### Ok to continue:

		$now    = time();
		$expire = time() + 86400*3;
		$data   = serialize(Array ('html'   => $html,
							'email'         => $email,
							'first_name'    => $first_name,
							'last_name'     => $last_name,
							'newsletter_id' => $newsletter_id,
							'var'			=> base64_encode(serialize(@$VAR['static_relation']))));

		#####################################################
		### Create the temporary DB Record:

		$db     = &DB();
		$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'temporary_data_id');
		$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'temporary_data SET
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ',
					id          = ' . $db->qstr($id) . ',
					date_orig   = ' . $db->qstr($now) . ',
					date_expire = ' . $db->qstr($expire) . ',
					field1      = ' . $db->qstr($email) . ',
					data        = ' . $db->qstr($data) ;
		$result = $db->Execute($sql);

		#####################################################
		### Send the subscription confirmation email :

		$E['html']     = 0;
		$E['priority'] = 0;
		$E['to_email'] = $email;
		$E['to_name']  = $first_name;

		global $C_translate;
		$E['body_text'] = $C_translate->translate('subscribe_body','newsletter','');
		$E['subject']   = $C_translate->translate('subscribe_subj','newsletter','');

		$E['body_text'] = eregi_replace('%name%', $first_name, $E['body_text']);
		$E['body_text'] = eregi_replace('%email%', $email, $E['body_text']);
		$E['body_text'] = eregi_replace('%confirm_url%', URL.'?_page=newsletter:subscribe_confirm&email='.$email.'&validate='.$now, $E['body_text']);
		$E['body_text'] = eregi_replace('%site_name%', SITE_NAME, $E['body_text']);


		#####################################################
		### Get the setup email settings:

		$db = &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				id          = ".$db->qstr(DEFAULT_SETUP_EMAIL);
		$setup_email        = $db->Execute($q);

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


		######################################################
		### SEND THE MESSAGE!

		require_once(PATH_CORE   . 'email.inc.php');
		$email = new CORE_email;

		if($type == 0)
		{
			### SEND THE MESSAGE
			$email->PHP_Mail($E);
		}
		else
		{
			### SEND TEXT VERSION
			$email->SMTP_Mail($E);
		}

		#####################################################
		### Success message!
		if(isset($VAR['newsletter_type']))
		{
			$message = $C_translate->translate('subscribe_confirm', 'newsletter', '');
			$C_debug->alert($message);
		}
	}





	##############################
	##	 SUBSCRIBE CONFIRM      ##
	##############################

	function subscribe_confirm($VAR)
	{
		global $C_debug, $C_translate;

		### validate that the user provided their email

		/*
		We need the following vars to confirm:

		'email'
		'validate'

		*/

		if(!isset($VAR['email']) || !isset($VAR['validate']))
		{
			### ERROR: bad link....
			$url = '<br><a href="'. URL . '?_page=newsletter:subscribe">' . $C_translate->translate('submit','CORE','') . '</a>';
			$message = eregi_replace('%here%', $url, $C_translate->translate('subscribe_confirm_fail','newsletter',''));
			echo $message;
			return;
		}
		else
		{
			### Confirm the email/timestamp match
			$email = @$VAR['email'];
			$time  = @$VAR['validate'];


			$db     = &DB();
			$sql    = 'SELECT data FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
						site_id     = ' .   $db->qstr(DEFAULT_SITE) . ' AND
						date_orig   = ' .   $db->qstr($time) . ' AND
						field1      = ' .   $db->qstr($email);
			$result = $db->Execute($sql);

			if($result->RecordCount() == 0)
			{
				### ERROR: no match for submitted link, invalid or expired.
				$url = '<br><a href="'. URL . '?_page=newsletter:subscribe">' . $C_translate->translate('submit','CORE','') . '</a>';
				$message = eregi_replace('%here%', $url, $C_translate->translate('subscribe_confirm_fail','newsletter',''));
				echo $message;
				return;
			}

			$arr = unserialize($result->fields['data']); 
			@$varstored['static_relation'] = unserialize(base64_decode($arr['var']));


			###############################################################
			### Delete the temporary record
			$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
					site_id     = ' .       $db->qstr(DEFAULT_SITE) . ' AND
					field1      = ' .       $db->qstr($email);
			$db->Execute($sql);




			###############################################################
			### Create the newsletter subscription(s):
			$db     = &DB();
			for($i=0; $i<count($arr['newsletter_id']); $i++)
			{
				#########################################################
				### Drop any existing subscriptions to avoid duplicates!
				$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'newsletter_subscriber WHERE
						site_id     = ' .  $db->qstr(DEFAULT_SITE) . ' AND
						newsletter_id=' .  $db->qstr($arr["newsletter_id"][$i]) . ' AND
						email        = ' . $db->qstr($email);
				$db->Execute($sql);


				### Insert
				$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'newsletter_subscriber_id');
				$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'newsletter_subscriber SET
							site_id  =  ' .   $db->qstr(DEFAULT_SITE) . ',
							id          = ' . $db->qstr($id) . ',
							date_orig   = ' . $db->qstr(time()) . ',
							newsletter_id= '. $db->qstr($arr["newsletter_id"][$i]) . ',
							email       = ' . $db->qstr($arr["email"]) . ',
							html        = ' . $db->qstr($arr["html"]) . ',
							first_name  = ' . $db->qstr($arr["first_name"]) . ',
							last_name   = ' . $db->qstr($arr["last_name"]) ;
				$result = $db->Execute($sql);

				### Set the static vars: 
				require_once(PATH_CORE   . 'static_var.inc.php');
				$static_var = new CORE_static_var;     		
				$static_var->add($varstored, 'newsletter_subscriber', $id); 
			}


			### Return the success message:
			echo $C_translate->translate('subscribe_confirm_success','newsletter','');
		}

	}




	##############################
	##	 UNSUBSCRIBE            ##
	##############################

	function unsubscribe($VAR)
	{
		global $C_debug, $C_translate;

		### Check required var
		if(!isset($VAR['newsletter_email']) || $VAR['newsletter_email'] == '')
		{
			### ERROR: bad link....
			$message = $C_translate->translate('subscribe_email_req','newsletter','');
			$C_debug->alert($message);
			return;
		}

		### Confirm the email/timestamp match
		$email          = @$VAR['newsletter_email'];
		$n_id           = @$VAR['newsletter_id'];


		#########################################################
		### Drop any existing subscriptions to avoid duplicates!

		$db = &DB();
		if(gettype($n_id) == 'array')
		{
			for($i=0; $i<count($n_id); $i++)
			{
				$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'newsletter_subscriber WHERE
						site_id     = ' .  $db->qstr(DEFAULT_SITE) . ' AND
						newsletter_id=' .  $db->qstr($n_id[$i]) . ' AND
						email        = ' . $db->qstr($email);
				$db->Execute($sql);
			}
		}

		### Return Success!

		$message = $C_translate->translate('unsubscribe_success','newsletter','');
		$C_debug->alert($message);
	}                                                                                       			
}
?>