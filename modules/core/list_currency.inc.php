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
	
function list_format_currency ($number, $currency_id)
{
	if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
	if(isset($this->format_currency["currency_id"]))
	{
		return $this->format_currency["currency_id"]
				. "" . number_format($number, DEFAULT_DECIMAL_PLACE) . " "
				. $this->format_currency["currency_id"]["iso"];
	}
	else
	{
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'currency WHERE
				   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
				   id          = ' . $db->qstr($currency_id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$this->format_currency["currency_id"] = Array (
								'symbol' => $result->fields["symbol"],
								'iso'    => $result->fields["three_digit"]);                	
			return $result->fields["symbol"]
					. "" . number_format($number, DEFAULT_DECIMAL_PLACE) . " "
					. $result->fields["three_digit"];
		}
		else
		{
			return number_format($number, DEFAULT_DECIMAL_PLACE);
		}
	}
}


function list_currency_iso ($currency_id)
{
	if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
	if(isset($this->format_currency["currency_id"]))
	{
		return $this->format_currency["currency_id"]["iso"];
	}
	else
	{
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'currency WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($currency_id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
			return  $result->fields["three_digit"];
	}
}
?>