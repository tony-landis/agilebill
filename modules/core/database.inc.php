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
	
class CORE_database
{
	function add($VAR, &$construct, $type)
	{		
		include_once(PATH_CORE . 'database_add.inc.php');
		return CORE_database_add($VAR, $construct, $type);
	}

	function update($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_update.inc.php');
		return CORE_database_update($VAR, $construct, $type); 		
	}

	function search_form($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_search_form.inc.php');
		return CORE_database_search_form($VAR, $construct, $type); 		 	
	}

	function search($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_search.inc.php');
		return CORE_database_search($VAR, $construct, $type);
	}

	function search_show($VAR, &$construct, $type)
	{ 
		include_once(PATH_CORE . 'database_search_show.inc.php');
		return CORE_database_search_show($VAR, $construct, $type);
	}	

	function view($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_view.inc.php');
		return CORE_database_view($VAR, $construct, $type);
	}		

	function mass_delete($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_mass_delete.inc.php');
		return CORE_database_mass_delete($VAR, $construct, $type);
	}	

	function delete($VAR, &$construct, $type)
	{
		include_once(PATH_CORE . 'database_delete.inc.php');
		return CORE_database_delete($VAR, $construct, $type);
	}

	function join_fields($result, $linked)
	{
		include_once(PATH_CORE . 'database_join_fields.inc.php');
		return CORE_database_join_fields($result, $linked);
	}		

	// replaced in v1.4.91 (use sqlSelect)
	function sql_select($TableList, $FieldList, $Conditions, $Order, &$db)  {   
		return sqlSelect( $db, $TableList, $FieldList, $Conditions, $Order); 
	}

	/**
	 * Remove fields from the standard construct type to ingore insert/select/validation rules set in construct
	 *
	 * @param array $ignore_fields
	 * @param string $construct_fields
	 * @return array
	 */
	function ignore_fields($ignore_fields,$construct_fields) {
		if(!is_array($construct_fields)) $fields = explode(",", $construct_fields); else $fields = $construct_fields;
		foreach($fields as $id=>$fld) {
			if(in_array($fld,$ignore_fields)) {
				unset($fields[$id]);
			}
		}
		return $fields;  		
	}
}


class CORE_debugger
{
	var $sql_count; 

	function sql_count() {
		if(!isset($this->sql_count)) $this->sql_count = 0;
		$this->sql_count++; 
	}

	function alert($message) {
		$this->alert = Array ($message);
	}

	function error($module, $method, $message) {				
		$this->error = $module . ':'. $method . ' => &nbsp;&nbsp ' . $message . '<br>';
		if(defined("ERROR_REPORTING") && ERROR_REPORTING > 0) $this->alert($this->error);
		$db = &DB();
		$this->record_id = $db->GenID(AGILE_DB_PREFIX . "" . 'log_error_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."log_error
				SET
				id         = ". $db->qstr($this->record_id).",
				date_orig  = ". $db->qstr(time()).",
				account_id = ". @$db->qstr(SESS_ACCOUNT).",
				module     = ". $db->qstr($module).",
				method     = ". $db->qstr($method).",
				message    = ". $db->qstr($message).",
				site_id    = ". @$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);         									
	}
} 


function &DB($debug=false) {
	static $saved_db_conn;

	if (isset($saved_db_conn) && defined("AGILE_DB_CACHE")) {
		#echo '<b>Cached:</b><pre>'.print_r($saved_db_conn,true).'</pre><br>';
		if($debug) $saved_db_conn->debug=true; else $saved_db_conn->debug=false;
		return $saved_db_conn;
	}

	$saved_db_conn = NewADOConnection(AGILE_DB_TYPE);
	if(defined("AGILE_DB_PCONNECT") && AGILE_DB_PCONNECT == true)
		$saved_db_conn->PConnect(AGILE_DB_HOST,AGILE_DB_USERNAME,AGILE_DB_PASSWORD,AGILE_DB_DATABASE);
	else
		$saved_db_conn->Connect(AGILE_DB_HOST,AGILE_DB_USERNAME,AGILE_DB_PASSWORD,AGILE_DB_DATABASE);
	#echo '<b>Original:</b><pre>'.print_r($saved_db_conn,true).'</pre><br>';   	 

	if($debug) $saved_db_conn->debug=true; else $saved_db_conn->debug=false; 
	return $saved_db_conn;		
} 

function sqlGenID(&$db, $table) {
	return $db->GenID( AGILE_DB_PREFIX . $table . '_id' );
}

function sqlConditions( &$db, $Conditions=false, $Tables=false )
{
	$where = " WHERE ";

	if($Conditions) {
		if(preg_match('/::/', $Conditions) ) {
			$s = explode('::', $Conditions);
			$ii=1;
			$Conditions = '';
			for($i=0; $i<count($s); $i++) {
				if($ii==1) {
					$Conditions .= " {$s[$i]} ";
					$ii++;
				} else {
					$Conditions .= $db->qstr($s[$i]);
					$ii=1;
				}
			}
		}
		$where .= $Conditions . " AND ";
	}

	if(!is_array($Tables)) {
		$where .= " site_id = ". DEFAULT_SITE;
	} else {
		$tbarr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S', 'T','U','V');
		for($i=0; $i<count($Tables); $i++) {
			if( $i > 0 ) $where .= " AND ";
			$where .= " {$tbarr[$i]}.site_id = ". DEFAULT_SITE;
		}
	} 

	if( $where ) return $where;
}

function sqlDelete(&$db, $table, $conditions) {
	$conditions = sqlConditions( $db, $conditions);
	return "DELETE FROM ".AGILE_DB_PREFIX."$table $conditions";
}

function sqlInsert(&$db, $table, $fields, $id=false) {
	if(!$id) $id = sqlGenID( $db,$table);
	$fields['id'] = $id;
	if(empty($fields['site_id'])) $fields['site_id'] = DEFAULT_SITE;
	$tab = AGILE_DB_PREFIX.''.$table;
	return $db->GetInsertSQL($tab, $fields, get_magic_quotes_gpc());
}

function sqlUpdate(&$db, $table, $fields, $conditions, $force=false) {
	$rs = $db->Execute(  sqlSelect( $db, $table, '*', $conditions) ); 
	if(empty($fields['site_id'])) $fields['site_id'] = DEFAULT_SITE;
	return $db->GetUpdateSQL( $rs, $fields, false, get_magic_quotes_gpc());
}

function sqlSelect(&$db, $TableList, $FieldList, $Conditions, $Order=false, $Limit=false, $DISTINCT='', $GroupBy=false )
{
	### Table(s)
	if(is_array($TableList)) {
		$tbarr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S', 'T','U','V');
		$table = '';
		$site_id_where = '';
		for($i=0;$i<count($TableList); $i++) {
			$as = $tbarr[$i];
			if($i>0) {
				$table .= ",".AGILE_DB_PREFIX.$TableList[$i] . " AS $as";
			} else  {
				$table .= AGILE_DB_PREFIX.$TableList[$i] . " AS $as";
			}
		}
	} else {
		$table = AGILE_DB_PREFIX.$TableList;
	}

	### Field(s)
	if(is_array($FieldList)) {
		$fields = '';
		for($i=0;$i<count($FieldList); $i++) {
			if($i>0)
			$fields .= ",".$FieldList[$i];
			else
			$fields .= $FieldList[$i];
		}
	} else {
		$fields = $FieldList;
	}

	### Condition(s)
	$where = sqlConditions( $db, $Conditions, $TableList);

	### Order By
	if(!empty($Order)) {
		$where .= " ORDER BY $Order ";
	}

	### Group By
	if(!empty($GroupBy)) {
		$where .= " GROUP BY $GroupBy ";
	}

	$where = str_replace('{p}', AGILE_DB_PREFIX, $where );

	if(!empty($DISTINCT)) $DISTINCT = 'DISTINCT';

	return "SELECT $DISTINCT $fields FROM $table $where";
}
?>