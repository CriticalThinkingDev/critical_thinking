<?php
/* * **********************************************************************
  © 2014 Dydacomp Development Corporation.   All rights reserved.
  DYDACOMP, FREESTYLE COMMERCE, and all related logos and designs are
  trademarks of Dydacomp or its affiliates.
  All other product and company names mentioned herein are used for
  identification purposes only, and may be trademarks of
  their respective companies.
 * ********************************************************************** */
function escapeInventoryFileData($str_data) 
{
	// 2012 june 27 - PJQ - For Hyperdrug/justpetfood
	$str_data = str_replace("amp;", "", $str_data);
	
	// 2014-10-02 PJQ - BSXL-136 - backtick
	$str_data = str_replace("`", "'", $str_data);

	$searched_data = array("\r\n", "\n", "\r", "\t", "\\");
	$replaced_data = ' ';
	return str_replace($searched_data, $replaced_data, $str_data);
}

class FreestyleSolutions_BizSyncXL_Model_Product extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('bizsyncxl/product');
    }
	
	/**
	 * ConsumeJSON function
	 *
	 * @return string  "OK" on success, otherwise error message
	 * @author Paul Quirnbach
	 **/
	public function ConsumeJSON( $json )
	{
		//Mage::Log(__METHOD__, Zend_Log::INFO, "bizsyncxl.log");
		
		$parametrsArray = (array)json_decode($json);
		
		$row = $parametrsArray[0];
		
		if( $row[0] != "prices" && $row[0] != "quantity" && $row[0] != "product")
		{
			$ret_msg = __METHOD__ . " Invalid Data - action must be product , prices or quantity";
			Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
			return $ret_msg;
		}
		
		$sku = $row[1];
		
		if( $sku == "" )
		{
			$ret_msg = __METHOD__ . " SKU Empty";
			Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
			return $ret_msg;
		}
		
		if( $row[0] == "prices" )
		{
			//return $this->SyncPrices_ctype($parametrsArray);
			return $this->SyncPrices($parametrsArray);
			
		}
		
		if( $row[0] == "quantity" )
		{
			return $this->SyncQuantity($parametrsArray);
			
		}
		
		if( $row[0] == "product" )
		{
			return $this->SyncProduct($parametrsArray);
			
		}
		
		return "OK";
	}
	
	/**
	 * SyncPrices_ctype function
	 *
	 * @return string  "OK" on success, otherwise error message
	 * @author Paul Quirnbach
	 **/
	public function SyncPrices_ctype($parametrsArray) 
	{	
		$CustomerType = "ctype";  // ctype, ctype2, ctype3
		$default_group_id = 'all';

		$row = $parametrsArray[0];
		$sku = $row[1];		
		$price1 = $row[2];
		$store_id = (count($row) > 3 && is_numeric($row[3])) ? $row[3] : 1;
		
		
		
		if( !is_numeric($price1) )
		{
			$ret_msg = "Normal Selling Price cannot be empty";
			Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
			return $ret_msg;
		}
		
		
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));		
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($sku);
		
		if( !is_numeric($productId) )
		{
			$ret_msg = "SKU " . $sku . " not found in Magento catalog";
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			return "OK"; // BSXL-179 2014-12-03 PJQ //return $ret_msg;
		}
		
		if( intval($productId) > 0 )
		{
			//$prod_api =  new Mage_Catalog_Model_Product_Api();
			// set the price to the 'normal retail selling price' in MOM.
			$product['price'] = $price1;
			
			$prod->load($productId);
			if ($prod && $prod->getId()) 
			{
				//$prod->setStoreId(intval($store_id));
				$prod->setPrice($price1);
				
				
			}
			$ret_msg = "SKU " . $sku . " Normal Selling Price Updated." ;
			
			$tierprices = array();
			$groupprices = array();
			$updated_special_price = 0;
			
			foreach($parametrsArray as $row)
			{
				$product2 = array();
				
				// skip first row, we already processed it
				if( $row[0] == "prices")
				{
					continue;
				}
				
				//"select rtrim(NUMBER), NPOSITION, PRICE, DISCOUNT, COSTPLUS, QTY, rtrim(CL_KEY), TOTAL_DOL, ";
				//rtrim(CATCODE), rtrim(CTYPE), rtrim(CTYPE2), rtrim(CTYPE3),  FORMAT(DATE_START,'u'),  FORMAT(DATE_END.'u'), CUSTNUM, RFM, rtrim(ORDERTYPE), 
				//rtrim(COSTMETHOD), rtrim(PLEVEL), PRICE_ID ";
				$stocknumber = $row[0];
				if($stocknumber != $sku)
				{
					continue;
				}
				$npos  = $row[1];
				$price  = $row[2];
				$discount  = $row[3];
				$costplus  = $row[4];
				$qty  = $row[5];
				$coupon  = $row[6];
				$total_dol  = $row[7];
				$catcode  = $row[8];
				$ctype  = $row[9];
				$ctype2  = $row[10];
				$ctype3  = $row[11];
				$from_date  = $row[12];
				$to_date  = $row[13];
				$custnum  = $row[14];
				$rfm  = $row[15];
				$ordertype  = $row[16];
				$costmethod  = $row[17];
				$plevel  = $row[18];
				$price_id  = $row[19];
				
				// check modifiers
				// for 1.1.x we do not support discounts coming from MOM that have Ctype's or other exclusions (i.e. date ranges, catalog codes)
				// We need to make sure that we don't add them if we have any on the price line coming in that have these modifiers.
				// for 2.0, this is not going to be the case.
				$has_price_modifier = false;
				
				if ($ctype != '' 		||
					$ctype2 != ''		||
					$ctype3 != ''		||
					$coupon != ''		||
					$catcode != ''		||
					$total_dol != ''	||
					$rfm  != ''			||
					$ordertype != ''	||
					$custnum != '')
				{
					$has_price_modifier = true;
					// comment out the continue to do customer types and other modifiers
					//continue;
				}
				
				// 2014-08-14 PJQ - plevel W (web) and B (both) are good, along with empty value
				// plevel N (non web orders) is not good
				if ($plevel == 'N')
				{
					continue;
				}
				
				// gym: 08/16/11 - make sure we do percentage off discounts.
				// convert to a regular price.
				if($discount > 0)
				{
					// BSXL-113 2014-08-18 PJQ - if price record has a price, discount from that otherwise discount from price1
					if( $price > 0 )
					{
						$price = $price * (1 - ($discount / 100));
					}
					else
					{
						if( $price1 > 0 )
						{
							$price = $price1 * (1 - ($discount / 100));
						}
					}
				}
				
				//if its qty 1, then it needs to go into magento "special price" as magento tier prices must be qty > 1
				if($qty == '1' && !$has_price_modifier)
				{
				
					if (floatval ($price) < floatval ($price1) && floatval($price) > 0 )
					{
						
						//$special_price = $price;
						$product2['special_price'] = $price;
						if ($from_date != '')
						{
							$product2['special_from_date'] = substr($from_date, 0, 10);
						} else {
							$product2['special_from_date'] = '';
						}
						if ($to_date != '')
						{
							$product2['special_to_date'] = substr($to_date, 0, 10);
						} else {
							$product2['special_to_date'] = '';
						}	

						
					} else {
						// clear the special price.
						$product2['special_from_date'] = '';
						$product2['special_to_date'] = '';
						$product2['special_price'] = '';
					}
					
					// 2013dec17 PJQ - add 4th parameter identifierType to call
					//$prod_api->update($productId, $product2, intval($store_id), "id");
					//$prod->load($productId);
					
					if ($prod && $prod->getId()) 
					{
						//$prod->setStoreId(intval($store_id));
						$prod->setSpecialFromDate($product2['special_from_date']);
						$prod->setSpecialFromDateIsFormated(true);
						$prod->setSpecialToDate($product2['special_to_date']);
						$prod->setSpecialToDateIsFormated(true);
						$prod->setSpecialPrice($product2['special_price']);
						//$prod->save();
					}
					
					$ret_msg .= " Set special price." ;
					$updated_special_price = 1;
					
					continue;
				
				} // END magento "special price"
				
			
				// make sure we don't have a price modifier that is not supported
				$has_unsupported_price_modifier = false;
				
				if (//$ctype != '' 		||
					//$ctype2 != ''		||
					//$ctype3 != ''		||
					$coupon != ''		||
					$catcode != ''		||
					$total_dol != ''	||
					$rfm  != ''			||
					$ordertype != ''	||
					$plevel  != ''		||
					$custnum != '')
				{
					$has_unsupported_price_modifier = true;
					
					continue;
				}
				
				// figure out the group ID
				$group_id = $this->GetGroupID($ctype, $ctype2, $ctype3, $CustomerType, $default_group_id);
				
				if( $qty == 1 )
				{
					Mage::log("Customer " . $ctype . " price " . $price . " for SKU " . $sku, Zend_Log::INFO, "bizsyncxl.log");
					$groupprice['cust_group'] = $group_id;
					$groupprice['website_id'] = 'all';
					$groupprice['price'] = $price;
					
					$groupprices[] = $groupprice;
				}
				else
				{
					$tierprice['customer_group_id'] = $group_id;
					$tierprice['website_id'] = 'all';
					$tierprice['qty'] = $qty;
					$tierprice['price'] = $price;
					$tierprice['from_date'] = $from_date;
					$tierprice['to_date'] = $to_date;
				
				
				
				
					// Tier Pricing
					$tierprices[] = $tierprice;
									//array('website'   => 'all',
								  //'customer_group_id' => $default_group_id,
								  //'qty'               => $qty,
								  //'price'             => $price);
				}
			}
			
			// do the actually save of the regular price and special price (if there was one)
			if ($prod && $prod->getId()) 
			{
				$prod->save();
			}

			
			// save the tierprices
			//if( count($tierprices) > 0)  // what if they used to have tierprices but then removed them ? we need to delete them.
			{
				$mcmpata = null;
				//$mcmpata = new Mage_Catalog_Model_Product_Attribute_Tierprice_Api();
				if (Mage::helper('core')->isModuleEnabled(ChannelBrain_Tierpricedates))
				{
					$mcmpata = new ChannelBrain_Tierpricedates_Model_Product_Attribute_Tierprice_Api();
					Mage::log("ChannelBrain_Tierpricedates module being used.", Zend_Log::INFO, "bizsyncxl.log");
					$ChannelBrain_Tierpricedates = true;
				} else {
					// Use Mage core.
					$mcmpata = new Mage_Catalog_Model_Product_Attribute_Tierprice_Api();
				}	
				// Replace tier prices in magento
				if( is_object( $mcmpata ) )
				{
					if ($mcmpata->update($productId, $tierprices, "id")) {
						$ret_msg .= " Tier Prices Updated.";
					} else {
						$ret_msg .= " Tier Prices failed to update.";
					}
				}
			}
			
			if( !$updated_special_price )
			{
				// clear the special price.
				$prod_api =  new Mage_Catalog_Model_Product_Api();
				$product2['special_from_date'] = '';
				$product2['special_to_date'] = '';
				$product2['special_price'] = '';
				$prod_api->update($productId, $product2, intval($store_id), "id");
				$ret_msg .= " Clear special price.";
			}
			
			// save group prices
			//if( count($groupprices) > 0 ) // what if they used to have group price but deleted them? we need to remove them
			{
				// clear the special price.
				$prod_api =  new Mage_Catalog_Model_Product_Api();
				$product3['group_price'] = $groupprices;
				$prod_api->update($productId, $product3, intval($store_id), "id");
				$ret_msg .= " Set Customer Group price.";
			}
			$time2 = microtime(true);
			$ret_msg .= " time=" . ($time2 - $time1);
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			
			return "OK";				
		}
		
		
		//Mage::Log(sprintf('Line %s in file %s',__LINE__, __FILE__));
		return "OK";
				
	}
	
	public function GetGroupID($ctype, $ctype2, $ctype3, $CustomerType, $default_group_id)
	{
		// initialize it to the default and if we don't find anything we will return that
		$group_id = $default_group_id;
		
		$ctypes = array();
		$ctypes['ctype'] = $ctype;
		$ctypes['ctype2'] = $ctype2;
		$ctypes['ctype3'] = $ctype3;
		
		// for Magento we can only have ONE Ctype (group) for a price tier.
		// let's make sure we set that now.  This is globally set in the bizsync.globals.inc.php OR PASSED IN TO THIS FUNCTION
		$CTypeForGroup = $ctypes[$CustomerType];
		try
		{
			if ($CTypeForGroup != '')
			{
				// let's go get the actual group code object.
				$customer_discount_group = Mage::getModel('customer/group')
												->getCollection()
												->addFieldToFilter('customer_group_code', array('eq' => $CTypeForGroup))
												->getFirstItem();

				if (is_object ($customer_discount_group))
				{
						$group_id = $customer_discount_group->getId();
						// let's make sure we have a group id.
						if (strval($group_id) == '')
						{
							// no group id found, was it originally created?!
							$ret_msg =  " price group value not found for '" . $CTypeForGroup . "'. Group needs to be created first under 'Customer Groups' for customers.";
							Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
						}
				} else {
					// no group id found, was it originally created?!
					$ret_msg = $sku . " price group value not set for '" . $CTypeForGroup . "'. This group needs to be created first under 'Customer Groups' for customers.";
					//Ctype: '" . $ctype . "', ctype2: '" . $ctype2 . "', ctype3: '" . $ctype3 . "', coupon: '" . $coupon . "', catalog: '" . $catalog . "', custnum: '" . $custnum . "'");
					Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
				}
			} 
		} catch (Exception $e) {
			$ret_msg = "Customer Group: Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $sku . "'";
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
		}
		
		return $group_id;
	}
	
	/**
	 * SyncPrices function
	 *
	 * @return string  "OK" on success, otherwise error message
	 * @author Paul Quirnbach
	 **/
	public function SyncPrices($parametrsArray) 
	{	
		$row = $parametrsArray[0];
		$sku = $row[1];		
		$price1 = $row[2];
		$store_id = (count($row) > 3 && is_numeric($row[3])) ? $row[3] : 1;
		
		
		
		if( !is_numeric($price1) )
		{
			$ret_msg = "Normal Selling Price cannot be empty";
			Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
			return $ret_msg;
		}
		
		
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));		
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($sku);
		
		if( !is_numeric($productId) )
		{
			$ret_msg = "SKU " . $sku . " not found in Magento catalog";
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			return "OK"; // BSXL-179 2014-12-03 PJQ //return $ret_msg;
		}
		
		if( intval($productId) > 0 )
		{
			//$prod_api =  new Mage_Catalog_Model_Product_Api();
			// set the price to the 'normal retail selling price' in MOM.
			$product['price'] = $price1;
			
			// 2013dec17 PJQ - add 4th parameter identifierType to call
			//$prod_api->update($productId, $product, intval($store_id), "id");	
			$prod->load($productId);
			if ($prod && $prod->getId()) 
			{
				//$prod->setStoreId(intval($store_id));
				$prod->setPrice($price1);
				
				
			}
			$ret_msg = "SKU " . $sku . " Normal Selling Price Updated." ;
			
			$tierprices = array();
			$updated_special_price = 0;
			
			foreach($parametrsArray as $row)
			{
				$product2 = array();
				
				// skip first row, we already processed it
				if( $row[0] == "prices")
				{
					continue;
				}
				
				//"select rtrim(NUMBER), NPOSITION, PRICE, DISCOUNT, COSTPLUS, QTY, rtrim(CL_KEY), TOTAL_DOL, ";
				//rtrim(CATCODE), rtrim(CTYPE), rtrim(CTYPE2), rtrim(CTYPE3),  FORMAT(DATE_START,'u'),  FORMAT(DATE_END.'u'), CUSTNUM, RFM, rtrim(ORDERTYPE), 
				//rtrim(COSTMETHOD), rtrim(PLEVEL), PRICE_ID ";
				$stocknumber = $row[0];
				if($stocknumber != $sku)
				{
					continue;
				}
				$npos  = $row[1];
				$price  = $row[2];
				$discount  = $row[3];
				$costplus  = $row[4];
				$qty  = $row[5];
				$coupon  = $row[6];
				$total_dol  = $row[7];
				$catcode  = $row[8];
				$ctype  = $row[9];
				$ctype2  = $row[10];
				$ctype3  = $row[11];
				$from_date  = $row[12];
				$to_date  = $row[13];
				$custnum  = $row[14];
				$rfm  = $row[15];
				$ordertype  = $row[16];
				$costmethod  = $row[17];
				$plevel  = $row[18];
				$price_id  = $row[19];
				
				// check modifiers
				// for 1.1.x we do not support discounts coming from MOM that have Ctype's or other exclusions (i.e. date ranges, catalog codes)
				// We need to make sure that we don't add them if we have any on the price line coming in that have these modifiers.
				// for 2.0, this is not going to be the case.
				$has_price_modifier = false;
				
				if ($ctype != '' 		||
					$ctype2 != ''		||
					$ctype3 != ''		||
					$coupon != ''		||
					$catcode != ''		||
					$total_dol != ''	||
					$rfm  != ''			||
					$ordertype != ''	||
					$custnum != '')
				{
					$has_price_modifier = true;
					// comment out the continue to do customer types and other modifiers
					continue;
				}
				
				// 2014-08-14 PJQ - plevel W (web) and B (both) are good, along with empty value
				// plevel N (non web orders) is not good
				if ($plevel == 'N')
				{
					continue;
				}
				
				
				// gym: 08/16/11 - make sure we do percentage off discounts.
				// convert to a regular price.
				if($discount > 0)
				{
					// BSXL-113 2014-08-18 PJQ - if price record has a price, discount from that otherwise discount from price1
					if( $price > 0 )
					{
						$price = $price * (1 - ($discount / 100));
					}
					else
					{
						if( $price1 > 0 )
						{
							$price = $price1 * (1 - ($discount / 100));
						}
					}
				}
				
				//if its qty 1, then it needs to go into magento "special price" as magento tier prices must be qty > 1
				if($qty == '1' && !$has_price_modifier)
				{
				
					if (floatval ($price) < floatval ($price1) && floatval($price) > 0 )
					{
						
						//$special_price = $price;
						$product2['special_price'] = $price;
						if ($from_date != '')
						{
							$product2['special_from_date'] = substr($from_date, 0, 10);
						} else {
							$product2['special_from_date'] = '';
						}
						if ($to_date != '')
						{
							$product2['special_to_date'] = substr($to_date, 0, 10);
						} else {
							$product2['special_to_date'] = '';
						}	

						//if ($price_message != '')
						//{
						//	$price_message = $price_message . " and Special price";
						//} else {
						//	$price_message .= "Special price";
						//}
					} else {
						// clear the special price.
						$product2['special_from_date'] = '';
						$product2['special_to_date'] = '';
						$product2['special_price'] = '';
					}
					
					// 2013dec17 PJQ - add 4th parameter identifierType to call
					//$prod_api->update($productId, $product2, intval($store_id), "id");
					//$prod->load($productId);
					
					if ($prod && $prod->getId()) 
					{
						//$prod->setStoreId(intval($store_id));
						$prod->setSpecialFromDate($product2['special_from_date']);
						$prod->setSpecialFromDateIsFormated(true);
						$prod->setSpecialToDate($product2['special_to_date']);
						$prod->setSpecialToDateIsFormated(true);
						$prod->setSpecialPrice($product2['special_price']);
						//$prod->save();
					}
					
					$ret_msg .= " Set special price." ;
					$updated_special_price = 1;
					
					continue;
				
				} // END magento "special price"
				
				// Tier Pricing
				$tierprices[] = array('website'   => 'all',
							  'customer_group_id' => $default_group_id,
							  'qty'               => $qty,
							  'price'             => $price);
			}
			
			
			if ($prod && $prod->getId()) 
			{
				$prod->save();
			}
			
			
			if( count($tierprices) > 0)
			{
				$mcmpata = new Mage_Catalog_Model_Product_Attribute_Tierprice_Api();
				// Replace tier prices in magento
				if ($mcmpata->update($productId, $tierprices, "id")) {
					$ret_msg .= " Tier Prices Updated.";
				} else {
					$ret_msg .= " Tier Prices failed to update.";
				}
			}
			
			if( !$updated_special_price )
			{
				// clear the special price.
				$prod_api =  new Mage_Catalog_Model_Product_Api();
				$product2['special_from_date'] = '';
				$product2['special_to_date'] = '';
				$product2['special_price'] = '';
				$prod_api->update($productId, $product2, intval($store_id), "id");
				$ret_msg .= " Clear special price.";
			}
			
			$time2 = microtime(true);
			$ret_msg .= " time=" . ($time2 - $time1);
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			
			return "OK";				
		}
		
		
		//Mage::Log(sprintf('Line %s in file %s',__LINE__, __FILE__));
		return "OK";
				
	}
	
	/**
	 * SyncQuantity function
	 *
	 * @return string "OK" on success, otherwise error message
	 * @author Paul Quirnbach
	 **/
	public function SyncQuantity($parametrsArray) 
	{	
		$row = $parametrsArray[0];
		$sku = $row[1];		
		$qty = $row[2];
		$store_id = (count($row) > 3 && is_numeric($row[3])) ? $row[3] : 1;
		$manage_inventory = (count($row) > 4 && is_numeric($row[4])) ? $row[4] : 1;
		$deldate = (count($row) > 5 ) ? $row[5] : "";
		
		
		if( !is_numeric($qty) )
		{
			$ret_msg = "SKU " . $sku . " Quantity must be numeric";
			Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
			return "OK"; // BSXL-179 2014-12-03 PJQ //return $ret_msg;
		}
		
		
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));		
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($sku);
		
		if( !is_numeric($productId) )
		{
			$ret_msg = "SKU " . $sku . " not found in Magento catalog";
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			return "OK"; // BSXL-179 2014-12-03 PJQ //return $ret_msg;
		}
		
		if( intval($productId) > 0 )
		{
			$in_stock = ($qty > 0) ? 1 : 0;
			
			$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();
			//$product_stock->update($productId, array('qty' => $qty, 'is_in_stock' => $in_stock), intval($store_id));
			// 2013jan10 PJQ - looking at the source code for Mage_CatalogInventory_Model_Stock_Item_Api::update, it always does its own "getIdBySku"
			// and if it finds one, it changes the productId to the productId it found, 
			// so the only way to be sure to update the proper item's quantity is to pass in the SKU
			//$product_stock->update($sku, array('qty' => $qty, 'is_in_stock' => $in_stock, intval($store_id)));
			$product_stock->update($sku, array('qty' => $qty, 'is_in_stock' => $in_stock, 'use_config_manage_stock'=>$manage_inventory, 'manage_stock'=>$manage_inventory), intval($store_id));
					
			$ret_msg = "SKU " . $sku . " quantity set to " . $qty;
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			//return "OK";
		}
		
		if($deldate != "")
		{
			$ret_msg = $sku . " Delivery date: " . $deldate;
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			
			// put it in po_date ?
			//$prod_api =  new Mage_Catalog_Model_Product_Api();
			//$product['po_date'] = $deldate;
			//$prod_api->update($productId, $product, intval($store_id));
		}
		
		return "OK";
	}
	
	
	/**
	 * SyncProduct function
	 *
	 * @return string "OK" on success, otherwise error message
	 * @author Paul Quirnbach
	 **/
	public function SyncProduct($parametrsArray) 
	{	
		$row = $parametrsArray[0];
		$sku = $row[1];		
		$data = base64_decode($row[2]);
		$data = json_decode($data);
		
		$base_product_name = $data[3];
		
		$wgt = $data[5];
		$short_description = $data[3];
		$sizecolor_description = $data[4];
		$description = $data[8];
		$price = $data[1];
		$qty = $data[2];
		
		$taxable = ($data[6]) == "1" ? 0 : 2;
		
		$size_color = $data[9];
		$product_name = trim($base_product_name . " " . $sizecolor_description);
		$dropship = $data[10];
		$construct = $data[11]; // MOM speak for "Kit"
		$discont = $data[12];
		$is_active = 1;
		if( $discont == 1 && $qty <= 0 )
		{
			$is_active = 2;  // disabled is 2
		}
		$inetsdesc = $data[13];
		$inetshortd = $data[14];
		$inetfdesc = $data[15];
		$inetsell = $data[16];
		
		$store_id = (count($row) > 3 && is_numeric($row[3])) ? $row[3] : 1;
		$manage_inventory = (count($row) > 4 && is_numeric($row[4])) ? $row[4] : 1;
		$sync_short_desc = (count($row) > 5 && is_numeric($row[5])) ? $row[5] : 1;
		$sync_long_desc = (count($row) > 6 && is_numeric($row[6])) ? $row[6] : 1;
		$AssignToWebStore = (count($row) > 7 && is_numeric($row[7])) ? $row[7] : 1;
		
		//if( !is_numeric($qty) )
		//{
		//	$ret_msg = "SKU " . $sku . " Quantity must be numeric";
		//	Mage::Log($ret_msg, Zend_Log::ERR, "bizsyncxl.log");
		//	return $ret_msg;
		//}
		
		
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));		
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($sku);
		
		// map channelbrain to Magento.
		$omx2mage_product_data_map = array(
			'name'              => escapeInventoryFileData($product_name),
			'weight'	    	=> $wgt ? $wgt : 1,
			//'short_description' => escapeInventoryFileData($short_description),		
			//'description'       => escapeInventoryFileData($description),
			'price'             =>  $price,
			'tax_class_id'		=> $taxable,
			'status'	=> $is_active
		);
		
		if( $size_color )
		{
			$omx2mage_product_data_map['visibility'] = 1; // do not show individually
		}
		
		if( $sync_short_desc )
		{
			$omx2mage_product_data_map['short_description'] = escapeInventoryFileData($short_description); 
		}
		
		if( $sync_long_desc )
		{
			$omx2mage_product_data_map['description'] = escapeInventoryFileData($description); 
		}
		
		if( intval($productId) > 0 )
		{
			// update
			$prod_api =  new Mage_Catalog_Model_Product_Api();
			$prod_api->update($productId, $omx2mage_product_data_map, Mage_Core_Model_App::ADMIN_STORE_ID, "id");
			$ret_msg = $sku . " updated";
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			
			if($manage_inventory && !$dropship && !$construct)
			{
				$in_stock = ($qty > 0) ? 1 : 0;
				$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();
				$manage_stock_array = array('qty' => $qty, 'is_in_stock' => $in_stock, 'use_config_manage_stock'=>$manage_inventory, 'manage_stock'=>$manage_inventory);
				$product_stock->update($sku, $manage_stock_array, intval($store_id));
			}
			
			if( $size_color )
			{
				// update parent info
				$parentsku = trim(substr($sku, 0, 10));
				$productId = $prod->getIdBySku($parentsku);
				$omx2mage_product_data_map['name'] = $base_product_name;
				$omx2mage_product_data_map[visibility] = 4; // catalog, search
				
				if( intval($productId) > 0 )
				{
					$prod_api->update($productId, $omx2mage_product_data_map, Mage_Core_Model_App::ADMIN_STORE_ID, "id");
					$ret_msg = $parentsku . " updated";
					Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
				}
			}
		}
		else
		{
			
			
			$prod_type = "simple";
			$default_attribute_set_id = 4;
			$attribute_name = "size";
			$attribute_id = 0;
			$attribute_value_id = 0;
			$itemcode =$sku;
			if( $size_color )
			{
				$attribute_id = $this->GetAttibuteId($attribute_name, $default_attribute_set_id);
				//print("attribute_id= " . $attribute_id);
				//$ret_msg = "attribute_id= " . $attribute_id;
				//Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
				$attribute_value_id = $this->GetAttibuteValueId($attribute_id, $sizecolor_description);
				//$ret_msg = "attribute_value_id= " . $attribute_value_id;
				//Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
				$omx2mage_product_data_map[$attribute_name] = $attribute_value_id;  //168;
			}
			$websiteids = array();     // it is new, it won't be in any yet... $prod->getWebsiteIds();
			if ($AssignToWebStore == 1)
			{
				// now add the store id this product was told to go into.
				// BSXL-98 2014-07-23 PJQ - set the website based on the store
				$store_model = Mage::getModel('core/store');
				$store_data =  $store_model->load($store_id);
				$websiteids[] = $store_data->getWebsiteId(); //$BizSyncStoreID;
				$websites['websites'] = array_unique ($websiteids);
				$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
			}
			try
			{
				$prod_api =  new Mage_Catalog_Model_Product_Api();
				$productId = $prod_api->create($prod_type, $default_attribute_set_id, $itemcode, $omx2mage_product_data_map);
				$ret_msg = $sku . " created";
				Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			}
			catch( Exception $e )
			{
				$ret_msg =  "Exception trying to create sku " . $sku . " " . $e->GetMessage() ;
				Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			}
			
			if($manage_inventory && !$dropship && !$construct)
			{
				$in_stock = ($qty > 0) ? 1 : 0;
				$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();
				$manage_stock_array = array('qty' => $qty, 'is_in_stock' => $in_stock, 'use_config_manage_stock'=>$manage_inventory, 'manage_stock'=>$manage_inventory);
				$product_stock->update($sku, $manage_stock_array, intval($store_id));
			}
			
			if( $size_color )
			{
				$attribute_ids = array();
				$attribute_ids[] = $attribute_id; // 133;
				$subitem_ids = array();
				$subitem_ids[$productId] = array();
				$sur = 0.00;
				$key = $attribute_name; //size
				$val = $attribute_id;  // 133;
				//$subitem_ids[$productId][$key] = array('attribute_id' => $val, 'label' => $sizecolor_description ,'value_index' => $omx2mage_product_data_map['size'], 'pricing_value' => $sur, 'is_percent' => 0);
				$subitem_ids[$productId][$key] = array('attribute_id' => $val, 'label' => $sizecolor_description ,'value_index' => $attribute_value_id, 'pricing_value' => $sur, 'is_percent' => 0);
				
				// set parent info
				$parentsku = trim(substr($sku, 0, 10));
				$productId2 = $prod->getIdBySku($parentsku);
				$prod_type = "configurable";
				
				if( intval($productId2) <= 0 )
				{
					// Create parent
					
					$subitem_skus   = array();
					$subitem_skus[] = $sku;  // this is the child sku
					$omx2mage_product_data_map["associated_skus"] = $subitem_skus;
					$omx2mage_product_data_map['name'] = $base_product_name;
					
					$omx2mage_product_data_map[$attribute_name] = "";
					$omx2mage_product_data_map[visibility] = 4; // catalog, search
										
					$productId2 = $prod_api->create($prod_type, $default_attribute_set_id, $parentsku, $omx2mage_product_data_map);
					$ret_msg = $parentsku . " created";
					Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
					
					$prod->load($productId2);
					$prod->setTypeId($prod_type);
					$prod->getTypeInstance()->setUsedProductAttributeIds ($attribute_ids) ;
					$ConfigurableAttributesAsArray = $prod->getTypeInstance()->getConfigurableAttributesAsArray($prod);
					
					foreach($ConfigurableAttributesAsArray as $key => $val)
					{
						$ConfigurableAttributesAsArray[$key]["store_label"] = $ConfigurableAttributesAsArray[$key]["frontend_label"];
						$ConfigurableAttributesAsArray[$key]["label"] = $ConfigurableAttributesAsArray[$key]["frontend_label"];
						$idx = 0;
						foreach($subitem_ids as $key3 => $val3)
						{
							foreach($val3 as $key2 => $val2)
							{
								$subitem_ids[$productId][$key] = array('attribute_id' => $val, 'label' => $sizecolor_description ,'value_index' => $attribute_value_id, 'pricing_value' => $sur, 'is_percent' => 0);
								$idx++;
							}
						}
					}
					
					$prod->setConfigurableAttributesData($ConfigurableAttributesAsArray);
					
					$prod->setCanSaveConfigurableAttributes(true);
					
										
				}
				else
				{
					$prod->load($productId2);
					
					// 2014-10-17 PJQ - make sure to tell its configurable product
					if( $prod->getTypeId() != "configurable" )
					{
						$prod->setTypeId($prod_type);

						// 2014-10-20 PJQ - force it to be a configutrable
						$prod->setIsMassupdate(true);
						$prod->save();
						$omx2mage_product_data_map2[$attribute_name] = "";
						$prod_api->update($productId2, $omx2mage_product_data_map2, Mage_Core_Model_App::ADMIN_STORE_ID, "id");
					}
					// Attach new variation to parent
					//
					$subitem_ids = $this->my_getConfigurableProductsData($productId2, $attribute_id) ;
														
					$subitem_ids[$productId][$key] = array('attribute_id' => $val, 'label' => $sizecolor_description ,'value_index' => $attribute_value_id, 'pricing_value' => $sur, 'is_percent' => 0);
				}
				
				$prod->setCanSaveCustomOptions(true);
				$prod->setConfigurableProductsData($subitem_ids) ;
				
				// save.
				try
				{
					$prod->setIsMassupdate(true);
					$prod->setExcludeUrlRewrite(true);					
					$prod->getTypeInstance()->save();
				}
				catch( Exception $e )
				{
					$ret_msg =  "Exception in  SyncProduct "  . $e->GetMessage() . serialize($subitem_ids);
					Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
				}
			}
										
				
		}
		
		return "OK";
	}
	
	/**
	 * my_getConfigurableProductsData function.
	 * 
	 * @access public
	 * @param mixed $productId2
	 * @param mixed $attribute_id
	 * @return array $subitem_ids
	 */
	function my_getConfigurableProductsData($productId2, $attribute_id)
	{
		$subitem_ids = array();
		
		try
		{
			$prod = Mage::getModel('catalog/product');
			$prod->load($productId2);
			$con = Mage::getModel('catalog/product_type_configurable')->setProduct($prod);
			$attrs = $con->getConfigurableAttributesAsArray();
			$num = count($attrs);
			//$ret_msg =  "num of attrs " . $num ;
			//Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
			$bfoundAttr = 0;
			
			for($z=0; $z<$num; $z++)
			{
				if($attribute_id == $attrs[$z]["attribute_id"] )
				{
					$attribute_code = $attrs[$z]["attribute_code"];
					
					$attrs = $attrs[$z]["values"];
					
					$bfoundAttr = 1;
					break;
				}
			}
			
			if( !bfoundAttr )
			{
				//print("not found Attr");
				return 0;
			}
		
			$child_ids = Mage::getResourceModel('catalog/product_type_configurable')->getChildrenIds($productId2);
			foreach($child_ids as $ids)
			{
				foreach($ids as $id)
				{
					$prod2 = Mage::getModel('catalog/product');
					$prod2->load($id);
					$prod_api =  new Mage_Catalog_Model_Product_Api();
					$info = $prod_api->info($id);
					$attribute_value_id = $info["size"];
						
					$num = count($attrs);
								
					$sizecolor_description = "";
					for($z=0; $z<$num; $z++)
					{
						if($attribute_value_id == $attrs[$z]["value_index"] )
						{
							$sizecolor_description = $attrs[$z]["label"];
							$sur = $attrs[$z]["pricing_value"];
							$bfound = 1;
							
							break;
						}
					}
				
					$subitem_ids[$id][$attribute_code] = array('attribute_id' => $attribute_id, 'label' => $sizecolor_description ,'value_index' => $attribute_value_id, 'pricing_value' => $sur, 'is_percent' => 0);
				
				}
			}
		}
		catch( Exception $e )
		{
			$ret_msg =  "Exception in  my_getConfigurableProductsData( " . $productId2 . "," . $attribute_id . ")"  . $e->GetMessage();
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
		}
		return $subitem_ids;
		
	}
	
	/**
	 * GetAttibuteValueId function.
	 * 
	 * @access public
	 * @param mixed $attribute_id
	 * @param mixed $sizecolor_description
	 * @return Int AttibuteValueId
	 */
	function GetAttibuteValueId($attribute_id, $sizecolor_description)
	{
		$the_dim_id = 0;
		
		try
		{
			$the_dim = $sizecolor_description; //strval($map_of_dd[$key]["Description"]);

			// we need to get the existings sizes and colors
			//$dim_options = $prod_attr_api->options($attribute_ids[$lname]);
			$attribute_model        = Mage::getModel('eav/entity_attribute');
			$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
			$attribute              = $attribute_model->load($attribute_id);
			$attribute_table        = $attribute_options_model->setAttribute($attribute);
			$dim_options            = $attribute_options_model->getAllOptions(false);

			// now loop thru the dim_options and find the option_id for the_dim
			foreach($dim_options as $d_opt)
			{
				if($the_dim == $d_opt["label"])
				{
					$the_dim_id = $d_opt["value"];
					break;
				}
			}
			// if we did not find it, then create it
			if($the_dim_id == 0)
			{
				
				$attribute_model        = Mage::getModel('eav/entity_attribute');
				$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
				$attribute              = $attribute_model->load($attribute_id);
				
				$new_value['option'] = array($the_dim,$the_dim);
				$result = array('value' => $new_value);
				$attribute->setData('option',$result);
				try
				{
					$attribute->save();
				}
				catch( Exception $e )
				{
					$ret_msg =  "Exception in  GetAttibuteValueId(" . $attribute_id . "," . $sizecolor_description . ") " . $e->GetMessage();
					Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
					return "Error: " . __FUNCTION__ . " on line " . __LINE__ . " : " .  $e->GetMessage() . " : " . $the_dim . "|" . $lname  . "|" . $attribute_ids[$lname] . "|";
				}

				$attribute_table        = $attribute_options_model->setAttribute($attribute);
				$dim_options            = $attribute_options_model->getAllOptions(false);

				// now loop thru the dim_options and find the option_id for the_dim
				foreach($dim_options as $d_opt)
				{
					if($the_dim == $d_opt["label"])
					{
						$the_dim_id = $d_opt["value"];
						break;
					}
				}

				if($the_dim_id == 0 )
				{
					return $the_dim_id;  
				}		
			}
		}
		catch( Exception $e )
		{
			$ret_msg =  "Exception in  GetAttibuteValueId(" . $attribute_id . "," . $sizecolor_description . ") " . $e->GetMessage();
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
		}		
		return $the_dim_id;
	}
	
	/**
	 * GetAttibuteId function.
	 * 
	 * @access public
	 * @param mixed $attribute_name
	 * @param mixed $default_attribute_set_id
	 * @return Int AttibuteId
	 */
	function GetAttibuteId($attribute_name, $default_attribute_set_id)
	{
		$attribute_id = 0;
		
		try
		{
			$prod_attr_api = new Mage_Catalog_Model_Product_Attribute_Api();
			$attrs = $prod_attr_api->items($default_attribute_set_id);
			$num_attra = count($attrs);
			
			$code = strtolower($attribute_name);
			
			for($z=0; $z<$num_attra; $z++)
			{

				if($code == $attrs[$z]["code"] )
				{
					$attribute_id = $attrs[$z]["attribute_id"];
					$bfound = 1;
				}
			}
			if( !$bfound )
			{
				$attribute_id = $this->AddMageAttribute( $code , $code, $default_attribute_set_id);
			}
		}
		catch( Exception $e )
		{
			$ret_msg =  "Exception in  GetAttibuteId( " . $attribute_name. "," . $default_attribute_set_id. ") " . $e->GetMessage();
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
		}		
		return $attribute_id;
	}
	
	/**
	 * AddMageAttribute function.
	 * 
	 * @access public
	 * @param mixed $code
	 * @param mixed $default_attribute_set_id
	 * @return void
	 */
	function AddMageAttribute( $code , $label, $default_attribute_set_id)
	{	
		try 
		{
		
			$type_id = 10; //magic number - from eav_attribute.entity_type_id
			$eav_entity_type = new  Mage_Eav_Model_Entity_Type();
			$eav_entity_type->loadByCode("catalog_product");
			$type_id = $eav_entity_type->getEntityTypeId();
						
			$c = array(
			'entity_type_id' => $type_id,
			'attribute_code' => $code,
			'backend_type'   => 'text',
			'frontend_input' => 'select',
			'is_global'              => '1',
			'is_visible'     => '1',
			'is_required'    => '0',
			'is_user_defined' => '1',
			'position'               => '1',
			'frontend_label' => $label
			);

			//$attribute = new Mage_Eav_Model_Entity_Attribute();
			$attribute   = Mage::getModel('eav/entity_attribute');
			$attribute->loadByCode($c['entity_type_id'], $c['attribute_code'])->setStoreId(0)->addData($c);
			$attribute->save();
			$new_id = $attribute->getId();
			
			// get the table name WITH PREFIX for eav_entity_attribute
			$tableName = Mage::getSingleton('core/resource')->getTableName('eav_entity_attribute');
			//$sql = "insert into eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id) values (";
			$sql = "insert into " . $tableName . " (entity_type_id, attribute_set_id, attribute_group_id, attribute_id) values (";
			$sql .= $type_id . "," . $default_attribute_set_id . ",4," . $new_id . ")";

			// fetch write database connection that is used in Mage_Core module
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$write->query( $sql ); 

		} catch (Exception $e) {
			$ret_msg =  "Exception in  AddMageAttribute( " . $code . "," . $label . "," . $default_attribute_set_id . ") " . $e->GetMessage();
			Mage::Log($ret_msg, Zend_Log::INFO, "bizsyncxl.log");
		}
		return $new_id;
	}
	
	
}