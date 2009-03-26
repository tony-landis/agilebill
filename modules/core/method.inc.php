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
	
class CORE_method
{
	function do_all()
	{
		global $C_auth, $C_sess, $C_debug, $C_translate, $VAR;
		if(empty($VAR['do'])) return;

		for($i=0; $i < count($VAR['do']); $i++)
		{
			if(!empty($VAR['do'][$i]))
			{
				if(preg_match("/:/", $VAR['do'][$i]))
				{
					$identifier = explode(':',$VAR['do'][$i]);
					$module = $identifier[0];
					$method = strtolower($identifier[1]);   		                
					$C_translate->value['core']['module_name'] = '<b><u>'. $module.":".$method . '</u></b>';
					if	(
							$module  != '' 		&&
							$method  != ''    	&&
							gettype($module) ==  'string' &&
							gettype($method) ==  'string'
						)
					{
						if($C_auth->auth_method_by_name($module,$method))
						{
							if (file_exists(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php'))
							{ 
								include_once(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php');
								if(class_exists($module))
								{
									$eval = '$' . $module . ' = new ' . $module . '(); ';
									$eval .= '$this_Obj  = $' . $module . ';';
									$eval .= '$this_Obj->' . $method . '($VAR,$this_Obj);';
									eval ($eval);
									#call_user_func (array($module, "$method"), $VAR, $this_Obj);	
								}
								else
								{
									$C_debug->alert($C_translate->translate('method_non_existant','core',''));
								}
							}
							else
							{
								$C_debug->alert($C_translate->translate('module_non_existant','core',''));
							}
						}
						else
						{
						   $C_debug->alert($C_translate->translate('module_non_auth','core',''));
						}                
					}
					else
					{
						$C_debug->alert($C_translate->translate('method_invalid','core',''));
					}
				}
				else
				{
					$C_debug->alert($C_translate->translate('method_invalid','core',''));
				}
			}
		}
	}


	function exe($module,$method)
	{	
		global $C_auth, $C_sess, $C_debug, $C_translate, $VAR;
		$C_translate->value['core']['module_name'] = $module.":".$method;
		if	(
			$module  != '' 		&&
			$method  != ''    	&&
			gettype($module) ==  'string' &&
			gettype($method) ==  'string'
			)
		{
			if($C_auth->auth_method_by_name($module,$method))
			{
				if (file_exists(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php'))
				{
					include_once(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php');
					if(class_exists($module))
					{                                                          
						$eval = '$' . $module . ' = new ' . $module . '; ';
						$eval .= '$this_Obj  = $' . $module . ';';
						$eval .= '$this_Obj->' . $method . '($VAR,$this_Obj);';
						eval ($eval);
						#call_user_func (array($module, "$method"), $VAR, $this_Obj);
						global $smarty;
						$smarty->assign_by_ref("return", $account);	
						$this->result 	= TRUE;   
						$this->error	= FALSE;
					}
					else
					{
						$this->result = FALSE; 
						$this->error  =  $C_translate->translate('method_non_existant','core','');
						return;
					}
				}
				else
				{
					$this->result = FALSE; 
					$this->error = $C_translate->translate('module_non_existant','core','');
					return;		
				}
			}
			else
			{
				$this->result = FALSE; 
				$this->error = $C_translate->translate('module_non_auth','core','');	
				return;			
			}
		}
		else
		{
			$this->result = FALSE;
			$this->error = $C_translate->translate('method_invalid','core','');
			return;
		}
	}			



	function exe_noauth($module,$method)
	{	
		global $C_auth, $C_sess, $C_debug, $C_translate, $VAR;
		if	(
			$module  != '' 		&&
			$method  != ''    	&&
			gettype($module) ==  'string' &&
			gettype($method) ==  'string'
			)
		{
				if (file_exists(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php'))
				{
					include_once(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php');
					if(class_exists($module))
					{
						$eval = '$' . $module . ' = new ' . $module . '; ';
						$eval .= '$this_Obj  = $' . $module . ';';
						$eval .= '$this_Obj->' . $method . '($VAR,$this_Obj);';
						eval ($eval);
						#call_user_func (array($module, "$method"), $VAR, $this_Obj);
						global $smarty;
						$smarty->assign_by_ref("return", $account);	
						$this->result 	= TRUE;
						$this->error	= FALSE;
					}
					else
					{
						$this->result = FALSE;
						$this->error  =  $C_translate->translate('method_non_existant','','');
						return;
					}
				}
				else
				{
					$this->result = FALSE;
					$this->error = $C_translate->translate('module_non_existant','','');
					return;		
				}
		}
		else
		{
			$this->result = FALSE;
			$this->error = $C_translate->translate('method_invalid','','');
			return;
		}
   }
}
?>