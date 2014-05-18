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
	
	
# create the main block
function dev_block_main($VAR)
{
    $ret = '
    <table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr>
        <td>
          <table width="500" border="0" cellspacing="1" cellpadding="0" align="center">
            <tr>
              <td class="table_heading">
                <center>
                  {translate module='.$VAR['module'].'}
                  menu
                  {/translate}
                </center>
              </td>
            </tr>
            <tr>
              <td class="row1">
                <table width="100%" border="0" cellpadding="5" class="row1">
                  <tr>
                    <td>{translate module='.$VAR['module'].'}help_file{/translate}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
';
    return $ret;
}




 
#######################################################
##          create the add block                    ###
#######################################################
function dev_block_add($VAR)
{

# define the field types:
$field_type['text_small'] = '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$VAR.' . $VAR['module'] . '_%%field%%}" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if} size="5">';
$field_type['text_medium'] ='<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$VAR.' . $VAR['module'] . '_%%field%%}" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if}>';
$field_type['text_large'] = '<textarea name="' . $VAR['module'] . '_%%field%%" cols="40" rows="5" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if}>{$VAR.' . $VAR['module'] . '_%%field%%}</textarea>';
$field_type['menu'] =       '{ $list->menu("", "' . $VAR['module'] . '_%%field%%", "%%table%%", "name", $VAR.' . $VAR['module'] . '_%%field%%, "form_menu") }';
$field_type['account_menu'] = '{ $list->popup("' . $VAR['module'] . '_add", "' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "account_admin", "account", "first_name,middle_name,last_name", "form_field", "") }';
$field_type['date'] =       '{ $list->calender_add("' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "form_field") }';
$field_type['date_time'] =   '{$list->date_time("")}  <input type="hidden" name="' . $VAR['module'] . '_%%field%%" value="{$smarty.now}">';
$field_type['date_now'] =    '{$list->date_time("")}  <input type="hidden" name="' . $VAR['module'] . '_%%field%%" value="{$smarty.now}">';
$field_type['bool'] =       '{ $list->bool("' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "form_menu") }';


$ret = '

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="' . $VAR['module'] . '_add" name="' . $VAR['module'] . '_add" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=' . $VAR['module'] . '}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
';

    #loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        $type = $VAR["field"]["$field"]["field_type"];
        $this_content = preg_replace('/%%field%%/', $field, $field_type["$type"]);
        if($type == 'menu') $this_content = preg_replace('/%%table%%/', $VAR["field"]["$field"]["asso_table"], $this_content);
        
        if(isset($VAR["field"]["$field"]["page_add"]["include"]))
        {

    
        $ret .= '                <tr valign="top">
                    <td width="35%">
                        {translate module=' . $VAR['module'] . '}
                            field_' . $field . '
                        {/translate}</td>
                    <td width="65%">
                        ' . $this_content . '
                    </td>
                  </tr>
';
        }
    }



    $ret .= '           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="' . $VAR['module'] . ':view">
                      <input type="hidden" name="_page_current" value="' . $VAR['module'] . ':add">
                      <input type="hidden" name="do[]" value="' . $VAR['module'] . ':add">
                    </td>
                  </tr>
                </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
';

return $ret;
}





    #######################################################
    ##          GENERATE THE INSTALL DATA (SQL)         ###
    #######################################################
    function dev_install_xml_data($module,$module_id)
    {
        		$rt = '
';
        		# open the backup file:
        		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?'.''.'>';
        	    $xml .= $rt . '<install>'  . $rt;


            		                       		
            		# generate the sql select statement:
            		$db = &DB();
                    $dba = &DB();    	
                     
                    	
                    $sql = 'SELECT * FROM '.AGILE_DB_PREFIX.''.$module.' WHERE site_id = '.
                                $dba->qstr(DEFAULT_SITE) . ' ORDER BY id';
                    $resulta = $dba->Execute($sql);

                    # check the results
                    if($resulta != false && $resulta->RecordCount() > 0)
                    {
                                                         		
                            # get the export data:
                            $resultarr = $resulta->GetArray();

                            # loop through each field
                            for ($ii=0; $ii<count($resultarr); $ii++)
                            {
                                $xml .= '  <'.$module.'>'  . $rt;
                                while (list ($field,$value) = each ($resultarr[$ii]))
                                {
                                	if($value != '' && !is_integer($field))
                                	{
	                                    if( preg_match('/</', $value) || preg_match('/>/', $value) || preg_match('/&/', $value) || preg_match('/\'/', $value) || preg_match('/"/', $value) ) {
	                                        $value = preg_replace('/&amp;/', '&', $value);
	                                        $data = '       <'.$field.'><![CDATA[' . $value . ']]></'. $field . '>';
	                                        $xml .= $data . '' . $rt;
	                                    } else  {
	                                        $data = '       <'.$field.'>' . $value . '</'. $field . '>';
	                                        $xml .= $data . '' . $rt;                                    	
	                                    }
                                	}
                                }
                                $xml .= '  </'.$module.'>' . $rt;
                            }


                        ####################################################################
                        # backup the autoincrement count:
                        ####################################################################

                        $idmodule = $module . '_id';   		
                        $sql = 'SELECT id FROM '.AGILE_DB_PREFIX.''.$idmodule;
                        $resulti = $db->Execute($sql);

                        if($resulti!=false && @$resulti->RecordCount() != 0)
                        {                                       		
                            # get the export data:
                            $resultarr = $resulti->GetArray();

                            # loop through each field
                            for ($ii=0; $ii<count($resultarr); $ii++)
                            {
                                $xml .= '  <'.$idmodule.'>'  . $rt;
                                while (list ($field,$value) = each ($resultarr[$ii]))
                                {
                                    if($value != '' && gettype($field) != 'integer')
                                    {
                                        $data = '       <'.$field.'>' . htmlspecialchars($value,0,"ISO8859-1") . '</'. $field . '>';
                                        $xml .= $data . '' . $rt;
                                    }
                                }
                                $xml .= '  </'.$idmodule.'>' . $rt;
                            }
                        }
                    } else {
                    	return false;
                    }

    	    		
           		$xml .= '</install>';
           		return $xml;
           		
    }




    #######################################################
    ##          GENERATE THE INSTALL XML                ###
    #######################################################
    function dev_install_xml_gen($module,$module_id)
    {
        # get the module parent
        $db = &DB();
        $sql = "SELECT * FROM ".AGILE_DB_PREFIX."module WHERE
                site_id = ".$db->qstr(DEFAULT_SITE)." AND
                id      = ".$db->qstr($module_id);
        $mr = $db->Execute($sql);
        
        if( $mr->fields["parent_id"] ==  "" ||
            $mr->fields["parent_id"] == "0" ||
            $mr->fields["parent_id"] == $module_id )
        {
            $parent = $module;
        }
        else
        {
            $db = &DB();
            $sql = "SELECT * FROM ".AGILE_DB_PREFIX."module WHERE
                    site_id = ".$db->qstr(DEFAULT_SITE)." AND
                    id      = ".$db->qstr($mr->fields["parent_id"]);
            $mrp = $db->Execute($sql);
            $parent = $mrp->fields["name"];
        }

        

        # get the current settings:
        $t = "\t";
        $n = "\n";
        $C_xml = new CORE_xml;
		$inst = $C_xml->xml_to_array(PATH_MODULES . '' . $module . '/' . $module . '_install.xml');	
 
        # Get any dependancy
        $dependancy = @$inst['install']['module_properties']['dependancy'];
        
        # Get any sub_modules		
		$sub_modules = @$inst['install']['module_properties']['sub_modules'];
		 
		#################################################################
		# regenerate the install file:
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>{$n}";
		$install_xml =
			"<install>". $n .
			"{$t}<module_properties>".$n .
			"{$t}{$t}<name>{$module}</name>".$n .
			"{$t}{$t}<parent>{$parent}</parent>".$n .
			"{$t}{$t}<notes><![CDATA[{$mr->fields["notes"]}]]></notes>{$n}";

		if(!empty($mr->fields["menu_display"]))
		$install_xml .=
			"{$t}{$t}<menu_display>1</menu_display>{$n}";

		if(!empty($dependancy))
		$install_xml .=
			"{$t}{$t}<dependancy>{$dependancy}</dependancy>{$n}";

		if(!empty($sub_modules))
		$install_xml .=
			"{$t}{$t}<sub_modules>{$sub_modules}</sub_modules>{$n}";

		$install_xml .=
			"{$t}</module_properties>" . $n .
			"{$t}<sql_inserts>" . $n .
			"{$t}{$t}<module_method>" . $n;

        # loop through the methods
        $db = &DB();
        $sql = "SELECT * FROM ".AGILE_DB_PREFIX."module_method WHERE
                site_id = ".$db->qstr(DEFAULT_SITE)." AND
                module_id    = ".$db->qstr($module_id);
        $result=$db->Execute($sql);
        
        while(!$result->EOF)
        {
            $method  = $result->fields['name'];
            $display = $result->fields['menu_display'];
            $notes   = $result->fields['notes'];
            $page    = trim(preg_replace('/&amp;/', '&', $result->fields['page'] ) );
            
            $install_xml .= 
                "{$t}{$t}{$t}<{$method}>".$n.
               	"{$t}{$t}{$t}{$t}<name>{$method}</name>". $n;
               	
          	if(!empty($notes))
          	$install_xml .=
          		"{$t}{$t}{$t}{$t}<notes><![CDATA[{$notes}]]></notes>" . $n;
          		
          	if(!empty($page))
          	$install_xml .=
          		"{$t}{$t}{$t}{$t}<page><![CDATA[{$page}]]></page>" . $n;

          	if(!empty($display))
          	$install_xml .=
          		"{$t}{$t}{$t}{$t}<menu_display>1</menu_display>" . $n;

          	$install_xml .=
          		"{$t}{$t}{$t}</{$method}>" . $n;   

            $result->MoveNext();
        }

        $install_xml .= 
        	"{$t}{$t}</module_method>".$n.
        	"{$t}</sql_inserts>".$n.
			"</install>";

        return $install_xml;
    }






#######################################################
##          create the view block                   ###
#######################################################
function dev_block_view($VAR)
{


# define the field types:
$field_type['text_small']   =   '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$'.$VAR['module'].'.%%field%%}" size="5">';
$field_type['text_medium'] =    '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$'.$VAR['module'].'.%%field%%}" size="32">';
$field_type['text_large'] =     '<textarea name="' . $VAR['module'] . '_%%field%%" cols="40" rows="5">{$'.$VAR['module'].'.%%field%%}</textarea>';
$field_type['menu'] =           '{ $list->menu("", "' . $VAR['module'] . '_%%field%%", "%%table%%", "name", $'.$VAR['module'].'.%%field%%, "form_menu") }';
$field_type['account_menu'] =   '{ $list->popup("' . $VAR['module'] . '_view", "' . $VAR['module'] . '_%%field%%", $'.$VAR['module'].'.%%field%%, "account_admin", "account", "first_name,middle_name,last_name", "form_field", $' . $VAR['module'] .'.id) }';
$field_type['date'] =           '{ $list->calender_view("' . $VAR['module'] . '_%%field%%", $'.$VAR['module'].'.%%field%%, "form_field", $' . $VAR['module'] .'.id) }';
$field_type['date_time'] =      '{ $list->calender_view("' . $VAR['module'] . '_%%field%%", $'.$VAR['module'].'.%%field%%, "form_field", $' . $VAR['module'] .'.id) }';
$field_type['date_now'] =       '{$list->date_time("")}  <input type="hidden" name="' . $VAR['module'] . '_%%field%%" value="{$smarty.now}">';
$field_type['bool'] =           '{ $list->bool("' . $VAR['module'] . '_%%field%%", $'.$VAR['module'].'.%%field%%, "form_menu") }';


    $ret = '
{ $method->exe("'  . $VAR["module"] .  '","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = \''  . $VAR["module"] .  '\';
    	var locations = \'{/literal}{$VAR.module_id}{literal}\';
    	if (locations != "")
    	{
    		refresh(0,\'#\'+locations)
    	}
    	// Mass update, view, and delete controller
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, \'\');		
    		if(ids == \'\') {
    			var url = \'?_page=core:search&module=\' + module + \'&do[]=\' + module + \':delete&delete_id=\' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = \'view&id=\' +ids;
    		}		
    		
    		var doit = \'delete\';
    		var url = \'?_page=\'+ module +\':\'+ page +\'&do[]=\' + module + \':\' + doit + \'&delete_id=\' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$'  . $VAR["module"] .  ' item='  . $VAR["module"] .  '} <a name="{$'  . $VAR["module"] .  '.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="' . $VAR['module'] . '_view" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=' . $VAR['module'] . '}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
';



    #loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        $type = $VAR["field"]["$field"]["field_type"];
        $this_content = preg_replace('/%%field%%/', $field, $field_type["$type"]);
        if($type == 'menu') $this_content = preg_replace('/%%table%%/', $VAR["field"]["$field"]["asso_table"], $this_content);

        if(isset($VAR["field"]["$field"]["page_view"]["include"]))
        {
            # is field changeable
            if(!isset($VAR["field"]["$field"]["page_view"]["type"]))
            {
                $this_content = $VAR['module'] . '.' . $field;
                
                if($type == 'bool')
                {
                    $this_content = '{if $' . $this_content . ' == "1"}{translate}true{/translate}{else}{translate}false{/translate}{/if}';
                }
                else if ($type == 'date')
                {
                    $this_content = '{$list->date($' . $this_content . ')}';
                }
                else if ($type == 'date_time' || 'date_now')
                {
                    $this_content = '{$list->date_time($' . $this_content . ')}';
                }
                else
                {
                    $this_content = '{$' . $this_content .'}';
                }
            }


        $ret .= '                  <tr valign="top">
                    <td width="35%">
                        {translate module=' . $VAR['module'] . '}
                            field_' . $field . '
                        {/translate}</td>
                    <td width="65%">
                        ' . $this_content . '
                    </td>
                  </tr>
';

        }
    }



            $ret .= '          <tr class="row1" valign="middle" align="left">
                    <td width="35%"></td>
                    <td width="65%">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record(\'{$'  . $VAR["module"] .  '.id}\',\'{$VAR.id}\');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="'  . $VAR["module"] .  ':view">
    <input type="hidden" name="'  . $VAR["module"] .  '_id" value="{$'  . $VAR["module"] .  '.id}">
    <input type="hidden" name="do[]" value="'  . $VAR["module"] .  ':update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
';

return $ret;
}


















#######################################################
##          create the search_form block            ###
#######################################################
function dev_block_search_form($VAR)
{


# define the field types:
$field_type['text_small']   = '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$VAR.' . $VAR['module'] . '_%%field%%}" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if} size="5"> &nbsp;&nbsp;{translate}search_partial{/translate}';
$field_type['text_medium']  = '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$VAR.' . $VAR['module'] . '_%%field%%}" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}';
$field_type['text_large']   = '<input type="text" name="' . $VAR['module'] . '_%%field%%" value="{$VAR.' . $VAR['module'] . '_%%field%%}" {if $' . $VAR['module'] . '_%%field%% == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}';
$field_type['menu']         = '{ $list->menu("", "' . $VAR['module'] . '_%%field%%", "%%table%%", "name", "all", "form_menu") }';
$field_type['account_menu'] = '{ $list->popup("' . $VAR['module'] . '_search", "' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "account_admin", "account", "first_name,middle_name,last_name", "form_field", "") }';
$field_type['date']         = '{ $list->calender_search("' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "form_field", "") }';
$field_type['date_time']    = '{ $list->calender_search("' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "form_field", "") }';
$field_type['date_now']     = '{ $list->calender_search("' . $VAR['module'] . '_%%field%%", $VAR.' . $VAR['module'] . '_%%field%%, "form_field", "") }';
$field_type['bool']         = '{ $list->bool("' . $VAR['module'] . '_%%field%%", "all", "form_menu") }';


    $ret = '
{ $method->exe("'  . $VAR["module"] .  '","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="' . $VAR["module"] .'_search" method="post" action="">
  {$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=' . $VAR['module'] . '}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
';

    #loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        $type = $VAR["field"]["$field"]["field_type"];
        $this_content = preg_replace('/%%field%%/i', $field, $field_type["$type"]);
        if($type == 'menu') $this_content = preg_replace('/%%table%%/i', $VAR["field"]["$field"]["asso_table"], $this_content);

        if(isset($VAR["field"]["$field"]["page_search_form"]["include"]))
        {


        $ret .= '                   <tr valign="top">
                    <td width="35%">
                        {translate module=' . $VAR['module'] . '}
                            field_' . $field . '
                        {/translate}</td>
                    <td width="65%">
                        ' . $this_content . '
                    </td>
                  </tr>
';
        }
    }



    $ret .= '                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$'  . $VAR["module"] .  '_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$'  . $VAR["module"] .  ' item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="'  . $VAR["module"] .  '">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
';

return $ret;
}




















#######################################################
##          create the search_show block            ###
#######################################################
function dev_block_search_show($VAR)
{



$ret = '

{$method->exe("'  . $VAR["module"] .  '","search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}
  <BR>


  {literal}
    <script language="JavaScript">
    <!-- START
    	var module 		= \''  . $VAR["module"] .  '\';		
    	{/literal}
    	{if $VAR._print == TRUE}
    	var p 			= \'&_escape=y&_print=y\';
    	{else}
    	var p 			= \'\';
    	{/if}{literal}
    	var IMAGE 		= \'{/literal}{$NONSSL_IMAGE}{literal}\';
    	var order 		= \'{/literal}{$order}{literal}\';
    	var sort1  		= \'{/literal}{$sort}{literal}\';
    	var search_id 	= \'{/literal}{$search_id}{literal}\';
    	var page 		= \'{/literal}{$page}{literal}\';
    	var pages		= \'{/literal}{$pages}{literal}\';
    	var results		= \'{/literal}{$results}{literal}\';
    	var limit 		= \'{/literal}{$limit}{literal}\';
    	record_arr = new Array ({/literal}{$limit}{literal});
    	var i = 0;	
    //  END -->
    </script>
    <SCRIPT SRC="themes/{/literal}{$THEME_NAME}{literal}/search.js"></SCRIPT>
    {/literal}

    <!-- SHOW THE SEARCH NAVIGATION MENU -->
    <center><script language="JavaScript">document.write(search_nav_top());</script></center>

<!-- BEGIN THE RESULTS CONTENT AREA -->
<div id="search_results" onKeyPress="key_handler(event);">
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
		
		
		  <!-- DISPLAY THE SEARCH HEADING -->
          <tr valign="middle" align="center" class="table_heading">
            <td width="5%" class="table_heading">&nbsp;</td>
';

    $total = 0;
    #count the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        if(isset($VAR["field"]["$field"]["page_search_show"]["include"]))
        {
            $total++;
        }
    }
    
    
    @$width = 95 / $total;

    #loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        $type = $VAR["field"]["$field"]["field_type"];
        $this_content = '$record.' . $field;
        if(isset($VAR["field"]["$field"]["page_search_show"]["include"]))
        {
$ret.= '            <td width="'.$width.'%" class="table_heading">
              {literal}
                 <script language="JavaScript">
					document.write(search_heading(\'{/literal}{translate module='  . $VAR["module"] .  '}field_'.$field.'{/translate}{literal}\',\''.$field.'\'));
				 </script>
              {/literal}
            </td>
';
        }
    }
		
$ret.= '			 <!-- LOOP THROUGH EACH RECORD -->
			 {foreach from=$'  . $VAR["module"] .  ' item=record}
             <tr id="row{$record.id}" onClick="row_sel(\'{$record.id}\',1);" onDblClick="window.location=\'?_page='  . $VAR["module"] .  ':view&id={$record.id},{$COOKIE_URL}\';" onMouseOver="row_mouseover(\'{$record.id}\', \'row_mouse_over_select\', \'row_mouse_over\');" onMouseOut="row_mouseout(\'{$record.id}\', \'{$record._C}\', \'row_select\');" class="{$record._C}">
             
              <td align="center" width="5%">
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel(\'{$record.id}\',1,\'{$record._C}\');">
              </td>
';

    #loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
        $type = $VAR["field"]["$field"]["field_type"];
        $this_content = '$record.' . $field;
 
        if(isset($VAR["field"]["$field"]["page_search_show"]["include"]))
        {
            if($type == 'bool')
            {
                $this_content = '{if ' . $this_content . ' == "1"}{translate}true{/translate}{else}{translate}false{/translate}{/if}';
            }
                else if ($type == 'date')
            {
                $this_content = '{$list->date(' . $this_content . ')}';
            }
            else if ($type == 'date_time' || $type == 'date_now')
            {
                $this_content = '{$list->date_time(' . $this_content . ')}';
            }
            else
            {
                $this_content = '{' . $this_content .'}';
            }

			
$ret .= '	            <td>&nbsp;' . $this_content . '</td>
';
        }
    }
				
$ret .= '
	          </tr>

              {literal}
              <script language="JavaScript">row_sel(\'{/literal}{$record.id}{literal}\', 0, \'{/literal}{$record._C}{literal}\'); record_arr[i] = \'{/literal}{$record.id}{literal}\'; i++; </script>
              {/literal}		
	          {/foreach} 	
			  <!-- END OF RESULT LOOP -->		  	
			  
        </table>
      </td>
    </tr>
  </form>
 </table>
{if $VAR._print != TRUE}<br>
<center>
<input type="submit" name="Submit" value="{translate}view_edit{/translate}" 	onClick="mass_do(\'\', module+\':view\', limit, module);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}delete{/translate}" 		onClick="mass_do(\'delete\', module+\':search_show&search_id={$search_id}&page={$page}&order_by={$order}&{$sort}{$COOKIE_URL}\', limit, module);" class="form_button">
<input type="submit" name="Submit" value="{translate}select_all{/translate}" 	onClick="all_select(record_arr);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}deselect_all{/translate}" 	onClick="all_deselect(record_arr);" 	class="form_button">
<input type="submit" name="Submit" value="{translate}range_select{/translate}" 	onClick="all_range_select(record_arr,limit);" class="form_button">
<br>
';

if(isset($VAR['module_export_bar']))
{
$ret .= '<br>
<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr>
    <td valign="middle" align="center">
      <a href="#" onClick="NewWindow(\'ExportWin\',\'toolbar=no,status=no,width=300,height=300\',\'?_page=core:export_search&module='  . $VAR["module"] .  '&_escape=&search_id={$search_id}&page={$page}&order={$order}&sort={$sort}{$COOKIE_URL}\');"><img src="themes/{$THEME_NAME}/images//icons/exp_32.gif" alt="{translate}search_export_image{/translate}" border="0"></a>
      <a href="?_page='  . $VAR["module"] .  ':search_show&_print=true&_escape=true&order_by={$order}&search_id={$search_id}&limit={$limit}&page={$page}{$COOKIE_URL}" target="_blank"><img src="themes/{$THEME_NAME}/images//icons/print_32.gif" border="0" alt="{translate}search_print_image{/translate}"></a>';
      //<a href="#" onClick="NewWindow(\'SaveSearchWin\',\'toolbar=no,status=no,width=200,height=150\',\'?_page=core:save_search&_escape=&search_id={$search_id}&module='  . $VAR["module"] .  ':search_save{$COOKIE_URL}\');"><img src="themes/{$THEME_NAME}/images/icons/savas_32.gif" border="0" alt="{translate}search_save_image{/translate}"></a>
$ret .= '
      <a href="?_page='  . $VAR["module"] .  ':search_form{$COOKIE_URL}"><img src="themes/{$THEME_NAME}/images/icons/srch_32.gif" border="0" alt="{translate}search_new_image{/translate}"></a>
      <a href="?_page='  . $VAR["module"] .  ':add{$COOKIE_URL}"><img src="themes/{$THEME_NAME}/images/icons/add_32.gif" border="0" alt="{translate module='  . $VAR["module"] .  '}title_add{/translate}"></a>
    </td>
  </tr>
</table>
';
}
$ret.= '</center>
{/if}
{/if}
</div>
';

return $ret;
}













#######################################################
##          GENERATE THE LANGUAGE PACKS             ###
#######################################################
function dev_language_xml($VAR)
{
$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?'.''.'>';
$language_xml = $xml . '
<translate>
	<name>' . $VAR["lang"]["name"] . '</name>
	
	<!-- define the block title translations -->';
	
    # loop through the methods
    for($i=0; $i < count($VAR["m"]); $i++)
    {
        $method = $VAR["m"]["$i"];
        $language_xml .= '
    <title_' . $method . '>';
        $title = $VAR["method"]["$method"]["block_name"];
        $language_xml .= $title . '</title_' . $method . '>';
    }
    	

	$language_xml .='
	
	<!-- define the menu lable translations -->
        <menu>' . $VAR["lang"]["menu"] . '</menu>';
	
	
    # loop through the methods
    for($i=0; $i < count($VAR["m"]); $i++)
    {
        $method = $VAR["m"]["$i"];
        $language_xml .= '
    <menu_' . $method . '>';
        $menu  = $VAR["method"]["$method"]["menu_name"];
        $language_xml .= $menu . '</menu_' . $method . '>';
    }

	$language_xml .='
    	
	<!-- define the field translations -->';

    # loop through the fields
    for($i=0; $i < count($VAR["f"]); $i++)
    {
        $field = $VAR["f"]["$i"];
	   $language_xml .='	
	<field_' . $field . '>' . $VAR["field"]["$field"]["name"] . '</field_' . $field . '>';
    }
    
    $pat_nl = '
';
	$language_xml .='	       			
</translate>';

return $language_xml;
}




#######################################################
##          GENERATE THE CONSTRUCT PHP              ###
#######################################################
function dev_construct_php($VAR)
{
    $construct_php = '<?php

    class ' . $VAR["module"] . '
    {

        # Open the constructor for this mod
        function ' . $VAR["module"] . '()
        {
            # name of this module:
            $this->module = "' . $VAR["module"] . '";

            # location of the construct XML file:
            $this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

            # open the construct file for parsing	
			$C_xml = new CORE_xml;
			$construct = $C_xml->xml_to_array($this->xml_construct);
		
			$this->method   = $construct["construct"]["method"];
			$this->trigger  = $construct["construct"]["trigger"];
			$this->field    = $construct["construct"]["field"];
        	$this->table 	= $construct["construct"]["table"];
        	$this->module 	= $construct["construct"]["module"];
        	$this->cache	= $construct["construct"]["cache"];
		    $this->order_by = $construct["construct"]["order_by"];
    		$this->limit	= $construct["construct"]["limit"];
        }

'; if (isset($VAR["method"]["add"])) { $construct_php .=  '

        ##############################
        ##		ADD   		        ##
        ##############################
        function add($VAR)
        {
    		$type 		= "add";
    		$this->method["$type"] = explode(",", $this->method["$type"]);    		
    		$db 		= new CORE_database;
    		$db->add($VAR, $this, $type);
        }
'
; } if (isset($VAR["method"]["view"])) { $construct_php .=
'    	
        ##############################
        ##		VIEW			    ##
        ##############################
        function view($VAR)
        {	
    		$type = "view";
            $this->method["$type"] = explode(",", $this->method["$type"]);
    		$db = new CORE_database;
    		 $db->view($VAR, $this, $type);
    	}		
'
; } if (isset($VAR["method"]["update"])) { $construct_php .=
'
        ##############################
        ##		UPDATE		        ##
        ##############################
        function update($VAR)
        {
            $type = "update";
            $this->method["$type"] = explode(",", $this->method["$type"]);
    		$db = new CORE_database;
    		 $db->update($VAR, $this, $type);
        }
'
; } if (isset($VAR["method"]["delete"])) { $construct_php .=
'
        ##############################
        ##		 DELETE	            ##
        ##############################
        function delete($VAR)
        {	
    		$db = new CORE_database;
    		 $db->mass_delete($VAR, $this, "");
    	}		
'
; } if (isset($VAR["method"]["search"])) { $construct_php .=
'
        ##############################
        ##	     SEARCH FORM        ##
        ##############################
        function search_form($VAR)
        {
    		$type = "search";
    	    $this->method["$type"] = explode(",", $this->method["$type"]);
    		$db = new CORE_database;
    		 $db->search_form($VAR, $this, $type);
    	}
'
; } if (isset($VAR["method"]["search"])) { $construct_php .=
'
        ##############################
        ##		    SEARCH		    ##
        ##############################
        function search($VAR)
        {	
    		$type = "search";
    	    $this->method["$type"] = explode(",", $this->method["$type"]);
    		$db = new CORE_database;
    		 $db->search($VAR, $this, $type);
    	}
'
; } if (isset($VAR["method"]["search"])) { $construct_php .=
'
        ##############################
        ##		SEARCH SHOW	        ##
        ##############################

        function search_show($VAR)
        {	
    		$type = "search";
    	    $this->method["$type"] = explode(",", $this->method["$type"]);
    		$db = new CORE_database;
    		 $db->search_show($VAR, $this, $type);
    	}	
'
; } if (isset($VAR["method"]["search_save"])) { $construct_php .=
'    	    	
        ##############################
        ##  	SEARCH SAVE         ##
        ##############################
        function search_save($VAR)
        {
    		if	(
    			isset($VAR["search_id"]) &&
    			isset($VAR["save_name"])
    			)
    		{
    			if	(
    				$VAR["search_id"] != "" &&
    				$VAR["save_name"] != ""
    				)
    			{
    			    # create the record
    			    include_once(PATH_CORE   . "search.inc.php");
                    $search = new CORE_search;
                    $search->save($VAR["search_id"], $this->table, $VAR["save_name"]);
                }
            }
        }
'
; } if (isset($VAR["method"]["search_export"])) { $construct_php .=
'
        ##############################
        ##	   SEARCH EXPORT        ##
        ##############################    	
    	function search_export($VAR)
    	{
           # require the export class    	
    	   require_once (PATH_CORE   . "export.inc.php");
    	
    	   # Call the correct export function for inline browser display, download, email, or web save.
    	   if($VAR["format"] == "excel")
    	   {
        	   $type = "export_excel";
        	   $this->method["$type"] = explode(",", $this->method["$type"]);
        	   $export = new CORE_export;
        	    $export->search_excel($VAR, $this, $type);    	
    	   }
    	
           else if ($VAR["format"] == "pdf")
    	   {
        	   $type = "export_pdf";
        	   $this->method["$type"] = explode(",", $this->method["$type"]);
        	   $export = new CORE_export;
        	    $export->search_pdf($VAR, $this, $type);      	
    	   }
    	
           else if ($VAR["format"] == "xml")
    	   {
        	   $type = "export_xml";
        	   $this->method["$type"] = explode(",", $this->method["$type"]);
        	   $export = new CORE_export;
        	    $export->search_xml($VAR, $this, $type);
    	   }
    	
           else if ($VAR["format"] == "csv")
    	   {
        	   $type = "export_csv";
        	   $this->method["$type"] = explode(",", $this->method["$type"]);
        	   $export = new CORE_export;
        	    $export->search_csv($VAR, $this, $type);
    	   }
    	
           else if ($VAR["format"] == "tab")
    	   {
        	   $type = "export_tab";
        	   $this->method["$type"] = explode(",", $this->method["$type"]);
        	   $export = new CORE_export;
        	    $export->search_tab($VAR, $this, $type);
           }                                           	
    	}      	
';
}

$construct_php .= '		
    }
?' . '' . '>';

return $construct_php;
}











#######################################################
##          GENERATE THE CONSTRUCT XML              ###
#######################################################
function dev_construct_xml($VAR)
{
           $xml = '<?xml version="1.0" encoding="ISO-8859-1" ?'.''.'>';

           $construct_xml = $xml . '
<construct>

    <!-- define the module name -->
    <module>' . $VAR["module"] . '</module>

    <!-- define the module table name -->
    <table>' . $VAR["table"] . '</table>

    <!-- define the module dependancy(s) -->
    <dependancy>' . $VAR["dependancy"] . '</dependancy>

    <!-- define the DB cache in seconds -->
    <cache>' . $VAR["cache"] . '</cache>

    <!-- define the default order_by field for SQL queries -->
    <order_by>' . $VAR["order_by"] . '</order_by>

    <!-- define the methods -->
    <limit>' . $VAR["limit"] . '</limit>

    <!-- define the fields -->
    <field>';

        # loop through the fields
        for($i=0; $i < count($VAR["f"]); $i++)
        {

            $field = $VAR["f"]["$i"];
            $construct_xml .= '
        <' . $field . '>';

            if (isset($VAR["field"]["$field"]["type"]))
            $construct_xml .= '
            <type>'. $VAR["field"]["$field"]["type"] .'</type>';

            if (isset($VAR["field"]["$field"]["min_len"]))
            $construct_xml .= '
            <min_len>'. $VAR["field"]["$field"]["min_len"] .'</min_len>';

            if (isset($VAR["field"]["$field"]["max_len"]))
            $construct_xml .= '
            <max_len>'. $VAR["field"]["$field"]["max_len"] .'</max_len>';

            if (isset($VAR["field"]["$field"]["def_len"]))
            $construct_xml .= '
            <def_len>'. $VAR["field"]["$field"]["def_len"] .'</def_len>';

            if (isset($VAR["field"]["$field"]["pdf_width"]))
            $construct_xml .= '
            <pdf_width>'. $VAR["field"]["$field"]["pdf_width"] .'</pdf_width>';

            if (isset($VAR["field"]["$field"]["default"]))
            $construct_xml .= '
            <default>'. $VAR["field"]["$field"]["default"] .'</default>';

            if (isset($VAR["field"]["$field"]["asso_table"]))
            $construct_xml .= '
            <asso_table>'. $VAR["field"]["$field"]["asso_table"] .'</asso_table>';

            if (isset($VAR["field"]["$field"]["asso_field"]))
            $construct_xml .= '
            <asso_field>'. $VAR["field"]["$field"]["asso_field"] .'</asso_field>';

            if (isset($VAR["field"]["$field"]["validate"]))
            $construct_xml .= '
            <validate>'. $VAR["field"]["$field"]["validate"] .'</validate>';

            if (isset($VAR["field"]["$field"]["convert"]))
            $construct_xml .= '
            <convert>'. $VAR["field"]["$field"]["convert"] .'</convert>';

            if (isset($VAR["field"]["$field"]["unique"]))
            $construct_xml .= '
            <unique>'. $VAR["field"]["$field"]["unique"] .'</unique>';

            if (isset($VAR["field"]["$field"]["index"]))
            $construct_xml .= '
            <index>'. $VAR["field"]["$field"]["index"] .'</index>';
        $construct_xml .= '
        </' . $field . '>';
        }


$construct_xml .= '
     </field>

     <!-- define all the methods for this class, and the fields they have access to, if applicable. -->
     <method>';

    # loop through the methods
    for($i=0; $i < count($VAR["m"]); $i++)
    {
        $method = $VAR["m"]["$i"];
        $construct_xml .= '
        <' . $method . '>id';
        $arr = $VAR["method"]["$method"];
        $ii = 0;
        while (list($key, $value) = each($arr))
        {
            if($value == 1)
            {
                if (($ii != 0) && ($key != 'method_display'))
                {
                    $construct_xml .= ',';
                    $construct_xml .= $key;
                }
            }
            $ii++;
        }

        $construct_xml .= '</' . $method . '>';
    }


$construct_xml .= '
     </method>

     <!-- define the method triggers -->
     <trigger>';

    # loop through the methods
    for($i=0; $i < count($VAR["m"]); $i++)
    {
        $method = $VAR["m"]["$i"];
        $construct_xml .= '
        <' . $method . '>';
        $arr = $VAR["method"]["$method"];
        $ii = 0;

        if(isset($VAR["method"]["$method"]["trigger_success"]))
            if($VAR["method"]["$method"]["trigger_success"] != '')
            {
                $construct_xml .= '
                <success>' . $VAR["method"]["$method"]["trigger_success"] . '</success>';
            }

        if(isset($VAR["method"]["$method"]["trigger_failure"]))
            if($VAR["method"]["$method"]["trigger_failure"] != '')
            {
                $construct_xml .= '
                <failure>' . $VAR["method"]["$method"]["trigger_failure"] . '</failure>';
            }

        $construct_xml .= '
        </' . $method . '>';
    }

$construct_xml .= '
     </trigger>
</construct>';

return $construct_xml;
}











    #######################################################
    ##          GENERATE THE INSTALL XML                ###
    #######################################################
     function dev_install_xml($VAR)
    {
               $xml = '<?xml version="1.0" encoding="ISO-8859-1" ?'.''.'>';
               $install_xml = $xml . '
<install>

        <!-- Define the main module properties -->
        <module_properties>
            <name>' .           $VAR["module"]          . '</name>
            <parent>' .         $VAR["module_parent"]   . '</parent>
            <notes>' .          $VAR["module_notes"]    . '</notes>
            <menu_display>' .   $VAR["module_menu_display"]    . '</menu_display>
            <dependancy>' .     $VAR["dependancy"]             . '</dependancy>
            <sub_modules>'.     $VAR["module_sub_module"]       .'</sub_modules>
        </module_properties>';

               
         /*
          for($i=0; $i < count($VAR["group_type"]); $i++)
         {
             if($VAR["group_type"]["$i"] != '')
             {
                 $install_xml .='
            <' . $VAR["group_type"]["$i"] . '>1</' . $VAR["group_type"]["$i"] . '>';
             } else {
                 $install_xml .='
            <' . $VAR["group_type"]["$i"] . '>0</' . $VAR["group_type"]["$i"] . '>';
             }
         }
         */

        $install_xml .= ' 

        <!-- Define any SQL inserts for this module -->
        <sql_inserts>
            <module_method>';

        # loop through the methods
        for($i=0; $i < count($VAR["m"]); $i++)
        {
            $method = $VAR["m"]["$i"];
            
            if(isset($VAR["method"]["$method"]["method_notes"]))
            {
                $notes = $VAR["method"]["$method"]["method_notes"];
            } else {
                $notes = '';
            }
            if(isset($VAR["method"]["$method"]["method_page"]))
            {
                $page  = $VAR["method"]["$method"]["method_page"];
            } else {
                $page = '';
            }
            
            if(isset($VAR["method"]["$method"]["method_display"]))
            {
                $display = $VAR["method"]["$method"]["method_display"];
            } else {
                $display = '';
            }
            
            $page = preg_replace('/&/','&amp;', $page);
            
            $install_xml .= '
                <' . $method . '>
                    <module_id>%%module_id%%</module_id>
                    <name>' . $method . '</name>
                    <notes>' . $notes . '</notes>
                    <page>' . $page . '</page>
                    <menu_display>' . $display . '</menu_display>
                </' . $method . '>';
                
            if($method == 'search')
            {
                $install_xml .= '
                <search_form>
                    <module_id>%%module_id%%</module_id>
                    <name>search_form</name>
                    <notes>Allow users to view the search form</notes>
                </search_form>
                <search_show>
                    <module_id>%%module_id%%</module_id>
                    <name>search_show</name>
                    <notes>Allow users to view the search results</notes>
                </search_show>
                ';
            }
        }

        $install_xml .= '
        </module_method>
    </sql_inserts>
</install>';

        return $install_xml;
    }

?>