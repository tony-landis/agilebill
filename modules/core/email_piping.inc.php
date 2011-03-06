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
	 
class email_piping
{
	var $results;
	var $delete_action=true;
	var $move_mbox='Trash';
	var $get_attachments=true; 
	var $max_attachment=2048; // max filesize of attachment in KB
		
	/**
	 * Start the email retrieval
	 *
	 * @param int $id email_setup.id 
	 */
	function email_piping($id)
	{
		global $C_debug;
		
		# check for imap support:
		if(!is_callable('imap_open')) { 
			$C_debug->error('core::email_piping','email_piping()', 'imap_open() - not supported');
			return;
		}
  
        $db = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'setup_email WHERE site_id = ' . $db->qstr(DEFAULT_SITE) . ' AND id = ' . $db->qstr($id);
        $result = $db->Execute($sql); 
        if($result && $result->RecordCount()) {
        
	        $this->cfg = $result->fields;  
	        $this->delete_action = $this->cfg['piping_action'];
	        $type = $this->cfg['piping']; // imap/pop
	        
	        // get imap_open connection string
	       	$constr = $this->get_connection_string($type, $this->cfg['piping_host']);
	        
	        // attempt connection
			error_reporting(0); 
			@$mbox = imap_open($constr, $this->cfg['piping_username'], $this->cfg['piping_password']); 		
			
			// attempt alternate conneciton
			if(!$mbox) { 
				$constr = $this->get_connection_string($type+2, $this->cfg['piping_host']); 
				@$mbox = imap_open($constr, $this->cfg['piping_username'], $this->cfg['piping_password']);	
			}
			
			// error log
			if(!$mbox) {			
		   		$C_debug->error('core::email_piping','email_piping()', imap_last_error() .' -- '. $constr ); 
		   	 	return false;
			} 			

			// check for messages
			if (@$hdr = imap_check($mbox)) { 
				$msgCount = $hdr->Nmsgs;
				if($msgCount==0) return false;
			} else {
			   	return false; // no messages
			}
			 
			// get folder overview
			@$overview=imap_fetch_overview($mbox,"1:$msgCount",0);
			$size=sizeof($overview);
			  
			// loop through messages
			for($i=$size-1;$i>=0;$i--)
			{ 
				$val=$overview[$i];
				$msg=$val->msgno;
				$this->attachments[$msg]=false;
				
				// formatting for from e-mail address
			   	$from=$val->from;
			   	$sender=$from=$val->from;
			   	$from=preg_replace("/\"/","",$from);
			   	if(preg_match("/</",$from)) {
			   		$f=explode("<", $from);
			   		$sender=$f[0];
			   		$from=$f[1];
			   		$from=str_replace(">","",$from);
			   	}
				  
	           	// retrieve body
				$body = $this->get_part($mbox, $msg, "TEXT/PLAIN");
				if(empty($body)) {
					$body = $this->get_part($mbox, $msg, "TEXT/HTML");
					if(!empty($body)) $body=str_replace("<br>", "\r\n", $body);
				}
				  
				// get attachements
				if($this->get_attachments) {				
				   	$struct=imap_fetchstructure($mbox,$msg);
				   	$contentParts = count($struct->parts);				   
				  	if ($contentParts >= 2) {				  		
				  		
						for ($ii=2; $ii<=$contentParts; $ii++) {
							$c=($ii-2);
				   			$att[$c] = imap_bodystruct($mbox,$msg,$ii); 
				   		}
				   		
				   		// download tmp file and add to attachemnt array
				   		for ($k=0;$k<sizeof($att);$k++) {
				   			$tmp=$this->download_file($mbox, $msg, $k, $att[$k]->parameters[0]->value);
				   		} 
				   	}   
				}
 			
				// Set the result array:
			    $this->results[] = Array('uniqueId' => $val->message_id,
			    						 'date'		=> $val->date,
			    						 'from'		=> trim($from),
			    						 'to'		=> $val->to,
			    						 'sender'   => trim($sender),
			    						 'subject'	=> trim($val->subject),
			    						 'body'		=> trim($body),
			    						 'attach'	=> $this->attachments[$msg]);
 			    						 			      
				// mark for deletion or move 				 
				if($this->cfg['piping_action'] == 1) {
					if($this->delete_action) 
						imap_delete($mbox, $msg);
					else if(($type==2 || $type==4 ) && !empty($this->move_mbox))
						imap_mail_move($mbox, $msg, $this->move_mbox); 	 
				}   			
			}  
			imap_close($mbox, CL_EXPUNGE);			 
        } 
	}
	
	/**
	 * Get an attached file 
	 */
   	function download_file(&$mbox, $msg, $file, $filename) { 
   		$filetype = strrev(substr(strrev($filename),0,3));
   		$filecontent = imap_fetchbody($mbox,$msg,$file+2);
   		if(empty($filecontent)) return false;  
   		$tmp = PATH_FILES.'/'. md5('piping'.$filename.microtime());
   		file_put_contents($tmp,$filecontent);    
   		$this->attachments[$msg][] = Array('tmp'=>$tmp, 'file'=>$filename, 'type'=> $filetype);
    }	
	
	/**
	 * Get MIME Type 
	 */
	function get_mime_type(&$structure) {
		$primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
	   	if($structure->subtype) {
	   		return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
	   	}
	   	return "TEXT/PLAIN";
   	}
   
   	/**
   	 * Get Message Part 
   	 */
   	function get_part($stream, $msg_number, $mime_type, $structure=false, $part_number=false) {   
		if(!$structure) {
	   		$structure = imap_fetchstructure($stream, $msg_number);
	   	}
	   	if($structure) {
	   		if($mime_type == $this->get_mime_type($structure)) {
	   			if(!$part_number) {
	   				$part_number = "1";
	   			}
	   			$text = imap_fetchbody($stream, $msg_number, $part_number);
	   			if($structure->encoding == 3) {
	   				return imap_base64($text);
	   			} else if($structure->encoding == 4) {
	   				return imap_qprint($text);
	   			} else {
	   				return $text;
	   			}
	   		}
	   		// multipart 
			if($structure->type == 1)  {
	   			while(list($index, $sub_structure) = each($structure->parts)) {
		   			if($part_number) {
		   				$prefix = $part_number . '.';
		   			}
		   			$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, @$prefix.($index + 1));
		   			if($data) {
	   					return $data;
	   				}
	   			} 
	   		} 
		} 
	   	return false;
   	}  	
	 
	
	/**
	 * Get a connection string to use
	 *
	 * @param string $type 1=pop3, 2=imap, 3=pop3 alternate, 4=imap alternate
	 * @param string $host HOSTNAME OR HOSTNAME:PORT|MBOX
	 * @return string Connection string for imap_open()
	 */
	function get_connection_string($type,$host,$port=false,$mbox=false) {
		 
		# Determine the path, host, & mbox name:
		if(preg_match("/:/", $host)) {
			$arr = explode(":", $host);
			
			# host
			@$host = $arr[0];
						  
			# port
			if(!$port && is_numeric($arr[1])) @$port = $arr[1];
			  
			# mbox
			elseif(preg_match('[|]', $arr[1])) {
				$arr2 = explode('|', $arr[1]);
				if(!$port && is_numeric($arr2[0]) ) @$port = $arr2[0];
				if(!$mbox) @$mbox = $arr2[1];
			}    	
		}
		
		# defaults: 
		if(empty($port) && $type==1 || $type==3) $port = "110";
		if(empty($port) && $type==2 || $type==4) ;
		if(empty($mbox)) $mbox = "INBOX";

		// pop3
		if($type==1) {
			if(empty($port)) $port = "110";
			return "{".$host.":".$port."/pop3}".$mbox; 
		}
		
		// pop3 alternate
		if($type==3) {
			if(empty($port)) $port = "110";
			return  "{".$host.":".$port."/pop3/notls/novalidate-cert}".$mbox;
		}
						
		// imap 
		if($type==2 ) {
			if(empty($port)) $port = "143";
			return "{".$host.":".$port."}".$mbox; 		
		}
		
		// imap alternate
		if($type==4 ) {
			if(empty($port)) $port = "143";
			return "{".$host.":".$port."/imap/notls/novalidate-cert}".$mbox; 		
		}		
 	
		return string;
	} 
} 
?>
