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
	
function list_select_groups($default, $field_name, $class, $size, $own_account)
{
	global $C_auth;	

	# get the default group	
	if(!isset($default) && $default == "")
	{
		$default = Array(DEFAULT_GROUP);
	}
	else if(gettype($default) == 'array')
	{
		#$default = $default;
	}
	else if(gettype($default) == 'string')
	{
	  $default = unserialize($default);
	}


	for($i=0; $i<count($default); $i++) 
		$checked[$default[$i]] = true;	

	# get the currect selected value & display
	$db = &DB();
	$sql= "SELECT id,name,parent_id FROM ".AGILE_DB_PREFIX."group 
			WHERE id != '0' AND site_id = '" . DEFAULT_SITE . "' 
			ORDER BY parent_id,name";
	$result = $db->Execute($sql); 
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('list.inc.php','select_groups', $db->ErrorMsg());
	}		

	# number of results
	if($result->RecordCount() > 0)
	{

		# set the results to an array:
		$arr = $result->GetArray();	

		# start the list
		$ret = '';

		#----------------------
		# start the parent loop
		#----------------------
		$group = 0;
		$arr_count = count($arr); 		
		for($i=0; $i < $arr_count; $i++)
		{

		####################
		### Is auth?
		if(!$C_auth->auth_group_by_id($arr[$i]['id']))
		$disabled = ' disabled';
		else
		$disabled = '';

		#####################

			$level = 0;
			if($arr[$i]['parent_id'] == $group)
			{
				#if($own_account && $checked[$arr[$i]['id']] == true) $disabled = ' disabled';

				$ret .= '<input type="checkbox" name="'.$field_name.''.$id.'[]" value="'.$arr[$i]['id'].'"';
				if($checked[$arr[$i]['id']] == true) $ret .= ' checked';
				$ret .= $disabled . '>&nbsp;&nbsp;'. $arr[$i]['name'] .'<br>';

				#----------------------
				# start the child loop
				#----------------------
				$level++;
				$ii_group = $arr[$i]['id'];
				$ii_print = 1;

				# count the available childs for this group
				$count_child[$ii_group]=0;
				for($c_child=0; $c_child < $arr_count; $c_child++)
					if($arr[$c_child]['parent_id'] == $ii_group) $count_child[$ii_group]++;

				for($ii=0; $ii < $arr_count; $ii++)
				{

			################
			### Is auth?
			if(!$C_auth->auth_group_by_id($arr[$ii]['id']))
			$disabled = ' disabled';
			else
			$disabled = '';
			#################

					if($arr[$ii]['parent_id'] == $ii_group)
					{
						#if($own_account && $checked[$arr[$ii]['id']] == true) $disabled = ' disabled';

						$ret .= '&nbsp;&nbsp;|__';
						$ret .= '<input type="checkbox" name="'.$field_name.''.$id.'[]" value="'.$arr[$ii]['id'].'"';
						if($checked[$arr[$ii]['id']] == true) $ret .= ' checked';
						$ret .= $disabled . '>&nbsp;&nbsp;'. $arr[$ii]['name'] .'<br>';

						$ii_print++;

						#--------------------------
						# start the sub-child loop
						#--------------------------
						$level++;
						$iii_group = $arr[$ii]['id'];
						$iii_print = 0;
						for($iii=0; $iii < $arr_count; $iii++)
						{

			################
			### Is auth?
			if(!$C_auth->auth_group_by_id($arr[$iii]['id']))
			$disabled = ' disabled';
			else
			$disabled = '';
			#################

							if($arr[$iii]['parent_id'] == $iii_group)
							{
								#if($own_account && $checked[$arr[$iii]['id']] == true) $disabled = ' disabled';

								if($count_child[$ii_group] < $ii_print)
								{
									$ret .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__ ';
								}
								else
								{
									$ret .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__ ';
								}
								$ret .= '<input type="checkbox" name="'.$field_name.''.$id.'[]" value="'.$arr[$iii]['id'].'"';
								if($checked[$arr[$iii]['id']] == true) $ret .= ' checked';
								$ret .= $disabled . '>&nbsp;&nbsp;'. $arr[$iii]['name'] .'<br>';
								$iii_print++;
							}	
						}
						$level--;	
						#-----------------------
						# end of sub-child loop
						#-----------------------

					}	
				}
				$level--;	
				#-------------------
				# end of child loop
				#-------------------
			}
		}		    		
	}	
	else
	{
		return 'No groups available!'; // translate!
	}
	return $ret;     	 			
}
?>