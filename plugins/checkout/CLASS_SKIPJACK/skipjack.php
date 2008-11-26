<?

// -----------------------------------------------------------------------------
// Skipjack PHP Interface
//
// Original version written by Greg MacLellan   
// Online Creator Inc   May 11, 2001
//
// This script requires the cURL Library for PHP
//
// It was tested on Apache 1.3.19 + PHP 4.0.5 + OpenSSL 0.9.6 + libcURL 7.7.3
//
// New version: July 10, 2004 - Ashbec LLC
// Syntax errors removed / debugged for updated environment:
//		Linux
//		Apache 1.3.28 / PHP 4.3.3 / cURL 7.10.5 / OpenSSL 0.9.6b / zlib 1.1.4
//		- fixed syntax error in define 
//		- initialized $str before parsing
//		- added new szReturnCode/Message values (-96 thru -100)
//		- added current AVS return codes, handled unknown codes
//		Only SkipJack_Authorize and Change_Status have been tested in the new environment.
//		Status has not been tested.
//
// New version: November 29, 2004 - Ashbec LLC
//      - Added code to gracefully?? exit if there is no response from Skipjack
//          (Fakes a communications error.)
//      - Set 60 second timeout on cURL execution
//
//	Copyright 2004, Ashbec LLC.  Rights to distribute and use without charge is hereby granted.
//		Right to sell this version of the software is expressly reserved to Ashbec LLC.
//
// function SkipJack_Authorize($request)
// function SkipJack_Status($request)
// function SkipJack_ChangeStatus($request)
// -----------------------------------------------------------------------------
//
// protocol + host for the server
// define("SJPHPAPI_ROOT_URL", "https://developer.skipjackic.com"); // test
// define("SJPHPAPI_ROOT_URL", "https://www.skipjackic.com"); // production
// -----------------------------------------------------------------------------

function SkipJack_Authorize($request) {
	$skipjackurl = SJPHPAPI_ROOT_URL."/scripts/evolvcc.dll?Authorize";

	$ch = curl_init(); // initalize cURL
	curl_setopt($ch, CURLOPT_URL, $skipjackurl); // connect to skipjack
	
	// special processing:
	// format the price  "5352.20" => "535220"
/* 	$request["Transactionamount"] = number_format($request["Transactionamount"], 2, "", "");  (doesn't work) */

	// take the $request array and turn it into name=value&name=value pairs
	if (count($request) > 0) {
		reset($request);
		$str = NULL;
		while (list($name, $value) = each($request)) {
			$str .= "&".$name."=".$value;
		}
		$str = substr($str,1);
	}

	curl_setopt($ch, CURLOPT_POST, 1); // we're doing a post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $str); // name=value pairs from above
    curl_setopt($ch, CURLOPT_USERAGENT, "SJ-PHP-API (Ashbec LLC)");
	
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return results
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // max time

	$results = curl_exec ($ch); // connect and grab the results
	
	curl_close ($ch);
	
	$ReturnValues = array("AUTHCODE",
	                      "szSerialNumber",
					  "szTransactionAmount",
					  "szAuthorizationDeclinedMessage",
					  "szAVSResponseCode",
					  "szAVSResponseMessage",
					  "szOrderNumber",
					  "szAuthorizationResponseCode",
					  "szIsApproved",
					  "szCVV2ResponseCode",
					  "szCVV2ResponseMessage",
					  "szReturnCode"	                      
					  );
	
	$szReturnCode = array("1"=>"Status Complete (1)",
	                      "0"=>"Call Failed (0)",
					  "-1"=>"Invalid length (-1)",
	                  "-35"=>"Invalid credit card number (-35)",
					  "-37"=>"Failed communication (-37)",
					  "-39"=>"Serial number is too short (-39)",
					  "-51"=>"The zip code is invalid",
					  "-52"=>"The shipto zip code is invalid",
					  "-53"=>"Length of expiration date (-53)",
					  "-54"=>"Length of account number date (-54)",
					  "-55"=>"Length of street address (-55)",
					  "-56"=>"Length of shipto street address (-56)",
					  "-57"=>"Length of transaction amount (-57)",
					  "-58"=>"Length of name (-58)",
					  "-59"=>"Length of location (-59)",
					  "-60"=>"Length of state (-60)",
					  "-61"=>"Length of shipto state (-61)",
					  "-62"=>"Length of order string (-62)",
					  "-64"=>"Invalid phone number (-64)",
					  "-65"=>"Empty name (-65)",
					  "-66"=>"Empty email (-66)",
					  "-67"=>"Empty street address (-66)",
					  "-68"=>"Empty city (-68)",
					  "-69"=>"Empty state (-69)",
					  "-70"=>"Empty zip code (-70)",
					  "-71"=>"Empty order number (-71)",
					  "-72"=>"Empty account number (-72)",
					  "-73"=>"Empty expiration month (-73)",
     				  "-74"=>"Empty expiration year (-74)",
     				  "-75"=>"Empty serial number (-75)",
     				  "-76"=>"Empty transaction amount (-76)",
     				  "-79"=>"Length of customer name (-79)",
     				  "-80"=>"Length of shipto customer name (-80)",
     				  "-81"=>"Length of customer location (-81)",
					  "-82"=>"Length of customer state (-82)",
					  "-83"=>"Length of shipto phone (-83)",
					  "-84"=>"Pos Error duplicate ordernumber (-84)",
					  "-91"=>"Pos Error CVV2 (-91)",
					  "-92"=>"Pos Error Approval Code (-92)",
					  "-93"=>"Pos Error Blind Credits Not Allowed (-93)",
					  "-94"=>"Pos Error Blind Credits Failed (-94)",
					  "-95"=>"Pos Error Voice Authorizations Not Allowed (-95)",
					  "-96"=>"Voice Authorization Failed (-96)",
					  "-97" => "Fraud Rejection - rule violation (-97)",
					  "-98" => "Invalid Discount Amount (-98)",
					  "-99" => "Invalid Pin Block (-99)",
					  "-100" => "Invalid Key Serial Number (-100)"
					  );

 	$szAVSResponse = array("X" => "Exact match, 9 digit zip",
	                       "Y" => "Exact match, 5 digit zip",
						   "M" => "Exact address match, international.",
						   "D" => "Exact address match, international.",
						  "A" => "Address matches, ZIP code does not",
						  "B" => "Address match without postal code, international.",
						  "W" => "ZIP Code (9) matches, address does not",
						  "Z" => "ZIP Code (5) matches, address does not",
						  "P" => "Postal code match only, international.",
						  "N" => "No address or zip match",
						  "U" => "Address verification unavailable",
						  "I" => "Address information not verified by issuer, international.",
						  "R" => "Retry - Issuer system unavailable or timed out",
						  "E" => "Error - AVS data is invalid",
						  "C" => "Incompatible address format, international.",
						  "G" => "Non-U.S. Issuer does not participate in AVS (verification unavailable)",
						  "S" => "Service not supported by US issuing Bank"
						  );  


	// parse through results for $ReturnValues in "<!--ReturnValue=value-->"		
	
	if (empty($results)) {	// No response from SkipJack.  Fake an error.
		$response['AUTHCODE'] = NULL;
		$response['szSerialNumber'] = NULL;
		$response['szTransactionAmount'] = NULL;
		$response['szAuthorizationDeclinedMessage'] = "No response from SkipJack financial network.";
		$response['szAVSResponseCode'] = "R";
		$response['szAVSResponseMessage'] = "No response from SkipJack financial network.";
		$response['szOrderNumber'] = NULL;
		$response['szAuthorizationResponseCode'] = 0;
		$response['szIsApproved'] = 0;
		$response['szCVV2ResponseCode'] = "P";
		$response['szCVV2ResponseMessage'] = "Not processed";
		$response['szReturnCode'] = -37;
	} else { // Parse the real results
		while (list($key,$code) = each($ReturnValues)) {
			$pos = strpos($results, $code);
			if ($pos) {
				$value = substr($results, $pos + strlen($code) + 1, strpos($results, "-->", $pos) - $pos - strlen($code) - 1);
				$response[$code] = $value;
			}
		}
	}
	
	// a couple extra response strings
	if (!empty($szAVSResponse[$response['szAVSResponseCode']])) {
		$response["textAVSResponseCode"] = $szAVSResponse[$response['szAVSResponseCode']];
	} else {
		$response["textAVSResponseCode"] = "Unknown AVS code: " . $response['szAVSResponseCode'];
	}
	if (!empty ($szReturnCode[$response["szReturnCode"]])) {
		$response["textReturnCode"] = $szReturnCode[ $response["szReturnCode"] ];
	} else {
		$response["textReturnCode"] = "Unknown return code: " . $response['szReturnCode'];
	}
	
	return $response;
}

function SkipJack_Status($request) {
	$skipjackurl = SJPHPAPI_ROOT_URL."/scripts/evolvcc.dll?SJAPI_TransactionStatusRequest";

	$ch = curl_init(); // initalize cURL
	curl_setopt($ch, CURLOPT_URL, $skipjackurl); // connect to skipjack 

	// take the $request array and turn it into name=value&name=value pairs
	$str = NULL;
	while (list($name, $value) = each($request)) {
		$str .= "&".$name."=".$value;
	}
	$str = substr($str,1);

	curl_setopt($ch, CURLOPT_POST, 1); // we're doing a post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $str); // name=value pairs from above
    curl_setopt($ch, CURLOPT_USERAGENT, "SJ-PHP-API (Ashbec LLC)");
	
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return results
 
	$results = curl_exec ($ch); // connect and grab the results
	
	curl_close ($ch);
	
	// fields in response record
	$responseRecFields = array("SerialNumber",
	                           "ErrorCode",
						  "NumRecs");
						  
	// response record error codes
	$responseRecErrorCodes = array("0"=>"Success",
	                               "-1"=>"Invalid Command",
	                               "-2"=>"Parameter Missing",
	                               "-3"=>"Failed retrieving response",
	                               "-4"=>"Invalid Status",
	                               "-5"=>"Failed reading security tags",
	                               "-6"=>"Developer serial number not found",
	                               "-7"=>"Invalid Serial Number",
	                               "-8"=>"Expiration year not four characters",
	                               "-9"=>"Credit card expired",
	                               "-10"=>"Invalid starting date (recurring payment)",
	                               "-11"=>"Failed adding recurring payment",
	                               "-12"=>"Invalid Frequency (recurring payment)");
	
	// fields in status records
	$responseFields = array("SerialNumber",
	                        "Amount",
					    "TransStatusCode",
					    "TransStatusMsg",
					    "OrderNumber",
					    "TransactionDate",
					    "TransactionID");
	
	$StatusText = array("0"=>"Idle",
	                    "1"=>"Authorized", 
	                    "2"=>"Denied", 
	                    "3"=>"Settled", 
	                    "4"=>"Credited", 
	                    "5"=>"Deleted", 
	                    "6"=>"Archived", 
	                    "7"=>"Pre-Auth");
					
	$PendingStatusText = array("0"=>"Idle",
	                           "1"=>"Pending Credit", 
	                           "2"=>"Pending Settlement", 
	                           "3"=>"Pending Delete", 
	                           "4"=>"Pending Authorization", 
	                           "5"=>"* Pending Settlement"); 
					 

	// first, we get the response record
	$temp = substr($results, 0, strpos($results,"\n") - 1);
	while (list($key,$val) = each($responseRecFields)) {
		$firstquote = strpos($temp,"\"");
		$secondquote = strpos($temp, "\"", $firstquote + 1);
		
		$responserecord[$val] = substr($temp, $firstquote + 1, $secondquote - 1 - $firstquote);
		
		$temp = substr($temp, $secondquote + 1);     
	}
	
	// additional text messages
	$responserecord["textErrorCode"] = $responseRecErrorCodes[$responserecord["ErrorCode"]];
	

	
	// get just the results into this string
	$results = substr($results, strpos($results, "\n") + 1);
	
	// if we didn't get some error	
	if ($responserecord["ErrorCode"] == 0) {

		// parse through results and create array
		$i = 0;
		while (strlen($results) > 0) {
			$temp = substr($results, 0, strpos($results,"\n") - 1);
			$results = substr($results, strpos($results, "\n") + 1);
	
			// parse through individual string and create array
			reset($responseFields);
			while (list($key,$val) = each($responseFields)) {
				$firstquote = strpos($temp,"\"");
				$secondquote = strpos($temp, "\"", $firstquote + 1);
				
				$response[$i][$val] = substr($temp, $firstquote + 1, $secondquote - 1 - $firstquote);
				
				$temp = substr($temp, $secondquote + 1);     
			}
			
			// create additional text responses
			$response[$i]["intStatus"] = substr($response[$i]["TransStatusCode"],0,1);
			$response[$i]["textStatus"] = $StatusText[ $response[$i]["intStatus"] ];
			$response[$i]["intPendingStatus"] = substr($response[$i]["TransStatusCode"],1,1);
			$response[$i]["textPendingStatus"] = $PendingStatusText[ $response[$i]["intPendingStatus"] ];
			
			$i++;
		}
		
		return array("Status"=>$responserecord, "Response"=>$response);
	} else {
		return array("Status"=>$responserecord, "Text"=>$results);
	}	
}


function SkipJack_ChangeStatus($request) {
	$skipjackurl = SJPHPAPI_ROOT_URL."/scripts/evolvcc.dll?SJAPI_TransactionChangeStatusRequest";

	$ch = curl_init(); // initalize cURL
	curl_setopt($ch, CURLOPT_URL, $skipjackurl); // connect to skipjack 

	// take the $request array and turn it into name=value&name=value pairs
	$str = NULL;
	while (list($name, $value) = each($request)) {
		$str .= "&".$name."=".$value;
	}
	$str = substr($str,1);

	curl_setopt($ch, CURLOPT_POST, 1); // we're doing a post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $str); // name=value pairs from above
     curl_setopt($ch, CURLOPT_USERAGENT, "SJ-PHP-API (OnlineCreator)");
	
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return results
 
	$results = curl_exec ($ch); // connect and grab the results
	
	curl_close ($ch);
	
	// fields in response record
	$responseRecFields = array("SerialNumber",
	                           "ErrorCode",
						  "NumRecs");
						  
	// response record error codes
	$responseRecErrorCodes = array("0"=>"Success",
	                               "-1"=>"Invalid Command",
	                               "-2"=>"Parameter Missing",
	                               "-3"=>"Failed retrieving response",
	                               "-4"=>"Invalid Status",
	                               "-5"=>"Failed reading security tags",
	                               "-6"=>"Developer serial number not found",
	                               "-7"=>"Invalid Serial Number",
	                               "-8"=>"Expiration year not four characters",
	                               "-9"=>"Credit card expired",
	                               "-10"=>"Invalid starting date (recurring payment)",
	                               "-11"=>"Failed adding recurring payment",
	                               "-12"=>"Invalid Frequency (recurring payment)");
	
	// fields in status records
	$responseFields = array("SerialNumber",
	                        "Amount",
					    "DesiredStatus",

					    "StatusResponse",
					    "StatusResponseMsg",
					    "OrderNumber",
					    "TransactionID");
	
	// so we can return a 1/0 response code
	$SuccessCodes = array("SUCCESSFUL"=>"1",
	                      "UNSUCCESSFUL"=>"0", 
 	                      "NOTALLOWED"=>"0");
					
	// first, we get the response record
	$temp = substr($results, 0, strpos($results,"\n") - 1);
	while (list($key,$val) = each($responseRecFields)) {
		$firstquote = strpos($temp,"\"");
		$secondquote = strpos($temp, "\"", $firstquote + 1);
		
		$responserecord[$val] = substr($temp, $firstquote + 1, $secondquote - 1 - $firstquote);
		
		$temp = substr($temp, $secondquote + 1);     
	}
	
	// additional text messages
	$responserecord["textErrorCode"] = $responseRecErrorCodes[$responserecord["ErrorCode"]];


	// get just the results into this string
	$results = substr($results, strpos($results, "\n") + 1);

	// if we didn't get some error	
	if ($responserecord["ErrorCode"] == 0) {

		// parse through results and create array
		// with ChangeStatusRequest, this is usually only one value,
		// but for consistency, we'll handle more if we need to
		$i = 0;
		while (strlen($results) > 0) {
			$temp = substr($results, 0, strpos($results,"\n") - 1);
			$results = substr($results, strpos($results, "\n") + 1);
			
			// parse through individual string and create array
			reset($responseFields);
			while (list($key,$val) = each($responseFields)) {
				$firstquote = strpos($temp,"\"");
				$secondquote = strpos($temp, "\"", $firstquote + 1);
				
				$response[$i][$val] = substr($temp, $firstquote + 1, $secondquote - 1 - $firstquote);
				
				$temp = substr($temp, $secondquote + 1);     
			}
			
			// create additional text responses
			$response[$i]["intSuccess"] = $SuccessCodes[ $response[$i]["StatusResponse"] ];
			
			$i++;
		}
		return array("Status"=>$responserecord, "Response"=>$response);
	} else {
		return array("Status"=>$responserecord, "Text"=>$results);
	}	
	
}



/*

// sample transaction:

$request = array("sjname" => "Skipjack PHP Test",
                         "Email" => "Transaction@skipjack.com",
                         "Streetaddress" => "2230 Park Ave",
                         "City" => "Cincinnati",
                         "State" => "OH",
                         "Zipcode" => "45206",
                         "Country" => "USA",
                         "Ordernumber" => "1PHP",
                         "Accountnumber" => "5121212121212124",
                         "Month" => "03",
                         "Year" => "2003",
                         "Serialnumber" => "xxxxxxxxxxxx",  // html Vital, NBova or production
                         "Transactionamount" => "3.45",
                         "Orderstring" => "1~Item 1~3.45~3~N~||",
                         "Shiptophone" => "888-368-8507");

echo "<pre>";
var_dump($request);
					
var_dump(SkipJack_Authorize($request));
echo "</pre>";

*/

/*

Sample Status Request:

$request["szSerialNumber"] = "xxxxxxxxxxxx";
$request["szDeveloperSerialNumber"] = "xxxxxxx";
//$request["szOrderNumber"] = "1PHP";
$request["szDate"] = "";

echo "<pre>";
var_dump($request);
echo "</pre><br>";

echo "<pre>";
var_dump ( SkipJack_Status($request) );
echo "</pre>";

echo "<br><hr><Br>";
*/


/*

Sample Status Change:

unset($request);

$request["szSerialNumber"] = "xxxxxxxxxxxx";
$request["szDeveloperSerialNumber"] = "xxxxxx";
$request["szOrderNumber"] = "1PHP";
$request["szDesiredStatus"] = "SETTLE";
$request["szForceSettlement"] = "1";

echo "<pre>";
var_dump($request);
echo "</pre><br>";

echo "<pre>";
var_dump ( SkipJack_ChangeStatus($request) );
echo "</pre><br>";
*/

?>
