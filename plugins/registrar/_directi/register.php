<?php 
 
	include_once('../../../config.inc.php');
	require_once(PATH_ADODB  . 'adodb.inc.php');
	require_once(PATH_CORE   . 'database.inc.php');
	require_once(PATH_CORE   . 'setup.inc.php');  			
	require_once(PATH_CORE   . 'vars.inc.php'); 

	$C_debug 	= new CORE_debugger;
	$C_vars 	= new CORE_vars;
	$VAR        = $C_vars->f;
	$C_db       = &DB();
	$C_setup 	= new CORE_setup; 
	 
	$SERVICE_URL = 	$VAR['DIRECTI_URL'];
	$DEBUG 		 = 	$VAR['DIRECTI_DEBUG']; 
	$domainHash	 = 	Array($VAR['DOMAIN_NAME'] => $VAR['TERM']);
	$nsHash		 = 	Array($VAR['NS1'] => $VAR['NS2']);
	$USERNAME	 =	$VAR['DIRECTI_USERNAME'];	 
	$PASSWORD	 =	$VAR['DIRECTI_PASSWORD'];				 
	$PARENTID	 =	$VAR['DIRECTI_PARENTID']; 
	$LIB_DIR	 =  "";						 
	$iserror 	 = 	false;
	 
	require_once($LIB_DIR."domorder.class.php");
	require_once($LIB_DIR . "customer.class.php");				 
	require_once($LIB_DIR."response.class.php");			 
	
	$DomOrder 	= new DomOrder($LIB_DIR . "wsdl/domain.wsdl");	// Creating an instance of DomOrder by passing wsdl url.
	$Customer 	= new Customer($LIB_DIR . "wsdl/customer.wsdl");	// Creating an instance of DomOrder by passing wsdl url.
	  
	// create/get the accounts id:
 	$return = $Customer->getCustomerId($USERNAME,$PASSWORD,"reseller","en",$PARENTID,$VAR['ACCT_USER']); 	
	
	if(is_array($return))
	{
		# add account
		$return  = $Customer->addCustomer($USERNAME,$PASSWORD,"reseller","en",$PARENTID,
											$VAR['ACCT_USER'],
											$VAR['ACCT_PASS'],
											$VAR['ACCT_NAME'],
											$VAR['ACCT_ADDR'],
											"",
											"",
											"",
											$VAR['ACCT_CITY'],
											$VAR['ACCT_STATE'],
											$VAR['ACCT_COUNTRY'],
											$VAR['ACCT_ZIP'],
											"01","8885551212","01","8885551212","01","8885551212","en");

	}
   
	// Register domain
	if(!is_array($return))
	{	
		$account = $return;
    	$return = $DomOrder->registerDomain($USERNAME,$PASSWORD,"reseller","en",$PARENTID,$domainHash,$nsHash,$account,$account,$account,$account,$account,'NoInvoice');	
	}
	 
	$response = new Response($return);
	
	// Status
	if(@$return['status'] == 'Success')
	echo 'REGISTER SUCCESS!';
	 		
	// Common Output for all above functions. 
	print "<BR><b>Output</b><br><br>";
	if($response->isError())
	{
		$response->printError();
	}
	else
	{
		$result = $response->getResult();
		$response->printData($result);
	} 

?> 