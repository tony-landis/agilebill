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
	
class ticket
{
	var $in_ticket_id;

	/**
	 * Return a smarty array of matching tickets for departmnet/status search
	 */
	function search_status($VAR) {
		extract($VAR);
		
		global $smarty; 
		$smarty->assign("count", '0');

		if(empty($department) || empty($status)) return false;
		
		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$sql = "SELECT DISTINCT 
        		A.id, A.subject, A.status, A.email, A.date_orig, A.body, B.email as email_acct, CONCAT(B.first_name,' ',B.last_name) as name, B.company, C.name as department 
				FROM
				{$p}ticket as A  
				left join {$p}account as B on ((B.id=A.account_id OR B.email=A.email) AND B.site_id=".DEFAULT_SITE.")
				left join {$p}ticket_department as C on (A.department_id=C.id AND C.site_id=".DEFAULT_SITE.") 
				WHERE 
				A.site_id=".DEFAULT_SITE." AND department_id=".$db->qstr($department);
		switch($status) { 	
			case 'new':
				$sql.= " AND (A.last_reply=0 OR A.last_reply IS NULL ) AND (A.status=0 OR A.status IS NULL)";
			break;
			case 'staff':
				$sql.= " AND A.date_orig != A.date_last AND A.last_reply=2 AND A.status=0";
			break;		
			case 'user':
				$sql.= " AND A.date_orig != A.date_last AND A.last_reply=1 AND A.status=0";
			break;	
			case 'hold':
				$sql.= " AND A.status=1";
			break;	
			case 'pending':
				$sql.= " AND A.status=3";
			break;	
			case 'closed':
				$sql.= " AND A.status=2";
			break;													
		} 
		$rs=$db->Execute($sql);
		if($rs && $rs->RecordCount()) {			
			while(!$rs->EOF) {
				if(empty($rs->fields["email"])) $rs->fields['email'] = $rs->fields['email_acct'];
				if(!empty($rs->fields["company"])) $rs->fields['email'] = $rs->fields['company'];
				elseif(!empty($rs->fields["name"])) $rs->fields['email'] = $rs->fields['name'];
				$smart[]=$rs->fields;
				$rs->MoveNext();
			}
			$smarty->assign("results", $smart); 
			$count=$rs->RecordCount();
		} else {
			return false;
		}
		$smarty->assign("count", $count);		
	}
	
 

	/**
     * Return a smarty array of matching tickets for full search
     */
	function search_quick($VAR) {
		extract($VAR);

		global $smarty; 
		$smarty->assign("count", '0');
				
		if(empty($query)) return false;
		if($query_type=='all' || $query_type=='text') $this->search_text($query);
		if($query_type=='all' || $query_type=='sender') $this->search_user($query);
		if(!$this->in_ticket_id) return false;

		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$sql = "SELECT DISTINCT FIELD(A.id,".$this->search_sql_in_ticket_id().") as rank,
        		A.id, A.subject, A.status, A.email, A.date_orig, A.body, B.email as email_acct, CONCAT(B.first_name,' ',B.last_name) as name, B.company, C.name as department 
				FROM
				{$p}ticket as A  
				left join {$p}account as B on ((B.id=A.account_id OR B.email=A.email) AND B.site_id=".DEFAULT_SITE.")
				left join {$p}ticket_department as C on (A.department_id=C.id AND C.site_id=".DEFAULT_SITE.") 
				WHERE 
				A.site_id=".DEFAULT_SITE." AND A.id in (".$this->search_sql_in_ticket_id().") ";        	
		if($department!='all') $sql.= " AND A.department_id=".$db->qstr($department);
		if($status!='all') $sql.= " AND A.status=".$db->qstr($status);
		$sql .= " ORDER BY rank";
		#echo "<BR> ". $sql; 
		$rs=$db->Execute($sql);
		if($rs && $rs->RecordCount()) {			
			while(!$rs->EOF) {
				if(empty($rs->fields["email"])) $rs->fields['email'] = $rs->fields['email_acct'];
				if(!empty($rs->fields["company"])) $rs->fields['email'] = $rs->fields['company'];
				elseif(!empty($rs->fields["name"])) $rs->fields['email'] = $rs->fields['name'];
				$smart[]=$rs->fields;
				$rs->MoveNext();
			}
			$smarty->assign("results", $smart); 
			$count=$rs->RecordCount();
		} else {
			return false;
		}
		$smarty->assign("count", $count);
	}

	/**
     * Retrieve list of ticket ids that match the fulltext
     */
	function search_text($query, $offset=0, $limit=100) {
		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$sql = "select distinct
				ticket_id from {$p}ticket_message WHERE MATCH (message) AGAINST (".$db->qstr($query)." IN BOOLEAN MODE) 
				union select distinct id from {$p}ticket WHERE MATCH (subject, body) AGAINST (".$db->qstr($query)." IN BOOLEAN MODE) 
				union select distinct ticket_id from {$p}ticket_attachment WHERE MATCH (content) AGAINST (".$db->qstr($query)." IN BOOLEAN MODE)"; 
		$rs=$db->SelectLimit($sql, $limit, $offset);
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				$this->search_add_in_ticket_id($rs->fields['ticket_id']);
				$rs->MoveNext();
			}
			return true;
		}
		return false;
	}

	/**
     * Retrieve list of ticket ids that match the provided email, name, or company
     */
	function search_user($query, $offset=0, $limit=100) {
		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$sql = "select distinct A.id from {$p}ticket as A, {$p}account as B
       			WHERE (
       			  A.site_id=".DEFAULT_SITE." AND A.email LIKE ".$db->qstr('%'.$query.'%')." AND B.id is null
        		  OR  
        		  ( 
        		   A.account_id=B.id AND A.site_id=".DEFAULT_SITE." AND B.site_id=".DEFAULT_SITE." AND
        		   (
        		    MATCH (B.first_name,B.last_name,B.email,B.company) AGAINST (".$db->qstr($query)." IN BOOLEAN MODE) 
        		   )
        		  )
        		)";   		
		$rs=$db->SelectLimit($sql, $limit, $offset);
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				$this->search_add_in_ticket_id($rs->fields['id']);
				$rs->MoveNext();
			}
			return true;
		}
		return false;
	}

	/**
     * Add ticket id to in_ticket_id array
     */
	function search_add_in_ticket_id($id) {
		if(!is_array($this->in_ticket_id) || !in_array($id, $this->in_ticket_id)) $this->in_ticket_id[]=$id;
	}

	/**
     * Format in_ticket_id to string for sql query
     */
	function search_sql_in_ticket_id() {
		if(is_array($this->in_ticket_id)) {
			return implode(",", $this->in_ticket_id);
		} else {
			return false;
		}
	}



	##############################
	## Show Merge List		    ##
	##############################
	function merge_list($VAR)
	{
		$id = ereg_replace(',','', $VAR['id']);

		global $C_translate;

		# Get account id / email:
		$db = &DB();
		$sql= "SELECT account_id,email FROM ".AGILE_DB_PREFIX."ticket
					WHERE site_id = '".DEFAULT_SITE."' AND
					id = '{$id}'";
		$rs = $db->Execute($sql);
		$account_id = $rs->fields['account_id'];
		$email =  $rs->fields['email'];

		# Get available tickets:
		if($account_id > 0)
		{
			$sql= "SELECT id,subject,date_orig FROM ".AGILE_DB_PREFIX."ticket WHERE site_id = '".DEFAULT_SITE."'
					   AND ( account_id = '{$account_id}' OR email = '{$email}' ) 
					   AND id != '{$id}'
					   ORDER BY subject,date_orig"; 
		} else {
			$sql= "SELECT id,subject,date_orig FROM ".AGILE_DB_PREFIX."ticket WHERE site_id = '".DEFAULT_SITE."'
					   AND email = '{$email}' 
					   AND id != '{$id}'
					   ORDER BY subject,date_orig";	 
		}
		$result = $db->Execute($sql);

		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('ticket.inc.php','merge_list', $db->ErrorMsg());
		} else {
			$return = '<select id="ticket_merge" name="ticket_merge" onChange="ticket_merger()">';
			$return .= '<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;-------------- '.$C_translate->translate('merge', 'ticket','').' --------------&nbsp;&nbsp;&nbsp;&nbsp;</option>';
			$i = 0;
			while (!$result->EOF)
			{
				$subject = substr($result->fields["subject"], 0, 26);
				if(strlen($result->fields["subject"]) > 26)
				$subject .= '..';

				$return .= '<option value="' . $result->fields["id"] . '"';
				$return .= '>' . ereg_replace('%','', date(DEFAULT_DATE_FORMAT, $result->fields["date_orig"])) . ' | ' . $subject . '</option> ';
				$i++;
				$result->MoveNext();
			}
			if($i==0)
			$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
			$return .= '</select>';

			$return .= '<script language=javascript>
    						function ticket_merger() 
    						{
    							var from = "'.$id.'";
    							var to   = document.getElementById("ticket_merge").value; 
    							var msg  = "'.$C_translate->translate("merge_confirm","ticket","").'";
    							if(to != 0) {
									temp = window.confirm(msg);
									window.status=(temp)?\'confirm:true\':\'confirm:false\';
									
									if(temp == false) { return; } else {
										document.location = "?_page=ticket:view&id="+from+"&do[]=ticket:merge&merge_id="+to;
									}
								}
						    							
    						}
    						</script>';
			if($i > 0)
			echo $return;
		}
	}

	##############################
	## Merge the ticket			##
	##############################
	function merge($VAR)
	{
		$db 	= &DB();
		$sql 	= "SELECT * FROM ".AGILE_DB_PREFIX."ticket
						WHERE site_id = '".DEFAULT_SITE."' 
						AND id = '{$VAR['id']}'";	
		$from 	= $db->Execute($sql);

		$db 	= &DB();
		$sql 	= "SELECT * FROM ".AGILE_DB_PREFIX."ticket
						WHERE site_id = '".DEFAULT_SITE."' 
						AND id = '{$VAR['merge_id']}'";	
		$to 	= $db->Execute($sql);

		### Validate both exist
		if($from->RecordCount() == 0 || $to->RecordCount() == 0)
		return false;

		### Update TO ticket record
		$sql 	= "UPDATE ".AGILE_DB_PREFIX."ticket_message SET
						ticket_id	= ".$db->qstr($VAR['id'])."
						WHERE 
						site_id = '".DEFAULT_SITE."' 
						AND 
						ticket_id = ".$db->qstr($VAR['merge_id']);	
		$rs 	= $db->Execute($sql);

		### Create new ticket_message record for the ticket being removed:
		$id     = $db->GenID(AGILE_DB_PREFIX .  'ticket_message_id');
		$sql 	= "INSERT INTO ".AGILE_DB_PREFIX."ticket_message SET
						id			= '{$id}',
						site_id		= '".DEFAULT_SITE."',
						ticket_id	= '{$VAR['id']}',
						staff_id	= '".SESS_ACCOUNT."',
						date_orig	= '{$to->fields['date_orig']}',
						message		= ".$db->qstr($to->fields['body']);	
		$rs 	= $db->Execute($sql);

		### Delete the old ticket...
		$sql 	= "DELETE FROM ".AGILE_DB_PREFIX."ticket WHERE
						site_id = '".DEFAULT_SITE."' 
						AND 
						id = '{$VAR['merge_id']}'";	
		$rs 	= $db->Execute($sql);

	}

	##############################
	## Verify Pending Ticket    ##
	##############################
	function pending_verify($VAR)
	{
		global $C_auth, $smarty;

		### Get user account details:
		$db = &DB();
		$sql    = 'SELECT email FROM ' . AGILE_DB_PREFIX . 'account WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id			= ' . $db->qstr(SESS_ACCOUNT);
		$account = $db->Execute($sql);

		### Get ticket details
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id			= ' . $db->qstr(@$VAR['id']);
		$ticket = $db->Execute($sql);
		$department_id = $ticket->fields['department_id'];

		if($ticket->fields['status'] != '3') return false;

		### Get the available groups:
		$sql    = 'SELECT group_id FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id			= ' . $db->qstr($department_id);
		$dept = $db->Execute($sql);
		$groups = unserialize($dept->fields['group_id']);

		### Check if this account is authed for the ticket department
		$this->auth = false;
		for($i=0; $i<count($groups); $i++)
		{
			if($C_auth->auth_group_by_account_id(SESS_ACCOUNT, $groups[$i])) {
				$this->auth = true;
			}
		}

		if($this->auth)
		{
			# Update ticket details
			$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'ticket SET
	            			status      = ' . $db->qstr(0) . ',
	            			account_id  = ' . $db->qstr(SESS_ACCOUNT) . '
	            			WHERE
	                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
	                        id			= ' . $db->qstr(@$VAR['id']);
			$rs = $db->Execute($sql);


			### Get any staff members who should be mailed
			$sql    = 'SELECT id,account_id,department_avail FROM ' . AGILE_DB_PREFIX . 'staff
	                       WHERE
	                       site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
	                       notify_new  = ' . $db->qstr("1");
			$result = $db->Execute($sql);

			if($result->RecordCount() > 0)
			{
				while(!$result->EOF)
				{
					@$avail = unserialize($result->fields['department_avail']);
					for($i=0; $i<count($avail); $i++)
					{
						if ($avail[$i] == $department_id)
						{
							###################################################################
							### Mail staff members the new_ticket email template
							global $VAR;
							$VAR['ticket_priority'] = $ticket->fields['priority'];
							$VAR['ticket_subject'] 	= $ticket->fields['subject'];
							$VAR['ticket_body'] 	= $ticket->fields['body'];
							require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
							$my = new email_template;
							$my->send('ticket_user_add_staff', $result->fields['account_id'], $VAR['id'], $avail[$i], '');
							$i = count($avail);
						}
					}
					$result->MoveNext();
				}
			}

			# Redirect to new page
			$smart = "<script language=javascript>document.location = '?_page=ticket:user_view&id={$VAR['id']}';</script>";
			$smarty->assign('pending_status', $smart);
		}
		else
		{
			$smart = $C_translate->translate('user_pending_verify','ticket','');
			$smarty->assign('pending_status', $smart);
		}
	}


	##############################
	##  	Overview		    ##
	##############################
	function piping($VAR)
	{
		# get ticket departments w/piping enabled
		$db = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE site_id = ' . $db->qstr(DEFAULT_SITE) . ' AND piping = 1';
		$result = $db->Execute($sql);
		if($result && $result->RecordCount()) {
			while(!$result->EOF)
			{
				$id = $result->fields['id'];
				$email = $result->fields['piping_setup_email_id'];

				# Get all messages from this account:
				include_once ( PATH_CORE . 'email_piping.inc.php' );
				$email_piping = new email_piping($email);
				if(!empty($email_piping->results) && is_array($email_piping->results))
				for ($i=0; $i<count($email_piping->results); $i++)
				$this->piping_add_ticket( $email_piping->results[$i], $id);
				$result->MoveNext();
			}
		}
	}


	##############################
	##  	Overview		    ##
	##############################
	function piping_add_ticket($arr,$department_id)
	{
		# Check values
		if (empty($arr['uniqueId']) || empty($arr['body']))
		return false;

		# Check if duplicate:
		$db 	= &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
                        site_id     		= ' . $db->qstr(DEFAULT_SITE) . ' AND
                        piping_unique_id	= ' . $db->qstr($arr['uniqueId']);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		return false;

		# Determine user's account id:
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'account WHERE
                        site_id     		= ' . $db->qstr(DEFAULT_SITE) . ' AND
                        email				= ' . $db->qstr($arr['from']);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		$account_id = 0;
		else
		$account_id = $result->fields['id'];


		### Do any authentication required:
		$C_auth = new CORE_auth(false);
		$status = 3;
		$this->auth = false;

		### Get available groups for this department:
		$sql    = 'SELECT group_id FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
                        site_id     		= ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id					= ' . $db->qstr($department_id);
		$result = $db->Execute($sql);
		@$groups = unserialize($result->fields['group_id']);

		if($account_id == 0)
		{
			### Unknown account, is this this department authorized for the All Users group id (0)
			$this->auth = true;
			for($i=0; $i<count($groups); $i++)
			{
				if($groups[$i] == '0') {
					$status = 0;
					$i=count($groups);
				}
			}
		}
		else
		{
			### Known account, loop through available groups and check if account is authorized
			for($i=0; $i<count($groups); $i++)
			{
				if($C_auth->auth_group_by_account_id($account_id, $groups[$i])) {
					$status = 0;
					$i=count($groups);
					$this->auth = true;
				}
			}
		}

		if($this->auth)
		{
			# Create the new ticket record:
			$ticket_id 	= $db->GenID(AGILE_DB_PREFIX . 'ticket_id');
			$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'ticket
	            			SET
	            			id     			= ' . $db->qstr($ticket_id) . ',
	                        site_id    		= ' . $db->qstr(DEFAULT_SITE) . ',
	                        date_orig  		= ' . $db->qstr(time()) . ',
	                        date_last  		= ' . $db->qstr(time()) . ',
							account_id 		= ' . $db->qstr($account_id) . ',                            		
	                        department_id	= ' . $db->qstr($department_id) . ',
	                        status    		= ' . $db->qstr($status) . ',
	                        priority   		= ' . $db->qstr( '0' ) . ',    	
	                        subject   		= ' . $db->qstr( $arr['subject'] ) . ',
	                        body	   		= ' . $db->qstr( @$arr['body'] ) . ',
	                        last_reply   	= ' . $db->qstr( '0' ) . ',
	                        piping_unique_id= ' . $db->qstr( $arr['uniqueId'] ) . ',	    		 
	                        email			= ' . $db->qstr($arr['from']);
			$result = $db->Execute($sql);

			// insert any attachments
			if(!empty($arr['attach']) && is_array($arr['attach'])) {
				foreach($arr['attach'] as $attach) {
					@$data = file_get_contents($attach['tmp']);
					if(!empty($data)) {
						// get file size
						$size=filesize($attach['tmp']);
						$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
						$size=round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];

						// insert record
						$fields=Array('ticket_id'=>$ticket_id, 'ticket_message_id'=>0, 'name'=>$attach['file'], 'size'=>$size, 'type'=> $attach['type'], 'content'=> $data);
						$db->Execute($sql=sqlInsert($db,"ticket_attachment",$fields));
					}
					@$u=unlink($attach['tmp']);
				}
			}
		}

		### Do any emails...
		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;

		if($account_id)
		$ticket_account_id = $account_id;
		else
		$ticket_account_id = trim($arr['from']);


		### Send the user ticket confirmation message:
		global $VAR;
		$VAR['ticket_subject'] 	= $arr['subject'];
		$VAR['email'] = trim($arr['from']);
		$VAR['key'] = $this->key($arr['from']);

		if($status == 0 && $this->auth)
		{
			### Mail the user the new_ticket email template
			$my->send('ticket_piping_add_user', $ticket_account_id, $ticket_id, '', '');
		}
		elseif ($status == 3 && $this->auth)
		{
			### Mail the user the new_ticket email template (pending)
			$my->send('ticket_piping_add_user_pending', $ticket_account_id, $ticket_id, '', '');
		}
		elseif (!$this->auth)
		{
			### Mail the user the new_ticket email template (unauthorized)
			$my->send('ticket_piping_add_user_unauth', $ticket_account_id, '', '', '');
		}

		if($status == 0)
		{
			### Get any staff members who should be mailed
			$dba     = &DB();
			$sql    = 'SELECT id,account_id,department_avail FROM ' . AGILE_DB_PREFIX . 'staff
	                       WHERE
	                       site_id     = ' . $dba->qstr(DEFAULT_SITE) . ' AND
	                       notify_new  = ' . $dba->qstr("1");
			$result = $dba->Execute($sql);

			if($result->RecordCount() > 0)
			{
				while(!$result->EOF)
				{
					@$avail = unserialize($result->fields['department_avail']);
					for($i=0; $i<count($avail); $i++)
					{
						if ($avail[$i] == $department_id)
						{
							###################################################################
							### Mail staff members the new_ticket email template

							global $VAR;
							$VAR['ticket_priority'] = 'Normal (e-mail)';
							$VAR['ticket_subject'] 	= $arr['subject'];
							$VAR['ticket_body'] 	= $arr['body'];

							$my = new email_template;
							$my->send('ticket_staff_add', $result->fields['account_id'], $ticket_id, $avail[$i], '');
							$i = count($avail);
						}
					}
					$result->MoveNext();
				}
			}
		}
	}



	##############################
	##  	Overview		    ##
	##############################
	function overview($VAR)
	{
		# get the authorized ticket departments:
		$dbs    = &DB();
		$sql    = 'SELECT id, department_avail FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$result = $dbs->Execute($sql);

		$cats = unserialize($result->fields['department_avail']);

		for($i=0; $i<count($cats); $i++)
		{
			# Get the category details
			$sql    = 'SELECT id,name FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
	                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                        id  = ' . $dbs->qstr($cats[$i]);
			$cat = $dbs->Execute($sql);

			$smart[$i]['id'] = $cat->fields['id'];
			$smart[$i]['name'] = $cat->fields['name'];

			if($class_name)
			{
				$smart[$i]['class'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['class'] = 'row2';
				$class_name = TRUE;
			}


			# Get New
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     		= ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       date_orig  		= date_last AND
	                       department_id  	= ' . $dbs->qstr($cats[$i]) . ' AND
	                       ( last_reply = 0 OR last_reply IS NULL ) AND
	                       ( status	 = 0 OR status IS NULL )';
			$rs = $dbs->Execute($sql);
			$smart[$i]['new'] = $rs->RecordCount();


			# Get Awaiting Reply
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     		= ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       date_orig  		!= date_last AND
	                       department_id  	= ' . $dbs->qstr($cats[$i]) . ' AND
	                       last_reply		= 2 AND
	                       status	  		= 0';
			$rs = $dbs->Execute($sql);
			$smart[$i]['waiting'] = $rs->RecordCount();


			# Get Awaiting Customer
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     		= ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       date_orig  		!= date_last AND
	                       department_id  	= ' . $dbs->qstr($cats[$i]) . ' AND
	                       last_reply		= 1 AND
	                       status	  		= 0';
			$rs = $dbs->Execute($sql);
			$smart[$i]['customer'] = $rs->RecordCount();


			# On Hold
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       department_id  = ' . $dbs->qstr($cats[$i]) . ' AND
	                       status	  = ' . $dbs->qstr( '1' );
			$rs = $dbs->Execute($sql);
			$smart[$i]['hold'] = $rs->RecordCount();

			# PENDING
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       department_id  = ' . $dbs->qstr($cats[$i]) . ' AND
	                       status	  = ' . $dbs->qstr( '3' );
			$rs = $dbs->Execute($sql);
			$smart[$i]['pending'] = $rs->RecordCount();

			# Get Resolved
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
	                       site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                       department_id  = ' . $dbs->qstr($cats[$i]) . ' AND
	                       status	  = ' . $dbs->qstr( '2' );
			$rs = $dbs->Execute($sql);
			$smart[$i]['resolved'] = $rs->RecordCount();


		}
		global $smarty;
		$smarty->assign('overview', $smart);
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->construct();
		global $C_vars,$C_list,$C_debug;

		### Strip Slashes:
		global $C_vars;
		$C_vars->strip_slashes_all();

		### Get the current staff id:
		$dbs    = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$result = $dbs->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### ERROR: this account does not have a staff id
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_no_account','ticket',''));
			$C_vars->strip_slashes_all();
			return;
		}
		else
		{
			$staff_id = $result->fields['id'];
			$VAR['ticket_staff_id'] = $staff_id;
		}

		### Validate either the user account_id or email has been provided.
		include_once(PATH_CORE . 'validate.inc.php' );
		$C_validate = new CORE_validate;
		$validate = false;

		if(empty($VAR['ticket_account_id']) && empty($VAR['ticket_email'])) {
			$validate = false;
		} elseif($C_validate->validate_email(@$VAR['ticket_email'], false) ) {
			$validate = true;
		}

		### Set the e-mail from the account for this ticket if none provided:
		if(!empty($VAR['ticket_account_id']) && empty($VAR['ticket_email'])) {
			$sql    = 'SELECT id,email FROM ' . AGILE_DB_PREFIX . 'account WHERE
	                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
	                        id		    = ' . $dbs->qstr(@$VAR['ticket_account_id']);
			$account = $dbs->Execute($sql);
			if($account->RecordCount() == 0)
			{
				$validate = false;
			} else {
				$this->email = trim($account->fields['email']);
				$VAR['ticket_email'] = trim($account->fields['email']);
				$validate = true;
			}
		}

		### Everything pass inspection?
		if( !$validate )
		{
			$C_debug->alert("A valid user account or e-mail address must be provided!");
			define('FORCE_PAGE', "ticket:add");
			return;
		}

		### Set times:
		$VAR['ticket_date_orig'] = $C_list->date_time(time());
		$VAR['ticket_date_last'] = $C_list->date_time(time());
		$VAR['ticket_last_reply'] = "0";
		$VAR['ticket_status'] = "0";

		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$this->record_id = $db->add($VAR, $this, $type);

		if($this->record_id)
		{
			###################################################################
			### Insert the static vars...

			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;
			$static_var->add($VAR, 'ticket', $this->record_id);

			###################################################################
			### Mail the user the new_ticket email template

			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
			$my = new email_template;

			if(empty($this->email)) {
				global $VAR;
				$VAR['key'] = $this->key($VAR['ticket_email']);
				$VAR['email'] = trim($VAR['ticket_email']);
				$my->send('ticket_staff_add_user', $VAR['ticket_email'], $this->record_id, '', '');
			} else {
				global $VAR;
				$VAR['key'] = $this->key($this->email);
				$VAR['email'] = trim($this->email);
				$my->send('ticket_staff_add_user', $VAR['ticket_account_id'], $this->record_id, '', '');
			}

			###################################################################
			### Get any staff members who should be mailed

			$dba     = &DB();
			$sql    = 'SELECT id,account_id,department_avail FROM ' . AGILE_DB_PREFIX . 'staff
                            WHERE
                            site_id     = ' . $dba->qstr(DEFAULT_SITE) . ' AND
                            notify_new  = ' . $dba->qstr("1");
			$result = $dba->Execute($sql);

			if($result->RecordCount() > 0)
			{
				while(!$result->EOF)
				{
					@$avail = unserialize($result->fields['department_avail']);
					for($i=0; $i<count($avail); $i++)
					{
						if ($avail[$i] == $VAR['ticket_department_id'])
						{
							###################################################################
							### Mail staff members the new_ticket email template
							$my = new email_template;
							$my->send('ticket_staff_add', $result->fields['account_id'], $this->record_id, $avail[$i], '');
							$i = count($avail);
						}
					}
					$result->MoveNext();
				}
			}
		}
	}





	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{
		$this->construct();
		### Get the departments this staff member is authorized for:

		$dbs    = &DB();
		$sql    = 'SELECT id,department_avail,signature FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$result = $dbs->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### ERROR: this account does not have a staff id
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_no_account','ticket',''));
			return;

		}
		else
		{
			$staff_id   = $result->fields['id'];
			@$avail     = unserialize($result->fields['department_avail']);
			global $smarty;
			$smarty->assign('signature',$result->fields['signature']);

			### Loop through the records to define the custom SQL:
			for($i=0; $i<count($avail); $i++)
			{
				$this->custom_EXP[] = Array('field' => 'department_id', 'value' => $avail[$i]);
			}
		}


		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = &DB();

		# set the field list for this method:
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
		        	  ".AGILE_DB_PREFIX."$this->table
					  WHERE					
		        	  $id_list
		        	  AND site_id = " . $db->qstr(DEFAULT_SITE) . "
		        	  ORDER BY $this->order_by ";

			$result = $db->Execute($q);

			# error reporting
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('database.inc.php','view', $db->ErrorMsg());
				return;
			}


			# put the results into a smarty accessable array
			$i=0;
			$class_name = TRUE;
			$staffs='';
			while (!$result->EOF)
			{
				$smart[$i] = $result->fields;
				$smart[$i]["i"] = $i;

				// get any attachments for ticket
				$attach = $db->Execute(sqlSelect($db,"ticket_attachment","id,name,size,type","ticket_id={$result->fields['id']}"));
				if($attach && $attach->RecordCount()) {
					while(!$attach->EOF) {
						$smart[$i]['attachments'][] = $attach->fields;
						$attach->MoveNext();
					}
				}

				### Get any replies...
				$dba     = &DB();
				$p = AGILE_DB_PREFIX;
				$s = DEFAULT_SITE;
				$sql    = " SELECT DISTINCT
	                    			A.* 
                    			FROM 
	                    			{$p}ticket_message AS A 
                    			WHERE                   			
                    				A.site_id = $s  
                    			AND
                                	A.ticket_id   = {$result->fields['id']}                                
                                ORDER BY 
                                	A.date_orig";

				$replys = $dba->Execute($sql);
				$ii=0;
				while (!$replys->EOF)
				{
					# Get the staff name:
					if( $replys->fields['staff_id'] > 0 )
					{
						$dbm = new CORE_database();
						$rss = $db->Execute(
						$sql = $dbm->sql_select(
						'staff',		// tables (for one table, delete array)
						'nickname',	// fields (for one field, delete array)
						"id = {$replys->fields['staff_id']} ", // conditions
						"", // order
						$db
						)
						);
						$replys->fields['staff_nickname'] = $rss->fields['nickname'];
					}
					else
					{
						if(!empty($result->fields['email'])) {
							$replys->fields['user_name'] = $result->fields['email'];
						} elseif(!empty($result->fields['account_id'])) {
							$db = &DB();
							$dbm = new CORE_database();
							$rss = $db->Execute(
							$dbm->sql_select(
							'account',		// tables (for one table, delete array)
							'first_name,last_name',	// fields (for one field, delete array)
							"id = {$result->fields['account_id']}", // conditions
							"", // order
							$db
							)
							);
							$replys->fields['user_name'] = $rss->fields['first_name'].' ' .$rss->fields['last_name'];
						} else {
							$replys->fields['user_name'] = 'User';
						}
					}

					$ii++;
					if($ii >= $replys->RecordCount())
					$replys->fields['last'] = true;
					else
					$replys->fields['last'] = false;

					$reply[] = $replys->fields;
					$replys->MoveNext();

				}
				$smart[$i]["reply"] = $reply;


				### Get the static vars:
				require_once(PATH_CORE   . 'static_var.inc.php');
				$static_var = new CORE_static_var;
				$arr = $static_var->view_form($this->module, $result->fields['id']);
				if(gettype($arr) == 'array')
				{
					$smart[$i]["static_var"] = $arr;
				}
				
				// Get the user authentication details
				if($result->fields['account_id'] > 0) {
					 
					// get services
					$sql = "SELECT id,from_unixtime(date_orig,'%m-%d-%Y') as date_orig,active,sku FROM {$p}service 
							WHERE account_id={$result->fields['account_id']} AND site_id = ".DEFAULT_SITE." 
							GROUP BY sku,active
							ORDER BY date_orig";
					$authsrvc = $db->Execute($sql);
					if($authsrvc && $authsrvc->RecordCount()) {
						while(!$authsrvc->EOF) {
							$smart[$i]['authsrvc'][] = $authsrvc->fields;
							$authsrvc->MoveNext();
						}
					}
					
					// get groups 
					$sql = "SELECT DISTINCT B.id,B.name,A.active,from_unixtime(A.date_orig,'%m-%d-%Y') as date_orig FROM {$p}account_group as A
							JOIN {$p}group as B ON (B.id=A.group_id AND B.site_id=".DEFAULT_SITE.")
							WHERE A.account_id = {$result->fields['account_id']} AND A.site_id = ".DEFAULT_SITE." 
							AND A.group_id>1001";
					$authgrp = $db->Execute($sql);
					if($authgrp && $authgrp->RecordCount()) {
						while(!$authgrp->EOF) {
							$smart[$i]['authgrp'][] = $authgrp->fields;
							$authgrp->MoveNext();
						}
					}	
					
					// get ordered products 
					$sql = "SELECT count(*) as qty, B.id,from_unixtime(B.date_orig,'%m-%d-%Y') as dateorg,A.sku FROM {$p}invoice_item as A
							JOIN {$p}invoice as B ON (B.id=A.invoice_id AND B.site_id=".DEFAULT_SITE." AND billing_status=1 AND process_status=1 )
							WHERE A.account_id = {$result->fields['account_id']} AND A.site_id = ".DEFAULT_SITE."						
							GROUP BY sku,dateorg";
					$authsku = $db->Execute($sql);
					if($authsku && $authsku->RecordCount()) {
						while(!$authsku->EOF) {
							$smart[$i]['authsku'][] = $authsku->fields;
							$authsku->MoveNext();
						}
					}	 	
				}

				$i++;
				$result->MoveNext();
			}

			# get the result count:
			$results = $i;

			### No results:
			if($i == 0)
			{
				global $C_debug;
				$C_debug->error("CORE:database.inc.php", "view()", "The selected record does not
                    exist any longer, or your account is not authorized to view it");
				return;
			}

			# define the results
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
		$this->construct();  
		
		$db =& DB();
		# get current department
		$rs = $db->Execute(sqlSelect($db,"ticket","ticket_department_id","id=::".$VAR['ticket_id']."::"));
		@$old_ticket_department_id = $rs->fields[0];

		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
  
		# if the ticket was moved to a new department, send email notice to staff members
		if ($old_ticket_department_id != $VAR['ticket_department_id']) 
		$this->send_staff_emails($VAR['ticket_id'], SESS_ACCOUNT, $old_ticket_department_id, 'change');	 
	}
 
	function send_staff_emails($id, $staff_account_id = 0, $old_department_id = 0, $notify_type='new' )
	{
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"ticket","*","id=::$id::")); 
		require_once(PATH_MODULES.'email_template/email_template.inc.php');
 
		$sql    = 'SELECT id,account_id,department_avail 
					   FROM '.AGILE_DB_PREFIX.'staff  
                       WHERE  
                       notify_'.$notify_type.'=1 AND 
                       account_id != '.$staff_account_id.' AND
                       site_id = '.DEFAULT_SITE;
		$result = $db->Execute($sql);

		if($result->RecordCount() > 0)
		{
			while(!$result->EOF)
			{
				$bSend = false;

				@$avail = unserialize($result->fields['department_avail']);
				for($i=0; $i<count($avail); $i++)
				{
					if ($avail[$i] == $rs->fields['department_id']) $bSend = true;
						  
					if($old_department_id && $result->fields['department_id'] == $old_department_id) {
						$bSend = false;
						$i = count($avail);
					}

					if($bSend) { 
						### Mail staff members the new_ticket email template 
						global $VAR, $C_translate;
						$VAR['ticket_priority'] = 'Normal (e-mail)';
						$VAR['ticket_subject'] 	= $rs->fields['subject'];
						$VAR['ticket_body'] 	= $rs->fields['body'];
						if($rs->fields['status'] == 0) {
							$VAR['ticket_status'] = $C_translate->translate('status_open','ticket','');
						} else if($rs->fields['status'] == 1) {
							$VAR['ticket_status'] = $C_translate->translate('status_hold','ticket','');
						} else if($rs->fields['status'] == 2) {
							$VAR['ticket_status'] = $C_translate->translate('status_close','ticket','');
						} else if($rs->fields['status'] == 3) {
							$VAR['ticket_status'] = $C_translate->translate('status_pending','ticket','');
						}
						# Find other bodies for ticket
						$rs1 = $db->Execute(sqlSelect($db,"ticket_message","*","ticket_id=::".$id."::"));
						if($rs1 && $rs1->RecordCount()) {
							while(!$rs1->EOF) {
								if($rs1->fields['date_orig'] >= $rs->fields['date_orig']) {
									$VAR['ticket_body'] = $rs1->fields['message'];
								}
								$rs1->MoveNext();
							}
						}
						$my = new email_template;
						$my->send('ticket_staff_add', $result->fields['account_id'], $id, $avail[$i], '');
						$i = count($avail); 
					}
				}
				$result->MoveNext();
			}
		}
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{
		$this->construct();
		$this->associated_DELETE[] = Array( 'table' => 'ticket_message', 'field' => 'ticket_id');
		$this->associated_DELETE[] = Array( 'table' => 'ticket_attachment', 'field' => 'ticket_id');

		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->construct();
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
		$this->construct();
		
		### Get the departments this staff member is authorized for:

		$dbs    = &DB();
		$sql    = 'SELECT id, department_avail FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$result = $dbs->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### ERROR: this account does not have a staff id
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_no_account','ticket',''));
			return;

		}
		else
		{
			$staff_id   = $result->fields['id'];
			@$avail     = unserialize($result->fields['department_avail']);
		}

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

							$where_list .= " WHERE ".AGILE_DB_PREFIX."ticket.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
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
											$where_list .= " WHERE ".AGILE_DB_PREFIX."ticket.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
											$where_list .= " AND ".AGILE_DB_PREFIX."ticket.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
									}
								}
							}
							else
							{
								$where_list .= " WHERE ".AGILE_DB_PREFIX."ticket.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
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

							$where_list .= " AND ".AGILE_DB_PREFIX."ticket.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
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

										$where_list .= " AND ".AGILE_DB_PREFIX."ticket.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
									}
								}
							}
							else
							{
								$where_list .=  " AND ".AGILE_DB_PREFIX."ticket.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
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

		# Dept ID;
		if(!empty($staff_id) && count($avail) > 0)
		{
			$where_list .= ' ( ';

			### Loop through the records to define the custom SQL:
			for($i=0; $i<count($avail); $i++)
			{
				if($i > 0)
				$where_list .= " OR ";
				$where_list .= " {$pre}ticket.department_id = ".$db->qstr($avail[$i]);
			}

			$where_list .= ' ) AND ';
		}


		$q = "SELECT DISTINCT ".AGILE_DB_PREFIX."ticket.id FROM ".AGILE_DB_PREFIX."ticket ";
		$q_save = "SELECT DISTINCT %%fieldList%% FROM ".AGILE_DB_PREFIX."ticket ";


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
		$q .= $join_list . $where_list ." ".AGILE_DB_PREFIX."ticket.site_id = " . $db->qstr(DEFAULT_SITE);
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
			$C_debug->error('ticket.inc.php','search', $db->ErrorMsg());
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
		$this->construct();
		### Get the departments this staff member is authorized for:

		$dbs    = &DB();
		$sql    = 'SELECT id, department_avail FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                       site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                       account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$result = $dbs->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### ERROR: this account does not have a staff id
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_no_account','ticket',''));
			return;

		}
		else
		{
			$staff_id   = $result->fields['id'];
			@$avail     = unserialize($result->fields['department_avail']);
		}


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
				$field_list .= AGILE_DB_PREFIX . "ticket" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "ticket" . "." . $value;

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
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'ticket.'.$VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'ticket.'.$this->order_by;
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
		$q .= " ".AGILE_DB_PREFIX . "ticket."."site_id = " . $db->qstr(DEFAULT_SITE);
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
			$C_debug->error('ticket.inc.php','search_show', $db->ErrorMsg());

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
	##		STATIC VARS         ##
	##############################

	function static_var($VAR)
	{
		$this->construct();
		global $smarty;

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;

		if(ereg('search', $VAR['_page']))
		$arr = $static_var->generate_form($this->module, 'user_add', 'search');
		else
		$arr = $static_var->generate_form($this->module, 'user_add', 'update');

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



	##############################
	##      REPLY          	    ##
	##############################

	function reply($VAR)
	{
		$this->construct();
		global $smarty;
		 
		if(!isset($VAR['ticket_id']) || $VAR['ticket_id'] == '')
		### ERROR: ID must be set!!
		{
			global $C_debug, $C_translate, $C_vars;
			$C_debug->alert($C_translate->translate('user_ticket_nonexist','ticket',''));
			$C_vars->strip_slashes_all();
			return false;
		}

		if(!isset($VAR['ticket_reply']) || $VAR['ticket_reply'] == '')
		### ERROR: Ticket Reply must be set!!
		{
			global $C_debug, $C_translate, $C_vars;
			$C_debug->alert($C_translate->translate('staff_response_required','ticket',''));
			$C_vars->strip_slashes_all();
			return;
		}


		### Get the ticket record
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id          = ' . $db->qstr($VAR['ticket_id']);
		$ticket = $db->Execute($sql);
		if($ticket->RecordCount() == 0)
		{
			### ERROR: ticket doesn't exist!
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_ticket_nonexist','ticket',''));
			return false;
		}

		$status           = $ticket->fields['status'];
		$department_id    = $ticket->fields['department_id'];

		### Get the staff ID:
		$dbs    = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'staff WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $dbs->qstr(SESS_ACCOUNT);
		$staff = $dbs->Execute($sql);
		$staff_id   = $staff->fields['id'];

		### Update the ticket status:
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'ticket SET
                        date_last   = ' . $dbs->qstr(time()) .',
                        last_reply  = ' . $dbs->qstr( '1' ) .',
                        staff_id    = ' . $dbs->qstr($staff_id) .'
                        WHERE
                        site_id     = ' . $dbs->qstr(DEFAULT_SITE) . ' AND 
                        id          = ' . $dbs->qstr($VAR['ticket_id']);
		$update = $dbs->Execute($sql);

		### Add the message Record to the DB
		$db     = &DB();
		$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'ticket_message_id');
		$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'ticket_message SET
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ',
                        staff_id    = ' . $db->qstr($staff_id) . ',
                        id          = ' . $db->qstr($id) . ',
                        ticket_id   = ' . $db->qstr($VAR['ticket_id']) . ',
                        date_orig   = ' . $db->qstr(time()) . ',
                        message     = ' . $db->qstr(htmlspecialchars($VAR['ticket_reply']), get_magic_quotes_gpc()) ; 
		$result = $db->Execute($sql);


		if(!empty($VAR['enable_user_notice']))
		{
			###################################################################
			### Mail the user the new_ticket email template

			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
			$my = new email_template;

			if(!empty($ticket->fields["email"]))
			{
				global $VAR;
				$VAR['key'] = $this->key($ticket->fields["email"]);
				$VAR['email'] = trim($ticket->fields["email"]);
				$VAR['message'] = trim(htmlspecialchars($VAR['ticket_reply']));
				$my->send('ticket_staff_update_user', $ticket->fields["email"], $VAR['ticket_id'], '', '');
			}
			else
			{
				$sql    = 'SELECT email FROM ' . AGILE_DB_PREFIX . 'account WHERE
		                       site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
		                       id          = ' . $db->qstr($ticket->fields['account_id']);
				$account = $db->Execute($sql);
				global $VAR;
				$VAR['key'] = $this->key($ticket->fields["email"]);
				$VAR['email'] = trim($account->fields['email']);
				$VAR['message'] = trim(htmlspecialchars($VAR['ticket_reply']));
				$my->send('ticket_staff_update_user', $ticket->fields["account_id"], $VAR['ticket_id'], '', '');
			}

			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_response_success','ticket',''));
		} 
		
		# send the reply to the staff members 
		$this->send_staff_emails($VAR['ticket_id'], SESS_ACCOUNT, 0, 'change');
	}




	##############################
	##   USER  LIST 		    ##
	##############################

	function user_list($VAR)
	{
		$this->construct();
		global $C_debug, $C_translate, $smarty;

		if(SESS_LOGGED == false)  {
			if(!$this->is_key_match($VAR)) {
				$C_debug->alert($C_translate->translate('login_required', '', ''));
				return false;
			}
		}

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket WHERE site_id     = ' . $db->qstr(DEFAULT_SITE);

		if(SESS_LOGGED == true) {
			$sql .= ' AND (
	                        account_id  = ' . $db->qstr(SESS_ACCOUNT) . ' 
	                    ) ';
		}

		if (SESS_LOGGED == true && !empty($VAR['email']) )
		$sql .= ' OR ';
		else if (SESS_LOGGED == false && !empty($VAR['email']) )
		$sql .= ' AND ';

		if ( !empty($VAR['email']) ) {
			$sql .= ' (
	                        email = ' . $db->qstr(@$VAR['email']) . '
	                        	AND
	                        email != ' . $db->qstr('') . '
	                        	AND
	                        email IS NOT NULL
                        )
                        ORDER BY status, date_last DESC';
		}

		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### Or if no results:
			$smarty->assign('ticket_results',		false);
			return false;
		}
		else
		{
			while (!$result->EOF)
			{
				$smart[] = $result->fields;
				$result->MoveNext();
			}

			## Set everything as a smarty array, and return:
			$smarty->assign('ticket_results',		$smart);
			return true;
		}
	}







	##############################
	##   USER  UPDATE   	    ##
	##############################

	function user_update($VAR)
	{
		$this->construct();
		global $smarty;

		if(SESS_LOGGED == false)  {
			if(!$this->is_key_match($VAR)) {
				$smarty->assign('ticket',		false);
				return false;
			}
		}

		if(!isset($VAR['id']) || $VAR['id'] == '')
		### ERROR: ID must be set!!
		{
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_ticket_nonexist','ticket',''));
		}

		### Check it this is a request to close the ticket
		if(isset($VAR['ticket_status']))
		{
			### Close the ticket
			$db    = &DB();
			$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'ticket
                		SET
                        status      = ' . $db->qstr('2') .',
                        date_last   = ' . $db->qstr(time()) .'
                        WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' 
                        AND
                        (
                        account_id  = ' . $db->qstr(SESS_ACCOUNT) . ' OR
                        email		= ' . $db->qstr(@$VAR["email"]) . '
                        )
                        AND
                        id          = ' . $db->qstr($VAR['id']);
			$ticket = $db->Execute($sql);
			return;
		}


		if(!isset($VAR['ticket_reply']) || $VAR['ticket_reply'] == '')
		### ERROR: Ticket Reply must be set!!
		{
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('staff_response_required','ticket',''));
		}

		### Get the ticket record
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket
            			WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' 
                        AND
                        (
                        account_id  = ' . $db->qstr(SESS_ACCOUNT) . ' OR
                        email		= ' . $db->qstr(@$VAR["email"]) . '
                        )
                        AND
                        id          = ' . $db->qstr($VAR['id']);
		$ticket = $db->Execute($sql);
		if($ticket->RecordCount() == 0)
		{
			### ERROR: ticket doesn't exist!
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_ticket_nonexist','ticket',''));
			return false;
		}

		$status           = $ticket->fields['status'];
		$department_id    = $ticket->fields['department_id'];
		$authorized       = false;


		if($status == '2')
		{
			if(SESS_LOGGED == true)
			{
				### Check if user is auth for this department still!
				$db     = &DB();
				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
	                            id          = ' . $db->qstr($department_id) . ' AND
	                            site_id     = ' . $db->qstr(DEFAULT_SITE)   . ' AND
	                            status      = ' . $db->qstr('1');
				$result = $db->Execute($sql);
				if($result->RecordCount() == 0)
				{
					### ERROR: not authorized to reopen this ticket!
					global $C_debug, $C_translate;
					$C_debug->alert($C_translate->translate('user_not_auth_reopen','ticket',''));
					return false;
				}
				$i = 0;
				$arr = unserialize($result->fields['group_id']);
				global $C_auth;
				for($i=0; $i<count($arr); $i++)
				{
					if($C_auth->auth_group_by_id($arr[$i])) $authorized = true;
				}
			}
			else
			{
				### Check if this department is authorized for the 'All Users' group id (0):
				$db     = &DB();
				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
	                            id          = ' . $db->qstr($department_id) . ' AND
	                            site_id     = ' . $db->qstr(DEFAULT_SITE)   . ' AND
	                            status      = ' . $db->qstr('1');
				$result = $db->Execute($sql);
				if($result->RecordCount() == 0)
				{
					### ERROR: not authorized to reopen this ticket!
					global $C_debug, $C_translate;
					$C_debug->alert($C_translate->translate('user_not_auth_reopen','ticket',''));
					return false;
				}
				$i = 0;
				$arr = unserialize($result->fields['group_id']);
				global $C_auth;
				for($i=0; $i<count($arr); $i++)
				{
					if($arr[$i] == 0) $authorized = true;
				}
			}
		}
		else
		{
			$authorized = true;
		}

		if (!$authorized && SESS_LOGGED == true)
		{
			### ERROR: not authorized to reopen this ticket!
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_not_auth_reopen','ticket',''));
			return false;
		}
		elseif (!$authorized && SESS_LOGGED == false)
		{
			### ERROR: not authorized to reopen this ticket & should try to login!
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_not_auth_reopen_login','ticket',''));
			return false;
		}

		if($status == '0' || $status == '2')
		$status = '0';
		else
		$status = '1';


		### Update the ticket status:
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'ticket SET
                        status      = ' . $db->qstr($status) .',
                        last_reply  = ' . $db->qstr( '2' ) .',
                        date_last   = ' . $db->qstr(time()) .'
                        WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        account_id  = ' . $db->qstr(SESS_ACCOUNT) . ' AND
                        id          = ' . $db->qstr($VAR['id']);
		$update = $db->Execute($sql);


		### Add the message Record to the DB
		$db     = &DB();
		$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'ticket_message_id');
		$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'ticket_message SET
                        site_id     =  ' . $db->qstr(DEFAULT_SITE) . ',
                        id          = ' . $db->qstr($id) . ',
                        ticket_id   = ' . $db->qstr($VAR['id']) . ',
                        date_orig   = ' . $db->qstr(time()) . ',
                        message     = ' . $db->qstr(htmlspecialchars($VAR['ticket_reply']), get_magic_quotes_gpc()) ;
		$result = $db->Execute($sql);


		###################################################################
		### Mail the user the new_ticket email template

		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;

		global $VAR;
		$VAR['email'] = trim($ticket->fields['email']);
		$VAR['key'] = $this->key($ticket->fields['email']);

		if(SESS_LOGGED)
		$my->send('ticket_user_update', SESS_ACCOUNT, $VAR['id'], '', '');
		else
		$my->send('ticket_user_update', $ticket->fields['email'], $VAR['id'], '', '');



		###################################################################
		### Get any staff members who should be mailed

		$db     =  &DB();
		$sql    = 'SELECT id,account_id,department_avail FROM ' . AGILE_DB_PREFIX . 'staff
                       WHERE
                       site_id        = ' . $db->qstr(DEFAULT_SITE) . ' AND
                       notify_change  = ' . $db->qstr("1");
		$result = $db->Execute($sql);

		if($result->RecordCount() > 0)
		{
			while(!$result->EOF)
			{
				@$avail = unserialize($result->fields['department_avail']);
				for($i=0; $i<count($avail); $i++)
				{
					if ($avail[$i] == $department_id)
					{
						###################################################################
						### Mail staff members the new_ticket email template
						$my = new email_template;
						$my->send('ticket_user_update_staff', $result->fields['account_id'], $VAR['id'], $avail[$i], '');
						$i = count($avail);
					}
				}
				$result->MoveNext();
			}
		}
		global $C_debug, $C_translate;
		$C_debug->alert($C_translate->translate('user_update_success','ticket',''));
		return;
	}




	##############################
	##   USER  VIEW		        ##
	##############################

	function user_view($VAR)
	{
		$this->construct();
		global $C_debug, $C_translate, $smarty;

		if(SESS_LOGGED == false)  {
			if(!$this->is_key_match($VAR)) {
				$smarty->assign('ticket',		false);
				return false;
			}
		}

		if(empty($VAR['id']))
		### ERROR: ID must be set!!
		{
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('user_ticket_nonexist','ticket',''));
			$smarty->assign('ticket',		false);
			return false;
		}

		### Get the ticket record
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket
            			WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' 
                        AND
                        (
                        	account_id  = ' . $db->qstr(SESS_ACCOUNT) . ' 
                        	OR
                        	email		= ' . $db->qstr(@$VAR['email']) . '
                        )
                        AND
                        id          = ' . $db->qstr($VAR['id']);

		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### Or if no results:
			$smarty->assign('ticket',		false);
			return false;
		}
		else
		{
			### Get any replies...
			$db     = &DB();
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_message WHERE
                            site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                            ticket_id   = ' . $db->qstr($VAR['id']) . '
                            ORDER BY date_orig';

			$replys = $db->Execute($sql);

			while (!$replys->EOF)
			{
				$reply[] = $replys->fields;
				$replys->MoveNext();
			}

			## Set everything as a smarty array, and return:
			$smarty->assign('ticket',		$result->fields);
			$smarty->assign('ticket_reply',	$reply);

			### Get the static vars:
			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;
			$arr = $static_var->view_form($this->module, $VAR['id']);

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



	##############################
	##   USER ADD 		        ##
	##############################

	function user_add($VAR)
	{
		$this->construct();
		global $C_debug, $C_translate, $C_vars, $smarty;

		### Strip Slashes
		global $VAR;
		$C_vars->strip_slashes_all();

		####################################################################
		### Check that the required fields are set:
		### ticket_department_id, ticket_subject, ticket_body
		####################################################################

		$fields = Array('priority',
		'department_id',
		'subject',
		'body');

		for($i=0; $i<count($fields); $i++)
		{
			$field = $fields[$i];
			$field_name = $this->table . '_' . $field;
			if(!isset($VAR["$field_name"]) || trim($VAR["$field_name"]) == "")
			{
				$this->val_error[] =  Array('field' 		=> $this->table . '_' . $field,
				'field_trans' 	=> $C_translate->translate('field_' . $field, $this->module, ""),							# translate
				'error' 		=> $C_translate->translate('validate_any',"", ""));
			}
		}



		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form($this->module, $this->val_error);

		if($all_error != false && gettype($all_error) == 'array')
		$this->validated = false;
		else
		$this->validated = true;

		### Validate e-mail
		if(!SESS_LOGGED)
		{

			include_once(PATH_CORE . 'validate.inc.php' );
			$C_validate = new CORE_validate;

			if(empty($VAR['ticket_email'])) {
				$this->validated = false;
				$smarty->assign('ticket_email', true);
				$all_error[] = Array (	'field' => 'ticket_email',
				'field_trans' => $C_translate->translate('field_email', "ticket", ""),
				'error' => $C_translate->translate('validate_any',"", ""));
			}
			elseif (!$C_validate->validate_email(@$VAR['ticket_email'], false))
			{
				$this->validated = false;
				$smarty->assign('ticket_email', true);
				$all_error[] = Array (	'field' => 'ticket_email',
				'field_trans' => $C_translate->translate('field_email', "ticket", ""),
				'error' => $C_translate->translate('validate_email',"", ""));
			}

			$this->email = $VAR['ticket_email'];

		} else {
			# Get the e-mail addy from the user's account
			$db     = &DB();
			$sql    = 'SELECT email FROM ' . AGILE_DB_PREFIX . 'account WHERE
	                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
	                        id          = ' . $db->qstr(SESS_ACCOUNT);
			$result = $db->Execute($sql);
			$VAR['ticket_email'] = $result->fields['email'];
			$this->email = $result->fields['email'];
		}


		###################################################################
		### Check that the user is authorized for this department

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        id          = ' . $db->qstr($VAR['ticket_department_id']) . ' AND
                        status      = ' . $db->qstr('1');
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			###################################################################
			### ERROR: The selected department is inactive or invalid

			$C_debug->alert($C_translate->translate('department_invalid', 'ticket', ''));
			return false;
		}

		global $C_auth;
		$i = 0;


		$dept_auth = false;
		while(!$result->EOF)
		{
			$arr = unserialize($result->fields['group_id']);

			if(!SESS_LOGGED)
			{
				### Check if the specified department is authorized for the 'All Users' group (0):
				for($i=0; $i<count($arr); $i++) {
					if($arr[$i] == '0') {
						$dept_auth = true;
					}
				}

				if(!$dept_auth) {
					$C_debug->alert($C_translate->translate('login_required', '', ''));
					return false;
				}
			}
			else
			{
				for($i=0; $i<count($arr); $i++) {
					if($C_auth->auth_group_by_id($arr[$i])) {
						$dept_auth = true;
					}
				}
			}
			$result->MoveNext();
		}

		if(!$dept_auth)
		{
			###################################################################
			### ERROR: The current user does not have access to the selected department!

			$C_debug->alert($C_translate->translate('department_not_auth', 'ticket', ''));
			return false;
		}
		else
		{

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

			###################################################################
			### Assemble the SQL & Insert the ticket

			$db     = &DB();
			$id     = $db->GenID(AGILE_DB_PREFIX . 'ticket_id');
			$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'ticket SET
                            site_id     = ' . $db->qstr(DEFAULT_SITE) . ',
                            id          = ' . $db->qstr($id) . ',
                            date_orig   = ' . $db->qstr(time()) . ',
                            date_last   = ' . $db->qstr(time()) . ',
                            date_expire = ' . $db->qstr(time()+86400*7) . ',
                            account_id  = ' . $db->qstr(SESS_ACCOUNT) . ',
                            department_id=' . $db->qstr($VAR['ticket_department_id']) . ',
                            status      = ' . $db->qstr(0) . ',
                            last_reply  = 0,
                            priority    = ' . $db->qstr($VAR['ticket_priority']) . ',
                            subject     = ' . $db->qstr($VAR['ticket_subject']) . ',
                            email		= ' . $db->qstr($VAR['ticket_email']) . ',
                            body        = ' . $db->qstr(htmlspecialchars($VAR['ticket_body'])) ;
			$result = $db->Execute($sql);

			# error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('ticket.inc.php','user_add', $db->ErrorMsg());
				return false;
			}


			###################################################################
			### Insert the static vars...

			$static_var->add($VAR, $this->module, $id);


			###################################################################
			### Mail the user the new_ticket email template

			require_once(PATH_MODULES   . 'email_template/email_template.inc.php');

			$VAR['email'] = trim($this->email);
			$VAR['key'] = $this->key($this->email);

			$my = new email_template;
			$my->send('ticket_user_add', $this->email, $id, '', '');

			unset($VAR['key']);
			unset($VAR['email']);

			###################################################################
			### Get any staff members who should be mailed

			$db     = &DB();
			$sql    = 'SELECT id,account_id,department_avail FROM ' . AGILE_DB_PREFIX . 'staff
                            WHERE
                            site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                            notify_new  = ' . $db->qstr("1");
			$result = $db->Execute($sql);

			if($result->RecordCount() > 0)
			{
				while(!$result->EOF)
				{
					@$avail = unserialize($result->fields['department_avail']);
					for($i=0; $i<count($avail); $i++)
					{
						if ($avail[$i] == $VAR['ticket_department_id'])
						{
							###################################################################
							### Mail staff members the new_ticket email template
							$my = new email_template;
							$my->send('ticket_user_add_staff', $result->fields['account_id'], $id, $avail[$i], 'sql3');
							$i = count($avail);
						}
					}
					$result->MoveNext();
				}
			}
		}

		global $C_debug, $C_translate;
		$C_debug->alert($C_translate->translate('user_add_success','ticket',''));
	}



	##############################
	##	DOES KEY MATCH? 		##
	##############################
	function is_key_match($VAR)
	{
		
		global $smarty;

		if (SESS_LOGGED == true)
		{
			$smarty->assign('ticket_key', true);
			return true;
		}

		if (empty($VAR['key']) || empty($VAR['email']))
		{
			$smarty->assign('ticket_key', false);
			return false;
		}

		if (strtolower($VAR['key']) == strtolower( $this->key( $VAR['email'] ) ) )
		{
			$smarty->assign('ticket_key', true);
			return true;

			### define as cookies:


		} else {
			$smarty->assign('ticket_key', false);
			return false;
		}
	}


	##############################
	##	GENERATE KEY 	 		##
	##############################
	function key ($email) {
		$key = strtoupper( md5(strtolower(trim($email)) . md5(LICENSE_KEY)) );
		return $key;
	}


	##############################
	##	IS USER AUTH FOR DEPTS? ##
	##############################

	function is_user_auth($VAR)
	{
		/* check if current session is authorized for any ticket departments..
		and return true/false...
		*/
		global $smarty;
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'ticket_department WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        status      = ' . $db->qstr('1');
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0)
		{
			$smarty->assign('display', false);
			return false;
		}

		global $C_auth;
		$ii = 0;

		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_id']);

			for($i=0; $i<count($arr); $i++)
			{
				if($C_auth->auth_group_by_id($arr[$i]))
				{
					### Add to the array
					$ii++;
					$arr_smarty[] = Array(  'name'          => $result->fields['name'],
					'description'   => $result->fields['description'],
					'id'            => $result->fields['id']);
					$i=count($arr);
				}
			}
			$result->MoveNext();
		}

		if($ii == "0")
		{
			$smarty->assign('display', false);
			return false;
		}
		else
		{
			$smarty->assign('display', 	true);
			$smarty->assign('results', 	$arr_smarty);
			return true;
		}
	}
	
	# Open the constructor for this mod
	function construct()
	{
		# name of this module:
		$this->module = "ticket";
	
		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
	
		# open the construct file for parsing
		if(defined('AJAX')) {
			require_once(PATH_CORE.'xml.inc.php');
			require_once(PATH_CORE.'translate.inc.php');
			$C_translate= new CORE_translate;
			global $C_translate;
		}
		
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
?>
