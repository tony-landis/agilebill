<?php
/* 
 **************************************************************************
 *
 * OpenSRS-PHP
 *
 * Copyright (C) 2000, 2001, 2002, 2003 Colin Viebrock
 *   and easyDNS Technologies Inc.
 *
 * Version 2.7.3
 *   15-Aug-2003
 *
 **************************************************************************
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.   
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 **************************************************************************
 *
 * vim: set expandtab tabstop=4 shiftwidth=4:
 * $Id: openSRS_base.php,v 1.1 2004/09/30 09:25:23 Tony Exp $
 *
 **************************************************************************
 */


# assuming that the PEAR directory is within your path, otherwise change the path here

require_once 'PEAR.php';
require_once 'Crypt/CBC.php';


# local requirements

require_once 'OPS.php';
require_once 'country_codes.php';



class openSRS_base extends PEAR {


	var $USERNAME				= '';
	var $HRS_USERNAME			= '';

	var $PRIVATE_KEY			= '';
	var $TEST_PRIVATE_KEY		= '';
	var $LIVE_PRIVATE_KEY		= '';
	var $HRS_PRIVATE_KEY		= '';

	var $VERSION				= 'XML:0.1';

	var $base_class_version		= '2.7.3';

	var $environment			= 'TEST';	/* 'TEST' or 'LIVE' or 'HRS' */
	var $protocol				= 'XCP';	/* 'XCP' or 'TPP' */

	var $LIVE_host				= 'rr-n1-tor.opensrs.net';
	var $LIVE_port		 		= 55000;
	var $TEST_host				= 'horizon.opensrs.net';
	var $TEST_port				= 55000;
	var $HRS_host;
	var $HRS_port;
	var $REMOTE_HOST;
	var $REMOTE_PORT;

	var $connect_timeout		= 20;				/* seconds */
	var $read_timeout			= 20;				/* seconds */

	var $log					= array();

	var $_socket				= false;
	var $_socket_error_num		= false;
	var $_socket_error_msg		= false;
	var $_session_key			= false;

	var $_OPS;
	var $_CBC;

	var $lookup_all_tlds		= false;

	var $_CRYPT;
	var $_iv;
	var $crypt_type				= 'DES';				/* 'DES' or 'BLOWFISH' */
	var $crypt_mode				= 'CBC';				/* only 'CBC' */
	var $crypt_rand_source		= MCRYPT_DEV_URANDOM;	/* or MCRYPT_DEV_RANDOM or MCRYPT_RAND */
	var $affiliate_id;

	var $PERMISSIONS = array (
		'f_modify_owner'	=> 1,
		'f_modify_admin'	=> 2,
		'f_modify_billing'	=> 4,
		'f_modify_tech'		=> 8,
		'f_modify_nameservers'	=> 16
	);

	var $REG_PERIODS = array (
		1	=> '1 Year',
		2	=> '2 Years',
		3	=> '3 Years',
		4	=> '4 Years',
		5	=> '5 Years',
		6	=> '6 Years',
		7	=> '7 Years',
		8	=> '8 Years',
		9	=> '9 Years',
		10	=> '10 Years'
	);

	var $UK_REG_PERIODS = array (
		2	=> '2 Years'
	);

	var $TV_REG_PERIODS = array (
		1	=> '1 Year',
		2	=> '2 Years',
		3	=> '3 Years',
		5	=> '5 Years',
		10	=> '10 Years'
	);

	var $TRANSFER_PERIODS = array (
		1	=> '1 Year'
	);

	var $OPENSRS_TLDS_REGEX = '(\.ca|\.(bc|ab|sk|mb|on|qc|nb|ns|pe|nf|nt|nv|yk)\.ca|\.com|\.net|\.org|\.co\.uk|\.org\.uk|\.tv|\.vc|\.cc|\.info|\.biz|\.name|\.us)';

	var $CA_LEGAL_TYPES = array (
		'ABO'	=> 'Aboriginal',
		'ASS'	=> 'Association',
		'CCO'	=> 'Canadian Corporation', 
		'CCT'	=> 'Canadian Citizen',
		'EDU'	=> 'Educational Institute',
		'GOV'	=> 'Government',
		'HOP'	=> 'Hospital',
		'INB'	=> 'Indian Band',
		'LAM'	=> 'Library, Archive or Museum',
		'LGR'	=> 'Legal Respresentative',
		'MAJ'	=> 'Her Majesty the Queen',
		'OMK'	=> 'Protected by Trade-marks Act',
		'PLT'	=> 'Political Party',
		'PRT'	=> 'Partnership',
		'RES'	=> 'Permanent Resident',
		'TDM'	=> 'Trade-mark Owner',
		'TRD'	=> 'Trade Union',
		'TRS'	=> 'Trust'
	);

	var $CA_LANGUAGE_TYPES = array (
		'EN'	=> 'English',
		'FR'	=> 'French'
	);

	var $CA_NATIONALITIES = array (
		'CND'	=> 'Canadian citizen',
		'OTH'	=> 'Foreign citizenship',
		'RES'	=> 'Canadian permanent resident'
	);


	var $OPENSRS_ACTIONS = array (
		'get_domain'				=> true,
		'get_userinfo'				=> true,

		'modify_domain'				=> true,
		'renew_domain'				=> true,
		'register_domain'			=> true,

		'get_nameserver'			=> true,
		'create_nameserver'			=> true,
		'modify_nameserver'			=> true,
		'delete_nameserver'			=> true,

		'get_subuser'				=> true,
		'add_subuser'				=> true,
		'modify_subuser'			=> true,
		'delete_subuser'			=> true,

		'change_password'			=> true,
		'change_ownership'			=> true,

		'set_cookie'				=> true,
		'delete_cookie'				=> true,
		'update_cookie'				=> true,

		'sw_register_domain'		=> true,
		'bulk_transfer_domain'		=> true,
		'register_domain'			=> true,

		'lookup_domain'				=> true,
		'get_price_domain'			=> true,

		'check_transfer_domain'		=> true,
		'quit_session'				=> true,

		'buy_webcert'				=> true,
		'refund_webcert'			=> true,	
		'query_webcert'				=> true,
		'cprefget_webcert'			=> true,
		'cprefset_webcert'			=> true,
		'cancel_pending_webcert'	=> true,
		'update_webcert'			=> true,

	);



#
#	BASIC PUBLIC FUNCTIONS
#


	/**
	 * Class constructor
	 *
	 * Initialize variables, logs, etc.
	 *
	 * @param	string		Which environment to use (LIVE or TEST or HRS)
	 * @param	string		Which protocol to use (XCP or TPP)
	 *
	 */

	function openSRS_base( $environment=NULL, $protocol=NULL )
	{

		$this->PEAR();

		$this->_log('i', 'OpenSRS Log:');
		$this->_log('i', 'Initialized: '.date('r') );

		$this->_OPS	= new OPS;

		if ($environment) {
			$this->environment = strtoupper($environment);
		}
		if ($protocol) {
			$this->protocol = strtoupper($protocol);
		}

		$this->_log('i', 'Environment: '.$this->environment );
		$this->_log('i', 'Protocol: '.$this->protocol );

		$this->PRIVATE_KEY = $this->{$this->environment.'_PRIVATE_KEY'};

		$this->_CBC	= false;

	}


#
#	setProtocol()
#		Switch between XCP and TPP
#


	function setProtocol( $proto )
	{

		$proto = trim(strtoupper($proto));

		switch ($proto) {
		  case 'XCP':
		  case 'TPP':
			$this->protocol = $proto;

			$this->_log('i', 'Set protocol: '.$this->protocol );

			return true;
			break;

		  default:
			return array(
				'is_success'	=> false,
				'error'			=> 'Invalid protocol: ' . $proto
			);
			break;
		}

	}


#
#	logout()
#		Send a 'quit' command to the server
#

	function logout() {
		if ($this->_socket) {
			$this->send_cmd( array(
				'action'	=> 'quit',
				'object'	=> 'session' )
			);
			$this->close_socket();
		}
	}


#
#	send_cmd()
#		Send a command to the server
#

	function send_cmd($request) {
		global $HTTP_SERVER_VARS;

		if (!is_array($request)) {
			$data = array(
				'is_success'	=> false,
				'response_code'	=> 400,
				'response_text'	=> 'Invalid command (not an array): '.$request
			);
			$this->_log('i',$data);
			return $data;
		}

		$action = $request['action'];
		$object = $request['object'];

		# prune any private data before sending down to the server
		# (eg. credit card numbers and information

		$this->prune_private_keys($request);

#
# Disable action checking.  This means you don't need to update the code
# each time OpenSRS adds a new command, but means you should be more 
# careful with your coding ...
#
#		if (!isset($this->OPENSRS_ACTIONS[$action.'_'.$object])) {
#			$data = array(
#				'is_success'	=> false,
#		        	'response_code'	=> 400,
#				'response_text'	=> 'Invalid command: '.$action.' '.$object
#			);
#			$this->_log('i',$data);
#			return $data;
#		}


		# make or get the socket filehandle

		if (!$this->init_socket() ) {
			$data = array(
				'is_success'	=> false,
				'response_code'	=> 400,
				'response_text'	=> 'Unable to establish socket: (' .
					$this->_socket_err_num . ') ' . $this->_socket_err_msg
			);
			$this->_log('i',$data);
			return $data;
		}

		if ($this->environment == 'HRS') {
			$auth = $this->authenticate($this->HRS_USERNAME,$this->PRIVATE_KEY);
		} else {
			$auth = $this->authenticate($this->USERNAME,$this->PRIVATE_KEY);
		}

		if (!$auth['is_success']) {
			if ($this->_socket) {
				$this->close_socket();
			}
			$data = array(
				'is_success'	=> false,
				'response_code'	=> 400,
				'response_text'	=> 'Authentication Error: ' . $auth['error']
			);
			$this->_log('i',$data);
			return $data;
		}

		$request['registrant_ip'] = $HTTP_SERVER_VARS['REMOTE_ADDR'];

		if ( strstr($request['action'], 'lookup') ) {
			# lookups are treated specially 
			$data = $this->lookup_domain( $request );
		} else {
			# send request to server
			$this->send_data( $request );
			$data = $this->read_data( );
		}

		return $data;

	}


#
#	validate()
#		Check data for validity
#

	function validate($data,$params=array()) {

		# Country codes are needed for checking ... more reliable than /^[A-Z]{2}$/
		include 'country_codes.php';

		$missing_fields = $problem_fields = array();

		$required_contact_fields = array (
			'first_name'	=> 'First Name',
			'last_name'	=> 'Last Name',
			'org_name'	=> 'Organization Name',
			'address1'	=> 'Address1',
			'city'		=> 'City',
			'country'	=> 'Country',
			'phone'		=> 'Phone',
			'email'		=> 'E-Mail'
			);

		$contact_types = array (
			'owner'		=> '',
			'billing'	=> 'Billing'
			);

		$required_fields = array (
			'reg_username'	=> 'Username',
			'reg_password'	=> 'Password',
			'domain'	=> 'Domain',
			);

		if (isset($params['custom_tech_contact'])) {
			$contact_types['tech'] = 'Tech';
		}

		# The primary and secondary nameservers are required.

		if ( isset($params['custom_nameservers']) && $data['reg_type']=='new' )	{
			if (!$data['fqdn1']) {
				$missing_fields[] = 'Primary DNS Hostname';
			}
			if (!$data['fqdn2']) {
				$missing_fields[] = 'Secondary DNS Hostname';
			}
		}

		# check the required fields

		foreach ($contact_types as $type=>$contact_type) {

			foreach ($required_contact_fields as $field=>$required_field) {
				$data[$type.'_'.$field] = trim($data[$type.'_'.$field]);

				if ($data[$type.'_'.$field] == '') {
					$missing_fields[] = $contact_type.' '.$required_field;
				}
			}

			$data[$type.'_country'] = strtolower($data[$type.'_country']);

			if ($data[$type.'_country']=='us' || $data[$type.'_country']=='ca') {
				if ($data[$type.'_postal_code']=='') {
					$missing_fields[] = $contact_type.' Zip/Postal Code';
				}
				if ($data[$type.'_state']=='') {
					$missing_fields[] = $contact_type.' State/Province';
				}
			}

			if (!isset($COUNTRY_CODES[$data[$type.'_country']])) {
				$problem_fields[$contact_type.' Country'] = $data[$type.'_country'];
			}

			if (!$this->check_email_syntax($data[$type.'_email'])) {
				$problem_fields[$contact_type.' Email'] = $data[$type.'_email'];
			}

			if (!preg_match('/^\+?[\d\s\-\.\(\)]+$/', $data[$type.'_phone'] )) {
				$problem_fields[$contact_type.' Phone'] = $data[$type.'_phone'];
			}
		}

		foreach ($required_fields as $field=>$required_field) {
			if ($data[$field] == '') {
				$missing_fields[] = $required_field;
			}
		}

		# these fields must have at least an alpha in them

		foreach ($data as $field=>$value) {
			if ($value=='') {
				continue;		# skip blanks
			}

			if ($field=='first_name' || $field=='last_name' || $field=='org_name' || $field=='city' || $field=='state') {
				if (!preg_match('/[a-zA-Z]/', $value)) {
					$error_msg .= "Field $field must contain at least 1 alpha character.<br>\n";
				}
			}
		}

		# take $missing_fields and add them to $error_msg

		foreach ($missing_fields as $field) {
			$error_msg .= "Missing field: $field.<br>\n";
		}


		# check syntax on several fields
		# check domain, country, billing_country, email, billing_email, phone,
		# and billing_phone

		$domains = explode("\0", $data['domain'] );
		foreach ($domains as $domain) {
			$syntaxError = $this->check_domain_syntax($domain);
			if ($syntaxError) {
				$problem_fields['Domain'] = $domain . " - " . $syntaxError;
			}
		}


		# print error if $problem_fields

		if (count(array_keys($problem_fields))) {
			foreach ($problem_fields as $field=>$problem) {
				# only show problem fields if it had a value.  Otherwise, it
				# would have been caught above.
				if ($problem != '') {
					$error_msg .= "The field \"$field\" contained invalid characters: <i>$problem</i><br>\n";
				}
			}
		}


		# insert other error checking here...

		if ($error_msg) {
			return ( array('error_msg' => $error_msg) );
		} else {
			return ( array('is_success' => true) );
		}
	}


#
#	version()
#		return base class version
#

	function version() {
		return 'OpenSRS-PHP Class version '.$this->base_class_version;
	}




#
#	PRIVATE FUNCTIONS
#

#
#	init_socket()
#		Initialize a socket connection to the OpenSRS server
#

	function init_socket() {

		if ($this->_socket) {
			return true;
		}

		if (!$this->environment) {
			return false;
		}

		$this->REMOTE_HOST = $this->{$this->environment.'_host'};
		$this->REMOTE_PORT = $this->{$this->environment.'_port'};

		# create a socket

		$this->_socket = fsockopen($this->REMOTE_HOST, $this->REMOTE_PORT,
			$this->_socket_err_num, $this->_socket_err_msg, $this->connect_timeout );

		if (!$this->_socket) {
			return false;
		} else {
			$this->_log('i','Socket initialized: ' . $this->REMOTE_HOST . ':' . $this->REMOTE_PORT );
			return true;
		}
	}


#
#	authenticate()
#		Authenticate the connection with the username/private key
#

	function authenticate($username=false,$private_key=false) {

		if (@$this->_authenticated) {
			return array('is_success' => true);
		}

		if (!$username) {
			return array(
				'is_success'	=> false,
				'error'			=> 'Missing reseller username'
			);
		} else if (!$private_key) {
			return array(
				'is_success'	=> false,
				'error'			=> 'Missing private key'
			);
		}

		$prompt = $this->read_data();

		if ( $prompt['response_code'] == 555 ) {
			# the ip address from which we are connecting is not accepted
			return array(
				'is_success'	=> false,
				'error'			=> $prompt['response_text']
			);
		} else if ( !preg_match('/OpenSRS\sSERVER/', $prompt['attributes']['sender']) ||
			substr($prompt['attributes']['version'],0,3) != 'XML' ) {
			return array(
				'is_success'	=> false,
				'error'			=> 'Unrecognized Peer'
			);
		}

		# first response is server version

		$cmd = array(
			'action'	=> 'check',
			'object'	=> 'version',
			'attributes'	=> array(
				'sender'	=> 'OpenSRS CLIENT',
				'version'	=> $this->VERSION,
				'state'		=> 'ready'
			)
		);
		$this->send_data( $cmd );

		$cmd = array(
			'action'		=> 'authenticate',
			'object'		=> 'user',
			'attributes'	=> array(
				'crypt_type'	=> strtolower($this->crypt_type),
				'username'		=> $username,
				'password'		=> $username
			)
		);
		$this->send_data( $cmd );

		$challenge = $this->read_data( array('no_xml'=>true,'binary'=>true) );

		# Respond to the challenge with the MD5 checksum of the challenge.
		# note the use of the no_xml => 1 set, because challenges are
		# are sent without XML

		# ... and PHP's md5() doesn't return binary data, so
		# we need to pack that too


		$this->_CBC = new Crypt_CBC(pack('H*', $private_key), $this->crypt_type );

		$response = pack('H*',md5($challenge));

		$this->send_data( $response, array('no_xml'=>true,'binary'=>true) );

		# Read the server's response to our login attempt (XML)

		$answer = $this->read_data();

		if (substr($answer['response_code'],0,1)== '2') {
			$this->_authenticated = true;
			return array('is_success' => true);
		} else {
			return array(
				'is_success'	=> false,
				'error'			=> 'Authentication failed'
			);
		}
	}


#
#	lookup_domain()
#		Special case for domain lookups
#
#
#	NOTE: I have changed the error codes returned by this function.
#	Instead of having all syntax errors return a 400 code, they return
#	codes in the range 490 to 499 range, depending on the type of error.
#	This makes it much easier to determine what *kind* of lookup error
#	happened, by having *us* parse through response_text for various strings.
#
#		490	No domain given
#		491	TLD not supported
#		492	Domain name too long
#		493	Invalid characters
#		499	Other error
#
#	Syntax errors coming back from the server will likely still have error
#	code 400.  Oh well.
#


	function lookup_domain($lookupData) {

		$domain = strtolower($lookupData['attributes']['domain']);
		$affiliate_id = $lookupData['attributes']['affiliate_id'];

		if ($domain=='') {
			$data = array(
				'is_success'	=> false,
				'response_code'	=> 490,
				'response_text'	=> "Invalid syntax: no domain given."
			);
			return $data;
		}

		$syntaxError = $this->check_domain_syntax($domain);

		if ($syntaxError) {

# START of new error stuff
			$code = 499;
			if (strstr($syntaxError, 'Top level domain in')) {
				$code = 491;
			} else if (strstr($syntaxError, 'Domain name exceeds maximum length')) {
				$code = 492;
			} else if (strstr($syntaxError, 'Invalid domain format')) {
				$code = 493;
			}
# END of new error stuff

			$data = array(
				'is_success'	=> false,
				'response_code'	=> $code,
				'response_text'	=> "Invalid domain syntax for $domain: $syntaxError."
			);
			return $data;
		}

		# attempt to find other available matches if requested in conf file

		$domains = array();

		preg_match('/(.+)'.$this->OPENSRS_TLDS_REGEX.'$/', $domain, $temp);
		$base = $temp[1];
		$tld = $temp[2];

		$relatedTLDs = $this->getRelatedTLDs( $tld );
		if ($this->lookup_all_tlds && is_array($relatedTLDs)) {
			$domains = array();
			foreach($relatedTLDs as $stem) {
				$domains[] = $base.$stem;
			}
		} else {
			$domains[] = $domain;
		}

		$data = array();

		foreach($domains as $local_domain) {
			$lookupData['attributes']['domain'] = $local_domain;

			# send request to server
	
			$this->send_data( $lookupData );
			$answer = $this->read_data( );

			if ( $answer['attributes']['status'] &&
			  stristr($answer['attributes']['status'],'available') &&
			  !stristr($local_domain,$domain) ) {

				$data['attributes']['matches'][] = $local_domain;
			}


			# The original domain in the lookup determines
			# the overall return values

			if ($local_domain==$domain) {
				$data['is_success']	= $answer['is_success'];
				$data['response_code']	= $answer['response_code'];
				$data['response_text']	= $answer['response_text'];
				$data['attributes']['status'] = $answer['attributes']['status'];
				$data['attributes']['upg_to_subdomain'] = $answer['attributes']['upg_to_subdomain'];
				$data['attributes']['reason'] = $answer['attributes']['reason'];

			}
		}

		return $data;

	}


#
#	close_socket()
#		Close the socket connection
#

	function close_socket() {
		fclose($this->_socket);
		if ($this->_CBC) {
			$this->_CBC->_Crypt_CBC();			/* destructor */
		}
		$this->_CBC				= false;
		$this->_authenticated	= false;
		$this->_socket			= false;
		$this->_log('i','Socket closed');
	}


#
#	read_data()
#		Reads a response from the server
#

	function read_data($args=array()) {

		$buf = $this->_OPS->readData($this->_socket, $this->read_timeout);

		if (!$buf) {
			$data = array('error' => 'Read error');
			$this->_log('i',$data);
		} else {
			$data = $this->_CBC ? $this->_CBC->decrypt($buf) : $buf;
			if (!$args['no_xml']) {
				$data = $this->_OPS->decode($data);
			}
			if ($args['binary']) {
				$temp = unpack('H*temp', $data);
				$this->_log('r', 'BINARY: ' . $temp['temp'] );
			} else {
				$this->_log('r',$data);
			}
		}

		return $data;
	}


#
#	send_data()
#		Sends request to the server
#

	function send_data($message, $args=array()) {

		if (!$args['no_xml']) {
			$message['protocol'] = $this->protocol;
			$data_to_send = $this->_OPS->encode( $message );

			# have to lowercase the action and object keys
			# because OPS.pm uppercases them

			$message['action'] = strtolower($message['action']);
			$message['object'] = strtolower($message['object']);
		} else {
			# no XML encoding
			$data_to_send = $message;
		}

		if ($args['binary']) {
			$temp = unpack('H*temp', $message);
			$this->_log('s', 'BINARY: ' . $temp['temp'] );
		} else {
			$this->_log('s', $message);
		}

		if ($this->_CBC) {
			$data_to_send = $this->_CBC->encrypt($data_to_send);
		}

		return $this->_OPS->writeData( $this->_socket, $data_to_send );
	}


#
#	check_email_syntax()
#		Regex check for valid email
#

	function check_email_syntax($email) {
		if ( preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email) ||
			!preg_match('/^\S+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/', $email) ) {
			return false;
		} else {
			return true;
		}
	}


#
#	check_domain_syntax()
#		Regex check for valid domain
#

	function check_domain_syntax($domain) {

		$domain = strtolower($domain);

		$MAX_UK_LENGTH = 61;
		$MAX_NSI_LENGTH = 67;

		if (substr($domain,-3)=='.uk') {
			$maxLengthForThisCase = $MAX_UK_LENGTH;
		} else {
			$maxLengthForThisCase = $MAX_NSI_LENGTH;
		}

		if (strlen($domain) > $maxLengthForThisCase) {
			return "Domain name exceeds maximum length for registry ($maxLengthForThisCase)";
		} else if (!preg_match('/'.$OPENSRS_TLDS_REGEX.'$/', $domain)) {
			return "Top level domain in \"$domain\" is unavailable";
		} else if (!preg_match('/^[a-zA-Z0-9][.a-zA-Z0-9\-]*[a-zA-Z0-9]'.$this->OPENSRS_TLDS_REGEX.'$/', $domain)) {
			return "Invalid domain format (try something similar to \"yourname.com\")";
		}
		return false;
	}


#
#	prune_private_keys()
#		Recursively remove keys that start with 'p_' from an array
#

	function prune_private_keys(&$data) {
		if (is_array($data) || is_object($data)) {
			foreach($data as $key=>$value) {
				if (substr($key,0,2)=='p_') {
					unset($data[$key]);
				} else if (is_array($value)) {
					$this->prune_private_keys($value);
				}
			}
		}
	}


#
#	getRelatedTLDs()
#

	function getRelatedTLDs($tld) {
		if (is_array($this->RELATED_TLDS)) {
			foreach($this->RELATED_TLDS as $relatedTLDs) {
				foreach ($relatedTLDs as $TLDstring) {
					if ($TLDstring==$tld) {
						return $relatedTLDs;
					}
				}
			}
		}
		return array();
	}



#
#	_log()
#		Internal logging method
#

	function _log($type,$data) {

		$types = array(
			'i'	=> 'Info',
			'r'	=> 'Read',
			's'	=> 'Sent'
		);

		$temp = sprintf("[ %s%s ]\n",
			strtoupper($types[$type]),
			(($type!='i' && $this->_CBC) ? ' - '.$this->crypt_type.' ENCRYPTED' : '')
		);

		ob_start();
		print_r($data);
		$temp .= ob_get_contents() . "\n";
		ob_end_clean();

		$this->log[] = $temp;

	}

#
#	showlog()
#		output the debugging log
#

	function showlog() {
		echo '<PRE>';
		foreach ($this->log as $line) {
			echo htmlEntities($line) . "\n";
		}
		echo '</PRE>';
	}



}
