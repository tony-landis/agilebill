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
	
/**
 * Test Discount Plugin Class
 */
class plgn_discount_test
{
	/**
	 * This variable holds the actual discount code that has been entered in the 
	 * Admin area under Accounts > Discounts. When the validation routine for this plugin
	 * is called and suceeds, this discount code will be added to the user's session
	 * for normal discount calculation.
	 *
	 * @var string
	 */
	var $discount_code='TEST-12346';
	
	
	/**
	 * This function is called when the user enters a discount code not
	 * passing the normal checks against the discount records in the database.
	 *  
	 * @param string $discount The discount code entered by the user
	 * @return string Return false or $this->discount_code if success
	 */
	function validate($discount) {  
		if(eregi("^test", $discount))
			return $this->discount_code; // success
		else
			return false; // validation failed
	}
}
?>