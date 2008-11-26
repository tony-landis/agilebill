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
	
class asset_invoice
{	
	/** get menu of asset pools */
	function main($var) {
		
		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$rs = $db->Execute("select id,name from {$p}asset_pool");
		if($rs && $rs->RecordCount()) {		
			global $smarty;
			$pools=array();
			while(!$rs->EOF) {
				array_push($pools, $rs->fields);
				$rs->MoveNext();
			}
			$smarty->assign('pools', $pools);		
		} 
	}
	
	/** show pending invoices */
	function invoice($v) {
		
		// verify pool
		if(empty($v['pool_id'])) 
			return false;
		else 
			$pool_id = $v['pool_id'];
			
		$db=&DB();
		$p=AGILE_DB_PREFIX;
 
		// get all products ids set for manual assignment or this pool
		$prodIds=array();
		$prod = $db->Execute("select id,prod_plugin_data from {$p}product 
				WHERE prod_plugin = 1 and prod_plugin_file = 'ASSET'");
		if($prod && $prod->RecordCount()) {
			while(!$prod->EOF) { 
				@$d = unserialize($prod->fields['prod_plugin_data']);
				
				// is this asset pool
				if(!empty($d['AssetPool']) && $d['AssetPool'] == $pool_id) {
					
					array_push($prodIds, $prod->fields['id']);
				
				// is manual enabled	
				} elseif(!empty($d['manual']) && $d['manual'] == '1') {
					
					array_push($prodIds, $prod->fields['id']);					
				} 
				$prod->MoveNext();
			}
		} 
		
		if(count($prodIds)==0) {
			echo '<br>No products defined for the selected asset pool or manual assignment.<br>';
			return false;
		}

		$rs = $db->Execute($sql="select distinct a.*, 
				b.first_name, b.last_name, b.city, b.state, b.zip from {$p}invoice a
				join {$p}account b on (a.account_id = b.id)				
				WHERE
				a.billing_status = 1 AND a.process_status <> 1 AND a.refund_status <> 1
				AND a.id in
				( select c.invoice_id from {$p}invoice_item c where 
					c.product_id 
					 in (".implode(',', $prodIds).")  
				) "); 
		if($rs && $rs->RecordCount()) {		
			$invoices=array();
			global $smarty;		 
			while(!$rs->EOF) {
				
				/** select invoice items */
				$rs2 = $db->Execute("select distinct a.id,a.product_id,a.sku,a.quantity, b.name 
					from {$p}invoice_item a
					left join {$p}product_translate b on (a.product_id=b.product_id and language_id='".DEFAULT_LANGUAGE."')
					where a.invoice_id = {$rs->fields['id']}
					and a.product_id in (".implode(',', $prodIds).") 
					");
				
				$itemsJs = '';
				if($rs2 && $rs2->RecordCount()) {
					$i=0;
					$jname = 'items_'. $rs->fields['id'];
					while(!$rs2->EOF) {
  
						$itemsJs .= "{$jname}[{$i}] = {'id':{$rs2->fields['id']}, 'value':0}; \r\n";
						
						$rs->fields['items'][] = $rs2->fields;
						$rs2->MoveNext();	
						$i++;					
					}
				}
				$rs->fields['itemsJs'] = $itemsJs;
				 
				array_push($invoices, $rs->fields);
				$rs->MoveNext();
			}
			$smarty->assign('invoices', $invoices);	 
			
			/** get available assets for this category */
			$rs = $db->Execute("select id,asset as name from {$p}asset where status <> 1 and pool_id = ".$db->qstr($pool_id));
			if($rs && $rs->RecordCount()) {		 
				$assets=array();
				while(!$rs->EOF) {
					array_push($assets, $rs->fields);
					$rs->MoveNext();
				}
				$smarty->assign('assets', $assets);		
			} 					
		}
	} 
	
	/** define asset ids for services and approve invoice */
	function assign($v) {
	
		@$id = $v['invoice_id'];
		@$items = $v['items'];
		
		/** input valid? */
		if(empty($id) || empty($items) || !is_array($items)) {
			echo 'Invalid data passed';
			return false;
		}
		
		$db=&DB();
		$p=AGILE_DB_PREFIX;
				
		// validate all items set
		foreach ($items as $key=>$item) {
			if(empty($item) || $item=='0') {
				echo 'All items must be assigned';
				return false;
			}
						
			// validate that asset is available
			$assetRs=$db->Execute("select a.id,a.asset,b.name as poolName from {$p}asset a, {$p}asset_pool b where a.pool_id=b.id and a.status <> 1 and a.id = ".$db->qstr($item));
			if(!$assetRs || !$assetRs->RecordCount()) {
				echo "Asset $item is already assigned or non-existant!";
				return false;
			} else {	
				$assets[] = array('item'=>$key, 'value'=>$item, 'asset'=> $assetRs->fields['asset'], 'poolname'=>$assetRs->fields['poolName'],);	
			}
		}
			 
		// loop through each line item and update the product_attr_cart field
		foreach ($assets as $asset) {
			
			$item = $asset['item'];
			$value = $asset['value'];
			$name = $asset['asset'];
			$pool = $asset['poolname'];
			
			$s = $db->GetOne($sql="select product_attr_cart from {$p}invoice_item where id = ".$db->qstr($item)." and invoice_id = ".$db->qstr($id) ); 
			if(empty($s)) { 
				$s['AssetId']=$value;
			} else {
				$s = unserialize($s);
				$s['AssetId']=$value; 
			}
			
			@$ss=serialize($s);

			/** formatting for invoice view */
			$old='';
			$oldAttr = $db->Execute("select product_attr from {$p}invoice_item where id = ".$db->qstr($item)." and invoice_id = ".$db->qstr($id) );			
			if($oldAttr && $oldAttr->RecordCount()) $old = $oldAttr->fields['product_attr'];
			$product_attr = "{$pool}=={$name} (ID: {$value})\r\n".$old;		
			
			/** update invoice */
			$db->Execute("update {$p}invoice_item set product_attr=".$db->qstr($product_attr).", product_attr_cart=".$db->qstr($ss)." where id = ".$db->qstr($item)." and invoice_id = ".$db->qstr($id) );
			
			/** update service if exists */
			$db->Execute("update {$p}service set prod_attr=".$db->qstr($product_attr).", prod_attr_cart=".$db->qstr($ss)." where invoice_item_id = ".$db->qstr($item)." and invoice_id = ".$db->qstr($id) );			
		}
		 
		// process invoice => service 
		require_once(PATH_MODULES.'invoice/invoice.inc.php');
		$inv = new invoice();
		$result = $inv->approveInvoice(array('id'=>$id),&$inv);
		
		if($result)
			echo 'true';
		else 
			echo 'Error occurred while approving invoice.'; 
	}
}
?>