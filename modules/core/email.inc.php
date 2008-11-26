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
	
	
/*
 Email Handler Class

 This class handles the interface to SMPT and Mail() functions.

 $arr = Array(
	'from_html'     =>  'true/false'  (so we know whether to stripslashes or not)
	'html'          =>  '0/1',
	'from_name'     =>  '',
	'from_email'    =>  '',
	'priority'      =>  '0/1',
	'to_email'      =>  'email@email.com',
	'to_name'       =>  '',
	'bcc_list'      =>  Array('email@email.com'),
	'cc_list'       =>  Array('email@email.com'),
	'subject'       =>  '',
	'body_text'     =>  '',
	'body_html'     =>  '',
	'attachments'   =>  Array(Array('file' => 'file.exe',
									'data' => 'file data here...'))
	'server'        =>  'mail.domain.com',
	'account'       =>  '',
	'password'      =>  '');
 */
class CORE_email
{
	var $debug=false;

	function PHP_Mail($arr)
	{
		### SET THE SMTP SETTINGS
		#ini_set('sendmail_from',    @$arr['from_email']);
		#ini_set('SMTP',             @$arr['server']);

		### CC LIST
		if(isset($arr['cc_list']) == 'array')
		{
			if(count($arr['cc_list'] > 0))
			{
				$cc = '';
				for($i=0; $i<count($arr['cc_list']); $i++)
			   {
					if($i == 0)
						$cc .= $arr['cc_list'][$i];
					else
						$cc .= ','.$arr['cc_list'][$i].',';
				}
			}
		}

		### BCC LIST
		if(isset($arr['bcc_list']) == 'array')
		{
			if(count($arr['bcc_list'] > 0))
			{
				$bcc = '';
				for($i=0; $i<count($arr['bcc_list']); $i++)
				{
					if($i == 0)
						$bcc .= $arr['bcc_list'][$i];
					else
						$bcc .= ','.$arr['bcc_list'][$i];
				}
			}
		}

		$headers  = '';

		### FROM:
		$headers .= "From: \"".$arr['from_name']."\" <".$arr['from_email'].">\r \n";
		$headers .= "Reply-To: \"".$arr['from_name']."\" <".$arr['from_email'].">\r \n";

		# html/non-html version of body & headers
		if(isset($arr['html']) && $arr['html'] == '1' && isset($arr['body_html']))
		{
			### specify MIME version 1.0
			$headers .= "MIME-Version: 1.0\r \n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r \n";
			$body     = $arr['body_html'];
		}
		else
		{
			### specify MIME version 1.0
			$headers .= "MIME-Version: 1.0\r \n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\r \n";
			$body     = $arr['body_text'];
		}


		### CC:
		if(isset($cc))
		$headers .= "Cc: ".$cc."\r \n";

		### BCC:
		if(isset($bcc))
		$headers .= "Bcc: ".$bcc."\r \n";

		### PRIORITY
		if(isset($arr['priority']) && $arr['priority'] == '1')
		$headers .= "X-Priority: 1";
		else
		$headers .= "X-Priority: 3";


		/*
		echo "<pre>";
		echo print_r($arr);
		echo $headers;
		echo $body;
		*/

		### Strip Slashes
		if (!isset($arr['from_html']) || @$arr['html_form'] == false) {
			# from database, we must strip slashes
			$arr['subject'] = stripslashes($arr['subject']);
			$body           = stripslashes($body);
		} elseif (@$arr['from_html'] == true && get_magic_quotes_gpc()) {
			# straight from html, we must strip slashes
			$arr['subject'] = stripslashes($arr['subject']);
			$body           = stripslashes($body);
		}

		if($this->debug)
		{
			if(mail($arr['to_email'], $arr['subject'], $body, $headers)) {
				global $C_debug;
				$message = 'PHP mail() failed to send message "'.$arr['subject'].'" to "'.$arr['to_email'].'"';
				$C_debug->alert('CORE:email.inc.php','SMTP_Mail', $message);
				return false;
			}
		}
		else
		{
			if(@mail($arr['to_email'], $arr['subject'], $body, $headers)) {
				global $C_debug;
				$message = 'PHP mail() failed to send message "'.$arr['subject'].'" to "'.$arr['to_email'].'"';
				return false;
			}
		}
		return true;
	}



	function SMTP_Mail($arr)
	{
		### include the phpmailer class
		require_once(PATH_INCLUDES."phpmailer/class.phpmailer.php");
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth     = true;
		$mail->Host         = @$arr['server'];
		$mail->Username     = @$arr['account'];
		$mail->Password     = @$arr['password'];
		$mail->From         = $arr['from_email'];
		$mail->FromName     = $arr['from_name'];
		$mail->AddAddress($arr['to_email'], @$arr['to_name']);
		#$mail->AddReplyTo($arr['from_name'], $arr['from_email']);


		### CC LIST
		if(is_array(@$arr['cc_list']))
			for($i=0; $i<count($arr['cc_list']); $i++)
				$mail->AddCC($arr['cc_list'][$i], "");

		### BCC LIST
		if(is_array(@$arr['bcc_list']))
			for($i=0; $i<count($arr['bcc_list']); $i++)
				$mail->AddBCC($arr['bcc_list'][$i], "");

		### Strip Slashes
		if (empty($arr['from_html']) || @$arr['html_form'] == false) {
			# from database, we must strip slashes
			$arr['subject']   = stripslashes($arr['subject']);
			@$arr['body_html'] = stripslashes($arr['body_html']);
			@$arr['body_text'] = stripslashes($arr['body_text']);
		} elseif (@$arr['from_html'] == true && get_magic_quotes_gpc()) {
			# straight from html, we must strip slashes
			$arr['subject'] = stripslashes($arr['subject']);
			@$arr['body_html'] = stripslashes($arr['body_html']);
			@$arr['body_text'] = stripslashes($arr['body_text']);
		}

		# html/non-html version of body & headers
		if(isset($arr['html']) && $arr['html'] == '1' && isset($arr['body_html'])) {
			$mail->IsHTML(true);
			$mail->Body     =  @$arr['body_html'];
			$mail->AltBody  =  @$arr['body_text'];
		}  else  {
			$mail->IsHTML(false);
			$mail->Body     =  @$arr['body_text'];
			$mail->WordWrap = 50;
		}

		# subject
		$mail->Subject  =  $arr['subject'];

		# PRIORITY
		if(isset($arr['priority']) && $arr['priority'] == '1')
		$mail->Priority = 1;
		else
		$mail->Priority = 3;


		/* attachments
		$mail->AddAttachment("/var/tmp/file.tar.gz");
		$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
		*/

		if(!$mail->Send())
		{
			if($this->debug) {
				global $C_debug;
				$message = 'SMTP mail() failed to send message "'.$arr['subject'].'" to "'.$arr['to_email'].'" on server "'.$arr['server'].'"';
				$C_debug->error('CORE:email.inc.php','SMTP_Mail', $message . ' ---- '.$mail->ErrorInfo);
				echo "Message was not sent <p>";
				echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				global $C_debug;
				$message = 'SMTP mail() failed to send message "'.$arr['subject'].'" to "'.$arr['to_email'].'" on server "'.$arr['server'].'"';
				$C_debug->error('CORE:email.inc.php','SMTP_Mail', $message. ' ---- '.$mail->ErrorInfo);
			}
		   return false;
		}
		return true;
	}
}
?>