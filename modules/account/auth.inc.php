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

$auth_methods = Array
(
	Array ('module' => 'account',               'method' => 'add'),
	Array ('module' => 'account',               'method' => 'user_add'),
	Array ('module' => 'account',               'method' => 'user_view'),
	Array ('module' => 'account',               'method' => 'add'),
	Array ('module' => 'account',               'method' => 'view'),
	Array ('module' => 'account',               'method' => 'update'),
	Array ('module' => 'account',               'method' => 'password'),
	Array ('module' => 'account',               'method' => 'password_reset'),
	Array ('module' => 'account',               'method' => 'static_var'),
	Array ('module' => 'account',               'method' => 'verify'),
	Array ('module' => 'account',               'method' => 'verify_resend'),
	Array ('module' => 'account', 				'method' => 'sub_account_add'),
	Array ('module' => 'account', 				'method' => 'sub_delete')
);

?>