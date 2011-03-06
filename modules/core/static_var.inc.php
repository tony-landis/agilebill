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
	
class CORE_static_var
{ 
	##############################
	##	Generate Static Var List #
	##############################

	function generate_form($module, $method, $display)
	{
		global $VAR, $C_translate;
		include_once(PATH_CORE . 'validate.inc.php');

		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'
		### $Module is the module name
		### $Display is the display type (view or update) update allows for
		### user changes...

		$validate = false;
		if(isset($VAR['do'])  &&   gettype($VAR['do']) == 'array')
		{
			for($i=0; $i<count($VAR['do']); $i++)
			{
				if($VAR['do'][$i] == $module . ':' . $method) $validate = true;
			}
		}

		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return false;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			return false;
		}
		else
		{
			$i = 0;
			$C_validate = new CORE_validate;

			while (!$relation->EOF)
			{  
				################################################################
				### Get the primary settings for this field

				$id             = $relation->fields['id'];
				$static_var_id  = $relation->fields['static_var_id'];
				$default_value  = $relation->fields['default_value'];
				$description    = $relation->fields['description'];
				$required       = $relation->fields['required'];



				############################################################
				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);



				$format         = $var->fields['input_format'];
				$validation     = $var->fields['validation_type'];
				$convert        = $var->fields['convert_type'];
				$name           = $var->fields['name'];



				############################################################
				### Generate the field name, translate if it exists,
				### otherwise, just return the actual field name

				### $this_name      = $C_translate->translate('field_'.$name, $module, '');        		 	
				### if($this_name = '') $this_name = $name;
				$this_name = $name;


				############################################################
				### Determine the field value & name

				if(!isset($VAR["static_relation"]["$id"]) || $VAR["static_relation"]["$id"] == '')
				$static_value = $default_value;
				else
				$static_value = $VAR["static_relation"]["$id"];

				$static_relation = 'static_relation['.$id.']';



				############################################################
				### Determine The CSS Style to use...

				if($required == '1' && $validate == true)
				{
					if(!isset($VAR["static_relation"]["$id"]) || trim($VAR["static_relation"]["$id"]) == '')
					{
						$css = 'form_field_error';
					}
					else if ($validation != 'any'   &&   $validation != 'none'  &&   $validation != '')
					{
						$css = 'form_field_error';
						$css = 'form_field';
					}
					else
					{
						$css = 'form_field';
					}
				}
				else
				{
					$css = 'form_field';
				}

				$css_menu = 'form_menu';


				############################################################
				### Create the HTML

				if($format == 'small_text')
				{
					if($display == 'update')
					{
						### SMALL TEXT FIELD
						$this_html = '<input type="text" size="8" name="'.$static_relation.
						'" value="'.$static_value.'">';
					}
					elseif($display == 'search')
					{
						$this_html = '<input type="text" size="8" name="'.$static_relation.
									'" value="'.$static_value.'">' . 
									$C_translate->translate('search_partial', 'CORE', SESS_LANGUAGE);
					}
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'medium_text')
				{
					if($display == 'update')
					{
						### MEDIUM TEXT FIELD
						$this_html = '<input type="text" size="32" name="'.$static_relation.
						'" value="'.$static_value.'">';
					}
					elseif($display == 'search')
					{
						$this_html = '<input type="text" size="32" name="'.$static_relation.
									'" value="'.$static_value.'">' . 
									$C_translate->translate('search_partial', 'CORE', SESS_LANGUAGE);
					}                        
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'large_text')
				{
					if($display == 'update')
					{
						### LARGE TEXT FIELD
						$this_html = '<textarea name="'.$static_relation.'" cols="40" rows="5" >'.
						$static_value.'</textarea>';
					}
					elseif($display == 'search')
					{
						$this_html = '<input type="text" size="32" name="'.$static_relation.
									'" value="'.$static_value.'">' . 
									$C_translate->translate('search_partial', 'CORE', SESS_LANGUAGE);
					}                           
					else
					{
						$this_html = $static_value;
					}
				}




				else if ($format == 'dropdown_list')
				{ 
					if( $display == 'update')
					{ 
						$this_html = '<select name="'.$static_relation.'">';

						if(isset($default_value) &&  $default_value != '')
						{
							$option = split (',', $default_value);
							for($i=0; $i<count($option); $i++)
							{
								$this_html .= '<option value="'.$option[$i].'"';
								if( $VAR["static_relation"]["$id"] == $option[$i]) $this_html .= ' selected';
								$this_html .= '>'. $option[$i] . '</option>';
							}
						}                            
						else
						{
							$this_html .= '<option value=""></>';
						}
						$this_html .= '</select>';
					}
					elseif ($display == 'search')
					{
						$this_html = '<select name="'.$static_relation.'">';

						if(isset($default_value) &&  $default_value != '')
						{
							$this_html .= '<option value=""></>';
							$option = split (',', $default_value);
							for($i=0; $i<count($option); $i++)
							{
								$this_html .= '<option value="'.$option[$i].'"';
								if($static_value == $option[$i])
								$this_html .= ' selected';
								$this_html .= '>'. $option[$i] . '</option>';
							}
						}                            
						else
						{
							$this_html .= '<option value=""></>';
						}
						$this_html .= '</select>';                        	
					}
					else
					{
						$this_html = $static_value;
					}  
				}


				else if ($format == 'calendar')
				{ 
					if($display == 'update' || $display == 'search')
					{ 
						# set the date to current date if 'now' is set as $default
						if( $static_value == 'now' ) {
							$default = date(UNIX_DATE_FORMAT);	
						} else {
							$default = $static_value;
						} 
						$id = rand(9,999);
						$this_html = '
							<input type="text" id="data_'.$field.'_'.$id.'" name="'.$static_relation.'"/>&nbsp;						
							<input type="button" id="trigger_'.$field.'_'.$id.'" value="+">
							<script type="text/javascript">
							  Calendar.setup(
								{
								  inputField  : "data_'.$field.'_'.$id.'",
								  ifFormat    : "'.DEFAULT_DATE_FORMAT.'",
								  button      : "trigger_'.$field.'_'.$id.'"
								}
							  );
							</script>
							';

					}                    
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'file_upload')
				{
					if($display == 'update')
					{
						### FILE UPLOAD
						$this_html = 'File upload not yet supported!';
					}
					else
					{
						$this_html = '';
					}
				}


				else if ($format == 'status')
				{
					if($display == 'update')
					{
						### BOOLEAN TRUE/FALSE
						$C_list = new CORE_list;
						$this_html = $C_list->bool_static_var($static_relation, $static_value, $css_menu);
					}
					elseif($display == 'search')
					{
						### BOOLEAN TRUE/FALSE
						$C_list = new CORE_list;
						$this_html = $C_list->bool_static_var($static_relation, 'all', $css_menu);
					}                          
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'checkbox')
				{
					if($display == 'update')
					{
						### CHECKBOX
						if($static_value == '1')
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1" checked>';
						else
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1">';
					}
					elseif($display == 'search')
					{
						### BOOLEAN TRUE/FALSE
						$C_list = new CORE_list;
						$this_html = $C_list->bool_static_var($static_relation, 'all', $css_menu);
					}                           
					else
					{
						$this_html = $static_value;
					}
				}

				else if ($format == 'hidden')
				{
					### HIDDEN FIELD
					$this_html = '<input type="hidden" name="'.$static_relation.'" value="'.$static_value.'">';
				}


				### add to the array
				$arr[] = Array('name' => $this_name,
							   'html' => $this_html);

				$i++;
				$relation->MoveNext();
			}
		}

		#echo "<pre>";
		#htmlspecialchars(print_r(@$arr));

		return $arr;
	}	
















	function update_form($module, $method, $record_id)
	{
	   global $VAR, $C_translate;
	   include_once(PATH_CORE . 'validate.inc.php');

	   $display = 'update';

		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'
		### $Module is the module name
		### $Display is the display type (view or update) update allows for
		### user changes...

		$validate = false;
		if(isset($VAR['do'])  &&   gettype($VAR['do']) == 'array')
		{
			for($i=0; $i<count($VAR['do']); $i++)
			{
				if($VAR['do'][$i] == $module . ':' . $method) $validate = true;
			}
		}

		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return false;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			return false;
		}
		else
		{
			$i = 0;
			$C_validate = new CORE_validate;

			while (!$relation->EOF)
			{

				################################################################
				### Get the primary settings for this field

				$id             = $relation->fields['id'];
				$static_var_id  = $relation->fields['static_var_id'];
				$default_value  = $relation->fields['default_value'];
				$description    = $relation->fields['description'];
				$required       = $relation->fields['required'];



				############################################################
				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);



				$format         = $var->fields['input_format'];
				$validation     = $var->fields['validation_type'];
				$convert        = $var->fields['convert_type'];
				$name           = $var->fields['name'];


				############################################################
				### Get the value for this record, if any...

				$sql    = 'SELECT value FROM ' . AGILE_DB_PREFIX . 'static_var_record
						   WHERE
						   site_id      = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   record_id    = ' . $db->qstr($record_id) .' AND
						   static_var_id= ' . $db->qstr($static_var_id) .' AND
						   static_var_relation_id = ' . $db->qstr($id) .' AND
						   module_id = ' . $db->qstr($module_id);
				$value = $db->Execute($sql);

				$static_value = $value->fields['value'];



				############################################################
				### Generate the field name, translate if it exists,
				### otherwise, just return the actual field name

				### $this_name      = $C_translate->translate('field_'.$name, $module, '');        		 	
				### if($this_name = '') $this_name = $name;
				$this_name = $name;
				$static_relation = 'static_relation['.$id.']';


				############################################################
				### Determine The CSS Style to use...

				if($required == '1' && $validate == true)
				{
					if(!isset($VAR["static_relation"]["$id"]) || trim($VAR["static_relation"]["$id"]) == '')
					{
						$css = 'form_field_error';
					}
					else if ($validation != 'any'   &&   $validation != 'none'  &&   $validation != '')
					{
						$css = 'form_field_error';
						$css = 'form_field';
					}
					else
					{
						$css = 'form_field';
					}
				}
				else
				{
					$css = 'form_field';
				}

				$css_menu = 'form_menu';




				############################################################
				### Create the HTML

				if($format == 'small_text')
				{
					if($display == 'update')
					{
						### SMALL TEXT FIELD
						$this_html = '<input type="text" size="8" name="'.$static_relation.
						'" value="'.$static_value.'">';
					}
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'medium_text')
				{
					if($display == 'update')
					{
						### MEDIUM TEXT FIELD
						$this_html = '<input type="text" size="32" name="'.$static_relation.
						'" value="'.$static_value.'">';
					}
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'large_text')
				{
					if($display == 'update')
					{
						### LARGE TEXT FIELD
						$this_html = '<textarea name="'.$static_relation.'" cols="40" rows="5">'.
						$static_value.'</textarea>';
					}
					else
					{
						$this_html = $static_value;
					}
				}




				else if ($format == 'dropdown_list')
				{
					if($display == 'update')
					{
						### MENU LIST
						$this_html = '<select name="'.$static_relation.'">';

						if(isset($default_value) &&  $default_value != '')
						{
							$option = split (',', $default_value);
							for($i=0; $i<count($option); $i++)
							{
								$this_html .= '<option value="'.$option[$i].'"';
								if($static_value == $option[$i])
								$this_html .= ' selected';
								$this_html .= '>'. $option[$i] . '</option>';
							}
						}
						else
						{
							$this_html .= '<option value=""></>';
						}
						$this_html .= '</select>';
					}
					else
					{
						$this_html = $static_value;
					} 
				}


				else if ($format == 'calendar')
				{
					if($display == 'update')
					{ 
						if(!empty($static_value))
						@$default = date(UNIX_DATE_FORMAT,$static_value);
						else 
						$default = false;

						$id = rand(9,999);
						$this_html = '
							<input type="text" id="data_'.$field.'_'.$id.'" name="'.$static_relation.'" size="10" value="'.$default.'" />&nbsp;						
							<input type="button" id="trigger_'.$field.'_'.$id.'" value="+" class="form_button">
							<script type="text/javascript">
							  Calendar.setup(
								{
								  inputField  : "data_'.$field.'_'.$id.'",
								  ifFormat    : "'.DEFAULT_DATE_FORMAT.'",
								  button      : "trigger_'.$field.'_'.$id.'"
								}
							  );
							</script>
							';
					}
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'file_upload')
				{
					if($display == 'update')
					{
						### FILE UPLOAD
						$this_html = 'File upload not yet supported!';
					}
					else
					{
						$this_html = '';
					}
				}


				else if ($format == 'status')
				{
					if($display == 'update')
					{
						### BOOLEAN TRUE/FALSE
						$C_list = new CORE_list;
						$this_html = $C_list->bool_static_var($static_relation, $static_value, $css_menu);
					}
					else
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'checkbox')
				{
					if($display == 'update')
					{
						### CHECKBOX
						if($static_value == '1')
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1" checked>';
						else
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1">';
					}
					else
					{
						$this_html = $static_value;
					}
				}

				else if ($format == 'hidden')
				{
					### HIDDEN FIELD
					$this_html = '<input type="hidden" name="'.$static_relation.'" value="'.$static_value.'">';
				}


				### add to the array
				$arr[] = Array('name' => $this_name,
								 'html' => $this_html);

				$i++;
				$relation->MoveNext();
			}
		} 	
		return $arr;
	}	












	#################################
	##	View static vars for Record #
	#################################

	function view_form($module, $record_id)
	{
	   global $VAR, $C_translate; 	

		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'
		### $record_id is the record to retrieve values for


		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return false;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			return false;
		}
		else
		{
			$i = 0;


			while (!$relation->EOF)
			{

				################################################################
				### Get the primary settings for this field

				$id             = $relation->fields['id'];
				$static_var_id  = $relation->fields['static_var_id'];


				############################################################
				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);

				$format         = $var->fields['input_format'];
				$validation     = $var->fields['validation_type'];
				$convert        = $var->fields['convert_type'];
				$this_name      = $var->fields['name'];



				############################################################
				### Get the value for this record, if any...

				$sql    = 'SELECT value FROM ' . AGILE_DB_PREFIX . 'static_var_record
						   WHERE
						   site_id      = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   record_id    = ' . $db->qstr($record_id) .' AND
						   static_var_id= ' . $db->qstr($static_var_id) .' AND
						   static_var_relation_id = ' . $db->qstr($id) .' AND
						   module_id = ' . $db->qstr($module_id);
				$value = $db->Execute($sql);

				$static_value = $value->fields['value'];

				############################################################
				### Create the HTML

				if($format == 'small_text')
				{
					if($display == 'update')
					{
						### SMALL TEXT FIELD
						$this_html = '<input type="text" size="8" name="'.$static_relation.
						'" value="'.$static_value.'" class="'.$css.'">';
					}
					else if ( $static_value != '' )
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'medium_text')
				{
					if($display == 'update')
					{
						### MEDIUM TEXT FIELD
						$this_html = '<input type="text" size="32" name="'.$static_relation.
						'" value="'.$static_value.'">';
					}
					else if ( $static_value != '' )
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'large_text')
				{
					if($display == 'update')
					{
						### LARGE TEXT FIELD
						$this_html = '<textarea name="'.$static_relation.'" cols="40" rows="5">'.
						$static_value.'</textarea>';
					}
					else if ( $static_value != '' )
					{
						$return = '
';
						$this_html = preg_replace('/'.$return.'/', '<br>', $static_value);
					}
				}


				else if ($format == 'dropdown_list')
				{
					if($display == 'update')
					{
						### MENU LIST
						$this_html = '<select name="'.$static_relation.'">';

						if(isset($default_value) &&  $default_value != '')
						{
							$option = split (',', $default_value);
							for($i=0; $i<count($option); $i++)
							{
								$this_html .= '<option value="'.$option[$i].'"';
								if(!isset($VAR["static_relation"]["$id"]) || $VAR["static_relation"]["$id"] == $option[$i])
								$this_html .= ' selected';
								$this_html .= '>'. $option[$i] . '</option>';
							}
						}
						else
						{
							$this_html .= '<option value=""></>';
						}
						$this_html .= '</select>';
					}
					else if ( $static_value != '' )
					{
						$this_html = $static_value;
					}
				}


				else if ($format == 'calendar')
				{
					if($display == 'update')
					{
						### SHOW DATE SELECTOR
						$C_list = new CORE_list;
						$this_html = $C_list->calender_add_static_var($static_relation, $static_value, $css);
					}
					else if ( $static_value != '' )
					{
						$date = date(UNIX_DATE_FORMAT, $static_value);	 		 	
						$this_html = $date;
					}
				}


				else if ($format == 'file_upload')
				{
					if($display == 'update')
					{
						### FILE UPLOAD
						$this_html = 'File upload not yet supported!';
					}
					else if ( $static_value != '' )
					{
						$this_html = '';
					}
				}


				else if ($format == 'status')
				{
					if($display == 'update')
					{
						### BOOLEAN TRUE/FALSE
						$C_list = new CORE_list;
						$this_html = $C_list->bool_static_var($static_relation, $static_value, $css_menu);
					}
					else if ( $static_value != '' )
					{
						if($static_value == 1)
						$this_html = $C_translate->translate('true','','');
						else
						$this_html = $C_translate->translate('false','','');
					}
				}


				else if ($format == 'checkbox')
				{
					if($display == 'update')
					{
						### CHECKBOX
						if($static_value == '1')
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1" checked>';
						else
						$this_html = '<input type="checkbox" name="'.$static_relation.'" value="1">';
					}
					else if ( $static_value != '' )
					{
						if($static_value == 1)
						$this_html = $C_translate->translate('true','','');
						else
						$this_html = $C_translate->translate('false','','');
					}
				}

				else if ($format == 'hidden' && $static_value != '' )
				{
					### HIDDEN FIELD
					$this_html = $static_value;
				}


				### add to the array
				if ( $static_value != '' )
				{
					$arr[] = Array('name' => $this_name, 'html' => $this_html);
					$i++;
				}

				$relation->MoveNext();
			}
		}		
		return $arr;
	}	












	##############################
	##	VALIDATE A FORM          #
	##############################

	function validate_form($module, $errors)
	{
	   global $VAR, $C_translate;
	   include_once(PATH_CORE . 'validate.inc.php');


		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'
		### $Module is the module name
		### $Display is the display type (view or update) update allows for
		### user changes...


		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return $errors;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			 return $errors;
		}
		else
		{
			$i = 0;
			$C_validate = new CORE_validate;

			while (!$relation->EOF)
			{

				################################################################
				### Get the primary settings for this field

				$id             = $relation->fields['id'];
				$static_var_id  = $relation->fields['static_var_id'];
				$default_value  = $relation->fields['default_value'];
				$description    = $relation->fields['description'];
				$required       = $relation->fields['required'];



				############################################################
				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);



				$format         = $var->fields['input_format'];
				$validation     = $var->fields['validation_type'];
				$convert        = $var->fields['convert_type'];
				$name           = $var->fields['name'];



				############################################################
				### Generate the field name, translate if it exists,
				### otherwise, just return the actual field name

				### $this_name      = $C_translate->translate('field_'.$name, $module, '');        		 	
				### if($this_name = '') $this_name = $name;
				$this_name = $name;
				$static_relation = 'static_relation['.$id.']';

				############################################################
				### Determine the field value & name

				if(!isset($VAR["static_relation"]["$id"]) || $VAR["static_relation"]["$id"] == '')
				$field_value = '';
				else
				$field_value = $VAR["static_relation"]["$id"];




				############################################################
				### VALIDATE THE FIELD

				if($required == '1')
				{
					if($field_value == '')
					{
						### ERROR: This field is required!
						$errors[] =  Array('field' 		    => $name,
											'field_trans' 	=> $name,							
											'error' 		=> $C_translate->translate('validate_any',"", ""));	 						 	
					}
					else if ($validation != 'any'   &&   $validation != 'none')
					{

						### VALIDATE THIS FIELD:
						$val["min_len"] = '1';
						$val["max_len"] = '999999';
						if (!$C_validate->validate($name, $val, $field_value, $validation))
						{
							### ERROR: Validation failed!
							### ERROR: This field is required!
							$errors[] =  Array('field' 		    => $name,
												'field_trans' 	=> $name,							
												'error' 		=> $C_validate->error[$name]);						 	

						}
					}
				}

				$i++;
				$relation->MoveNext();
			}
		}


		if(!isset($errors))
		return false;
		else		 	
		return $errors;
	}


























	##############################
	##	ADD STATIC VAR RECORDS   #
	##############################

	function add($VAR, $module, $record_id)
	{
		include_once(PATH_CORE . 'validate.inc.php');


		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'


		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return false;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT id, static_var_id FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			return false;
		}
		else
		{
			$i = 0;
			$C_validate = new CORE_validate;

			# define the validation class
			$validate = new CORE_validate;		

			while (!$relation->EOF)
			{

				################################################################
				### Get the primary settings for this field

				$id             = $relation->fields['id'];
				$static_var_relation_id = $id;
				$static_var_id  = $relation->fields['static_var_id'];


				############################################################
				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT id,name,convert_type FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);

				$convert        = $var->fields['convert_type'];
				$name           = $var->fields['name'];



				############################################################
				### Generate the field name, translate if it exists,
				### otherwise, just return the actual field name

				$static_relation = 'static_relation['.$id.']';

				if(isset($VAR["static_relation"]["$id"]) && $VAR["static_relation"]["$id"] != '')
				{
					$value = $VAR["static_relation"]["$id"];

					if($convert != 'none' && $convert != '')
					$value = $validate->convert($name, $value, $convert);


					########################################################
					### Create the DB Record:
					$db     = &DB();
					$idx    = $db->GenID(AGILE_DB_PREFIX . "" . 'static_var_record_id');
					$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'static_var_record SET
								site_id  =  ' . $db->qstr(DEFAULT_SITE) . ',
								id          = ' . $db->qstr($idx) . ',
								record_id   = ' . $db->qstr($record_id) . ',
								module_id   = ' . $db->qstr($module_id) . ',
								static_var_id=' . $db->qstr($static_var_id) . ',
								static_var_relation_id= ' . $db->qstr($static_var_relation_id) . ',
								value       = ' . $db->qstr($value) ;
					$insert = $db->Execute($sql);

					# error reporting:
					if ($insert === false)
					{
						global $C_debug;
						$C_debug->error('static_var.inc.php','add', $db->ErrorMsg());
						return false;
					} 
				}
				$relation->MoveNext();
			}
		}
	return true;
	}


















	##############################
	##	ADD STATIC VAR RECORDS   #
	##############################

	function update($VAR, $module, $record_id)
	{
		include_once(PATH_CORE . 'validate.inc.php');


		####################################################################
		### $Method is the method name called to add records, so we know if we
		### should use the error class, i.e: 'user_add'


		####################################################################
		### Get the Id for this module

		$db     = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		return false;
		else
		$module_id = $result->fields['id'];



		####################################################################	 	
		### Get all the associated STATIC RELATION records

		$sql    = 'SELECT id, static_var_id FROM ' . AGILE_DB_PREFIX . 'static_relation WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					module_id   = ' . $db->qstr($module_id) .' ORDER BY sort_order';
		$relation = $db->Execute($sql);

		if($relation->RecordCount() == 0)
		{
			return false;
		}
		else
		{
			$i = 0;   
			$validate = new CORE_validate;		

			while (!$relation->EOF)
			{
				unset($value);

				### Get the primary settings for this field 
				$id             = $relation->fields['id'];
				$static_var_relation_id = $id;
				$static_var_id  = $relation->fields['static_var_id'];


				### Get the extended details for this field from the STATIC
				### VAR records

				$sql    = 'SELECT id,name,convert_type FROM ' . AGILE_DB_PREFIX . 'static_var WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($static_var_id);
				$var = $db->Execute($sql);

				$convert        = $var->fields['convert_type'];
				$name           = $var->fields['name'];



				############################################################
				### Generate the field name, translate if it exists,
				### otherwise, just return the actual field name

				$static_relation = 'static_relation['.$id.']';
				@$value = $VAR["static_relation"]["$id"];

				if(!empty($VAR["static_relation"]["$id"]) || $value == 0 )
				{ 
					if($convert != 'none' && $convert != '')
					$value = $validate->convert($name, $value, $convert);

					### Test record already exists:
					$sql    = 'SELECT id,value FROM ' . AGILE_DB_PREFIX . 'static_var_record  
								WHERE
								site_id  =  ' . $db->qstr(DEFAULT_SITE) . ' AND
								record_id   = ' . $db->qstr($record_id) . ' AND
								module_id   = ' . $db->qstr($module_id) . ' AND
								static_var_id=' . $db->qstr($static_var_id) . ' AND
								static_var_relation_id= ' . $db->qstr($static_var_relation_id);                               
					$return = $db->Execute($sql);        		        

					if ($return->RecordCount() == 0)
					{ 
						### Create new record:
						$idx    = $db->GenID(AGILE_DB_PREFIX . "" . 'static_var_record_id');
						$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'static_var_record SET
									site_id  =  ' . $db->qstr(DEFAULT_SITE) . ',
									id          = ' . $db->qstr($idx) . ',
									record_id   = ' . $db->qstr($record_id) . ',
									module_id   = ' . $db->qstr($module_id) . ',
									static_var_id=' . $db->qstr($static_var_id) . ',
									static_var_relation_id= ' . $db->qstr($static_var_relation_id) . ',
									value       = ' . $db->qstr($value) ;
						$insert = $db->Execute($sql); 
						if ($insert === false) {
							global $C_debug;
							$C_debug->error('static_var.inc.php','update', $db->ErrorMsg());
							return false;
						}   
					}
					elseif ($value != $return->fields['value'])
					{ 
						### UPDATE the DB Record: 
						$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'static_var_record SET
									value       = ' . $db->qstr($value) . '
									WHERE
									site_id  	=  ' . $db->qstr(DEFAULT_SITE) . ' AND
									id   = ' . $db->qstr($return->fields['id']);                                  
						$insert = $db->Execute($sql); 
						if ($insert === false) {
							global $C_debug;
							$C_debug->error('static_var.inc.php','update', $db->ErrorMsg());
							return false;
						}                      	                        	                        	
					}                  		 	        		                              		 	     		 		 	
				}
				else
				{
					### Test record already exists:
					$sql    = 'DELETE FROM ' . AGILE_DB_PREFIX . 'static_var_record  
								WHERE
								site_id  =  ' . $db->qstr(DEFAULT_SITE) . ' AND
								record_id   = ' . $db->qstr($record_id) . ' AND
								module_id   = ' . $db->qstr($module_id) . ' AND
								static_var_id=' . $db->qstr($static_var_id) . ' AND
								static_var_relation_id= ' . $db->qstr($static_var_relation_id);                               
					$return = $db->Execute($sql);                    	
				}
				$relation->MoveNext();
			}
		} 
	}	 	
} 
?>