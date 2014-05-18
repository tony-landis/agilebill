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
	
class product_img
{

	# Open the constructor for this mod
	function product_img()
	{
		# name of this module:
		$this->module = "product_img";

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



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		global $_FILES, $C_translate, $C_debug;

		$imgarr = Array('jpeg','jpg','gif','bmp','tif','tiff','png');

		if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
		{
			for($i=0; $i<count($imgarr); $i++)
			{
				if(preg_match('@'.$imgarr[$i].'$@i', $_FILES['upload_file1']['name']))
				{
					$filename = $imgarr[$i];
					$i = 10;
				}
			}
			if(empty($filename))
			{
				echo 'You must upload a image file (jpg,gif,bmp,tiff,png)';
				return;
			}
			else
			{
				$VAR["product_img_name"] = $_FILES['upload_file1']['name'];
				$VAR["product_img_url"] = $filename;
				$VAR["product_img_type"] = "0";
			}
		}
		else
		{
			if(empty($VAR["img_url"]))
			{
				echo 'You must either upload a image or specify an image URL!';
				return;
			}
			else
			{
				$VAR["product_img_name"] = $VAR["img_url"];
				$VAR["product_img_url"]  = $VAR["img_url"];
				$VAR["product_img_type"] = "1";
			}
		}

		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$result 	= $db->add($VAR, $this, $type);

		# copy the image		
		if($result && !empty($filename))
		{
			$file = 'prod_img_' . $result . '.' . $filename;
			copy($_FILES['upload_file1']['tmp_name'], PATH_IMAGES . "" . $file);
		}
	}

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


	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$db = &DB();

		### Delete any saved images:
		if(isset($VAR["delete_id"]))
		{
			$id = explode(',',$VAR["delete_id"]);
		}
		elseif (isset($VAR["id"]))
		{
			$id = explode(',',$VAR["id"]);
		}

		for($i=0; $i<count($id); $i++)
		{
			if($id[$i] != '')
			{
				# get the filetype
				$sql    = 'SELECT url,type FROM ' . AGILE_DB_PREFIX . 'product_img WHERE
							site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
							id          = ' . $db->qstr($id[$i]);
				$result = $db->Execute($sql);
				if($result->RecordCount() > 0)
				{
					if($result->fields['type'] == "0")
					{
						unlink(PATH_IMAGES . 'prod_img_' . $id[$i] . '.' . $result->fields['url']);
					}

					# delete the record
					$sql    = 'DELETE FROM ' . AGILE_DB_PREFIX . 'product_img WHERE
								site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
								id          = ' . $db->qstr($id[$i]);
					$result = $db->Execute($sql);
				}  			
			}					
		}                    		


		# Alert delete message
		global $C_debug, $C_translate;
		$C_translate->value["CORE"]["module_name"] = $C_translate->translate('name',$this->module,"");
		$message = $C_translate->translate('alert_delete_ids',"CORE","");
		$C_debug->alert($message);	 		
	}	


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

}
?>