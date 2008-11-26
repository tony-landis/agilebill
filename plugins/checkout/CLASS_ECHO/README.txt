README.txt

1. Instructions
2. Description of fields
3. Support policy
4. License

-------------------------------------------------------------------------------
1. Instructions
-------------------------------------------------------------------------------

Please view readme_example.html for additional instructions.

-------------------------------------------------------------------------------
2. Description of fields
-------------------------------------------------------------------------------

// The description of these fields can be found at https://wwws.echo-inc.com/ISPGuide-Interface.asp
	
$order_type    
$transaction_type
$merchant_echo_id
$merchant_pin
$isp_echo_id
$isp_pin
$billing_ip_address
$billing_prefix
$billing_name
$billing_first_name
$billing_last_name
$billing_company_name
$billing_address1
$billing_address2
$billing_city
$billing_state
$billing_zip
$billing_country
$billing_phone
$billing_fax
$billing_email
$cc_number
$ccexp_month
$ccexp_year
$counter
$debug
$ec_account
$ec_account_type
$ec_payment_type
$ec_address1
$ec_address2
$ec_bank_name
$ec_city
$ec_email
$ec_first_name
$ec_id_country
$ec_id_exp_mm
$ec_id_exp_dd
$ec_id_exp_yy
$ec_id_number
$ec_id_state
$ec_id_type
$ec_last_name
$ec_other_name
$ec_payee
$ec_rt
$ec_serial_number
$ec_state
$ec_transaction_dt
$ec_zip
$grand_total
$merchant_email
$merchant_trace_nbr
$original_amount
$original_trandate_mm
$original_trandate_dd
$original_trandate_yyyy
$original_reference
$product_description
$purchase_order_number
$sales_tax
$track1
$track2
$EchoSuccess      // if this is true, it will send the order to ECHOnline
$cnp_recurring
$cnp_security  
	
// These variables are used after a transaction has been submitted.	
// You always get back all 3 responses
$EchoResponse     // Show All 3 ECHOTYPE responses
$echotype1        // Show ECHOTYPE 1 response
$echotype2        // Show ECHOTYPE 2 response - HTML format
$echotype3        // Show ECHOTYPE 3 response - XML format
		
// ECHOTYPE3 results
$authorization
$order_number
$reference
$status
$avs_result
$security_result
$mac                
$decline_code       
$tran_date          
$merchant_name      
$version

-------------------------------------------------------------------------------
3. Support policy
-------------------------------------------------------------------------------

The software on Openecho.com, ECHOpay.com and ECHOcart.com is designed 
for developers and programmers to use in programming payment processing on 
Web sites or through the Internet.  It is designed to operate with the ECHOnline 
payment gateway.  

All OpenECHO software has been tested and proven operational, and other users 
have successfully set it up on their platforms.  However, this cannot be guaranteed 
for every platform, and we do not warrant that it will work on any other system.  It is 
offered as is and with no warranty.  

Although all the software has been tested, we make no representations as to how 
or whether it can or cannot be utilized with a merchant's specific front-end software 
(shopping cart, payment form, etc.).  It is our policy that the responsibility for setting 
up the software lies with the merchant and/or the merchant's programmer to 
assure the functionality of the software in payment processing for any given Web 
site. 

The potential for problems in creating and/or implementing software are extensive, 
usually requiring the assistance of expert programmers to resolve. There are many 
possible conflicts between shopping cart software, web hosting servers, remote 
and/or virtual servers, and various server programs utilized by hosting companies, 
etc.   It is not ECHO's policy to offer application debugging, or code-level 
programming support.   It is our policy that such programming challenges are the 
responsibility of the merchant and/or the merchant's programmer.   

Questions

Questions can be sent to ECHO for general assistance via our developer support e-
mail as found at developer-support@echo-inc.com.  Our staff will assist with your 
questions, providing answers when they can.  In cases where we have an 
understanding of your PC or Web site environment and the issues involved, we 
provide specific answers and solutions.  In other cases, we can help identify the part 
of the process where the problem resides, but we may not necessarily know the 
exact remedy.  In other cases, we may not have the knowledge to troubleshoot 
problems with problems residing outside the ECHO environment.


-------------------------------------------------------------------------------
4. License
-------------------------------------------------------------------------------

In case of any license issues related to OpenECHO
please contact developer-support@echo-inc.com

OpenECHO License

* Copyright (c) 2002-2003 The OpenECHO Project.  All rights reserved.
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:

* I. Redistributions of source code must retain the above copyright
  notice, this list of conditions and the following disclaimer.
	  
* II. Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in
  the documentation and/or other materials provided with the
  distribution.

* III. All advertising materials mentioning features or use of this
  software must display the following acknowledgment:
  "This product includes software developed by the OpenECHO Project.
  (http://www.openecho.com)"

* IV. The names "OpenECHO" must not be used to endorse or promote products
  derived from this software without prior written permission. For
  written permission, please contact developer-support@echo-inc.com

* V. Products derived from this software may not be called "OpenECHO"
  nor may "OpenECHO" appear in their names without prior written
  permission of the OpenECHO Project.

* VI. Redistributions of any form whatsoever must retain the following
  acknowledgment:
  "This product includes software developed by the OpenECHO Project
  (http://www.openecho.com)"

* THIS SOFTWARE IS PROVIDED BY THE OpenECHO PROJECT ``AS IS'' AND ANY
* EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
* PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE OpenECHO PROJECT OR
* ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
* NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
* HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
* OF THE POSSIBILITY OF SUCH DAMAGE.