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
	
class net_term
{
	var $taxable=1;		# are late fees taxable? 0/1

	# Open the constructor for this mod
	function net_term()
	{ 
		# name of this module:
		$this->module = "net_term";

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

	### Check usergroup&checkout plugin to determin if net terms available (get best)
	function termsAllowed($account_id,$checkout_plugin_id) 
	{
		$db=&DB();
		$rs=&$db->Execute($sql=sqlSelect($db,"net_term","*","status=1 AND checkout_id=$checkout_plugin_id","fee ASC")); 
		if($rs && $rs->RecordCount() > 0)  { 
			global $C_auth; 
			while(!$rs->EOF) {
				$availarr = unserialize($rs->fields['group_avail']);       	
				if(is_array($availarr)) {
					foreach($availarr as $g) {
						if($C_auth->auth_group_by_account_id($account_id,$g)) return $rs->fields['id'];
					}
				}
			}
		}
		return 0;
	}

	### Task to generate late charges & insert into charge module:
	function task($VAR) 
	{
		require_once(PATH_MODULES.'email_template/email_template.inc.php');
		require_once(PATH_MODULES.'invoice/invoice.inc.php');
		$invoice = new invoice;

		# get active net terms
		$db=&DB();
		$rs=&$db->Execute($sql=sqlSelect($db,"net_term","*","status=1")); 
		if($rs && $rs->RecordCount() > 0) 
		{ 
			// loop through net terms 
			while(!$rs->EOF)
			{
				$id = $rs->fields['id']; 
				$last_interval = mktime(0,0,0,date('m'), date('d')-$rs->fields['terms'], date('Y')); 

				$i=&$db->Execute($sql=sqlSelect($db,"invoice",
					"id,account_id,total_amt,billed_amt,due_date,net_term_date_last,net_term_intervals", 
					"net_term_id = $id AND
					 (suspend_billing = 0 OR suspend_billing IS NULL) AND
					 (billing_status = 0 OR billing_status IS NULL) AND 
					 due_date <= $last_interval AND
					 net_term_date_last <= $last_interval"));  
				if($i && $i->RecordCount() > 0) 
				{   



					// loop through invoices
					while(!$i->EOF) 
					{ 
						$terms = $rs->fields['terms'];  
						echo "<BR>" . $start_interval = $i->fields['net_term_date_last'];
						echo "<BR>" . $stop_interval = $start_interval+(86400*$terms); 

						echo "<BR>". date(UNIX_DATE_FORMAT,$start_interval);

						// charge or suspend?
						if(!empty($i->fields['net_term_intervals']) && $rs->fields['suspend_intervals'] < $i->fields['net_term_intervals']) {

							// suspend invoice
							$arr['id'] = $i->fields['id'];
							$na =& $invoice->voidInvoice($arr,$invoice); 

							// suspend billing status
							$fields=Array('suspend_billing'=>1);
							$db->Execute($sql=sqlUpdate($db,"invoice",$fields,"id = {$i->fields['id']}"));		        				

							// send suspend e-mail
							if($rs->fields['enable_emails']) {
								$email = new email_template;
								$email->send('net_term_suspend', $i->fields['account_id'], $i->fields['id'], $rs->fields['suspend_intervals'], $i->fields['net_term_intervals']);		        				
							}
						} 
						else 
						{
							// calc late fee
							if($rs->fields['fee_type'] == 1) 
								$fee = $rs->fields['fee'];
							else
								$fee = ($i->fields['total_amt'] - $i->fields['billed_amt']) * $rs->fields['fee'];

							// create late charge
							if($fee>0) 
							{
								$fields=Array(	'date_orig'=> time(),
												'status'=> 0, 
												'account_id'=> $i->fields['account_id'], 
												'amount'=> $fee,
												'sweep_type'=> $rs->fields['sweep_type'],
												'taxable'=> $this->taxable,
												'quantity' => 1,
												'attributes'=> "Name=={$rs->fields['name']}\r\nInterval==".date(UNIX_DATE_FORMAT,$start_interval)." - ".date(UNIX_DATE_FORMAT,$stop_interval), // todo: translate
												'description'=> $rs->fields['sku']); 
								$db->Execute($sql=sqlInsert($db,"charge",$fields));

								// update invoice
								$_fields['net_term_intervals'] = $i->fields['net_term_intervals']+1;
								$_fields['net_term_date_last'] = $stop_interval; 
								$db->Execute($sql=sqlUpdate($db,"invoice",$_fields,"id={$i->fields['id']}")); 
								echo "<BR><BR>$sql";
							}		        					

							// send late fee/payment reminder e-mail:
							if($rs->fields['enable_emails']){
								$email = new email_template;
								$email->send('net_term_late_notice', $i->fields['account_id'], $i->fields['id'], number_format($fee,2), number_format($rs->fields['suspend_intervals']-$i->fields['net_term_intervals']));		        				
							}   				
						}
						$i->MoveNext();
					}        			
				}
				$rs->MoveNext();
			}
		}        		
	}



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
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
		$db = new CORE_database;
		 $db->mass_delete($VAR, $this, "");
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
		$db = new CORE_database;
		 $db->search($VAR, $this, $type);
	}

	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_show($VAR, $this, $type);
	}	

}
?>