<?php
/**
 * FreestyleSolutions_BizSyncXL_Magento class
 *
 * @package FreestyleSolutions_BizSyncXL
 * @author Gary MacDougall
 **/

define ('MODULE_BASE_VERSION', '1.1.9');  


class FreestyleSolutions_BizSyncXL
{
	var $ActionValues;
	var $BizSyncStoreID;
	var $ExtendedGiftMessage;
	var $default_group_id;
	var $enable_special_price;
	var $use_subitem_dimension_label_in_name;
	var $default_attribute_set_id;
	var $restart_indexer;

        //*@AA:07/06/16 10:57:37 AM:BSXL-433
        var $remove_html;

        //*@AA:10/14/16 11:27:10 AM:BSXL-456 
        var $moduleVersion = "1.1.10 - Release 9";  
	var $moduleDate = "04/07/17";
        
        /**
	 * construct function.
	 *
	 * @access public
	 * @return void
	 */
	function FreestyleSolutions_BizSyncXL ($ActionValues)
	{
		$this->ActionValues = $ActionValues;

		$this->BizSyncStoreID = 1;
		$this->ExtendedGiftMessage = true;
		$this->default_group_id = 'all';
		$this->enable_special_price = true;
		$this->use_subitem_dimension_label_in_name = 1;
		$this->restart_indexer = true;

		$this->default_attribute_set_id = Mage::getSingleton('eav/config')
			->getEntityType(Mage_Catalog_Model_Product::ENTITY)
			->getDefaultAttributeSetId();
                
                if ($this->ActionValues['enable_reindex'] == "1") {
                    $this->restart_indexer = true;                   
                }
                else {
                    $this->restart_indexer = false;
                }                

                //*@AA:07/06/16 10:57:37 AM:BSXL-433
                if ($this->ActionValues['remove_html'] == "1") {
                    $this->remove_html = true;                   
                }
                else {
                    $this->remove_html = false;
                }

        }
	/**
	 * Action_SyncCleanup function.
	 *
	 * @access public
	 * @return void
	 */
	public function Action_SyncCleanup()
	{
		writeStartTag("SyncCleanup");
		try {

			$names = trim($this->Start_Magento_Indexer());

			if ($names != "") {
				$names = "[Magento Re-Index Completed]\n" . $names;
			}
			writeElement("Success", 1);
			writeElement("Message", $names);
			writeElement("ResultID", 0);
			writeElement("ErrorData", "");

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}
		writeCloseTag("SyncCleanup");
	}
	/**
	 * Action_GetProductID function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetProductID()
	{
		writeStartTag("GetProductID");
		try
		{
			$sku = $this->ActionValues['sku'];
			$store_id = $this->ActionValues['store_id'];

			// go into admin mode
			Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			$prod = Mage::getModel('catalog/product');
			$productId = $prod->getIdBySku($sku);
			if ($productId > 0)
			{
				writeElement("Success", 1);
				writeElement("Message", $sku . " exists.");
				writeElement("ResultID", $productId);
				writeElement("ErrorData", "");

			} else {
				writeElement("Success", 0);
				writeElement("Message", $sku . " not found.");
				writeElement("ResultID", 0);
				writeElement("ErrorData", "");
			}

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}

		writeCloseTag("GetProductID");


	}
	/**
	 * Action_SyncProduct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_SyncProduct()
	{	
		$data="";
		set_time_limit(0);
                $is_final = $this->ActionValues['is_final'];
                //$enable_product_status = $this->ActionValues['enable_product_status'];
                
		try
		{
			$sku = $this->ActionValues['sku'];
			
			if ($this->ActionValues['compression'] == "1")
			{
				$data = gzuncompress($data);
			}
			$data = base64_decode ($this->ActionValues['data']);			

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}

		try
		{                        
			$this->Stop_Magento_Indexer();
			writeStartTag("SyncProductResponse");
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');

			$table = $resource->getTableName('catalog_product_entity');

			$select = $read->select()
			   ->from($table)
			  ->where('sku = ?',$sku)
			   ->order('sku DESC');

			$row = $read->fetchRow($select);


			if (count($row) && $row != false)
			{
				// Perform update on existing product
				$product_id = intval($row['entity_id']);
				//
				// default_attribute_set_id is 4 because 'Default' is set to 4 in Magento.
				//
				$result = $this->syncExistingProduct($sku, $data, $this->default_attribute_set_id);
				if ($result != true)
				{
					writeElement("Success", 0);		
					writeElement("ResultID", $product_id);
					writeElement("Message", "Action_SyncProduct updating product [" . $sku . "] failed.");
					writeElement("ErrorData", $result);					
				} else {
					writeElement("Success", 1);		
					writeElement("Message",  "[" . $sku . "] updated existing product.");
					writeElement("ResultID",  $product_id);
					writeElement("ErrorData", "");					
				}
			} else {
				// Perform insert of new product 
			        $product_id = $this->syncNewProduct($sku, $data, $this->default_attribute_set_id);
			        if ($product_id > 0)
			        {
					writeElement("Success", 1);		
					writeElement("ResultID", $product_id);
					writeElement("Message", "[" . $sku . "] created new product.");
					writeElement("ErrorData", "");
			        } else {
					writeElement("Success", 0);
					writeElement("ResultID", 0);
					writeElement("Message", "[" . $sku . "] insert failed.");
					writeElement("ErrorData", $product_id);
			        }
			}
		} catch (Exception $e) {
				writeElement("Success", 0);
				writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
				writeElement("ResultID", 0);
				writeElement("ErrorData", $e->GetMessage());

		}
		writeCloseTag("SyncProductResponse");		
	}
	/**
	 * Action_GetStores 
	 *
	 * @access public
	 * @return array
	 *
	 */
	 public function Action_GetStores ()
	 {
	    writeStartTag("Stores");
	    //writeElement("Start", $start);

	    $stores = Mage::getModel('core/store_group')
			->getResourceCollection()
			->setLoadDefault(true)
			->load();

		$stores = $stores->toArray();
		$stores = $stores['items'];

		$storeIds = array();
/*
    [group_id] => 2
    [website_id] => 2
    [name] =>  Store Name
    [root_category_id] => 2
    [default_store_id] => 2
*/

		foreach ($stores as $store) {
			if ($store['default_store_id'] != '0') {
				writeStartTag("Store");
				writeElement("store_id", $store['default_store_id']);
				writeElement("website_id", $store['website_id']);
				writeElement("store_name", $store['name']);
				writeCloseTag("Store");			
			} 
		}

	    writeCloseTag("Stores");				    

	 }
	/**
	 * Action_GetStore function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetStore()
	{
	    // get state name
	    $region_model = Mage::getModel('directory/region');
	    if (is_object($region_model))
	    {
		$state = $region_model->load(Mage::getStoreConfig('shipping/origin/region_id'))->getDefaultName();
	    }

	    $name = Mage::getStoreConfig('system/store/name');
	    $owner = Mage::getStoreConfig('trans_email/ident_general/name');
	    $email = Mage::getStoreConfig('trans_email/ident_general/email');
	    $country = Mage::getStoreConfig('shipping/origin/country_id');
	    $website = Mage::getURL();

	    writeStartTag("Store");
	    writeElement("Name", $name);
	    writeElement("Owner", $owner);
	    writeElement("Email", $email);
	    writeElement("State", $state);
	    writeElement("Country", $country);
	    writeElement("Website", $website);
	    writeCloseTag("Store");
	}
	/**
	 * Action_SyncQuantities function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_SyncQuantities()
	{	  
                $this->Stop_Magento_Indexer();
		writeStartTag("SyncQuantitiesResponse");

		try
		{
			$qty = $this->ActionValues['qty'];
			$sku = $this->ActionValues['sku'];
			$store_id = $this->ActionValues['store_id'];

                        $is_in_stock = trim($this->ActionValues['is_in_stock']);
                        //$enable_product_status = trim($this->ActionValues['enableproductstatus']);
                        
                        
			// go into admin mode
			Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			$prod = Mage::getModel('catalog/product');
			$productId = $prod->getIdBySku($sku);
			if (intval($productId) > 0)
			{
				$in_stock = ($qty > 0) ? 1 : 0;
				if ($productId > 0)
				{
					$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();
                                        //if (($is_in_stock == "1") && ($enable_product_status == "1")) {                                        
                                        // $is_in_stock corresponds to 
                                        // Enable or Disable "Is in Stock" on Porducts based on quantity
                                        // renamed to Allow Inventory "Stock Availability" Updates
                                        if (($is_in_stock == "1")) {
                                           // Update quantitiy and set stock availability to "In Stock" or "Out of Stock" based on quantity
                                           $product_stock->update($productId, array('qty' => $qty, 'is_in_stock' => $in_stock)); 
                                        } else {
                                           // Only update quantity
                                           $product_stock->update($productId, array('qty' => $qty));
                                        }
					writeElement("Success", 1);
					writeElement("Message", $sku . " updated to a quantity of " . $qty . ".");
					writeElement("ResultID", $productId);
					writeElement("ErrorData", "");

				} else {
					writeElement("Success", 0);
					writeElement("Message", "Product not found or invalid sku.");
					writeElement("ResultID", 0);
					writeElement("ErrorData", "");
				}

			} else {
				// no product for quantity
					writeElement("Success", 1);
					writeElement("Message", $sku . " Quantity NOT updated it didn't exist.");
					writeElement("ResultID", $productId);
					writeElement("ErrorData", "");
			}

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception: '" . $e->GetMessage() . "' caught on line " . __LINE__ . " in " . __FUNCTION__ . " for '" . $sku . "'");
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}

		writeCloseTag("SyncQuantitiesResponse");
	}
	/**
	 * Action_SyncQuantities function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_SyncPrices()
	{	  
                $this->Stop_Magento_Indexer();
		writeStartTag("SyncPricesResponse");

		try
		{
                        $store_id = $this->ActionValues['store_id'];
                                                                    
                        // MOM Product SKU
			$sku = $this->ActionValues['sku'];
                                                                        
                        // Method of Pricing: Unit Price
                        // Special Price
			$price = $this->ActionValues['price'];
                        // Discount
			$discount = $this->ActionValues['discount'];

                        // Customer Qualifiers For This Price
                        // Customer Type codes: 1,2,3
			$ctype = $this->ActionValues['ctype'];
			$ctype2 = $this->ActionValues['ctype2'];
			$ctype3 = $this->ActionValues['ctype3'];                        
                        
                        // Order Qualifiers For This Price
                        // Order Minimum Quantity for special price
			$qty = $this->ActionValues['qty'];
                        
                        // Order Catalog Code
                        $catalog = $this->ActionValues['catalog'];
                                                                        			
                        // Order Start Date/End Date
                        $from_date = $this->ActionValues['from_date'];
			$to_date = $this->ActionValues['to_date'];
			
                        // SiteLINK Price Level
                        $pricelevel = $this->ActionValues['pricelevel'];
                        
                        // Order Source Key
			$coupon = $this->ActionValues['coupon'];			
                                                
                        // MOM Normal Retail Selling Price 
                        $price1 = $this->ActionValues['price1'];
                        
                        // flag to determine if price level is removed for product pricing table.
                        // *@AA:01/15/16 12:47:08 PM:BSXL-356
                        // *@AA:01/15/16 12:47:08 PM:BSXL-236
                        $deleteprice = $this->ActionValues['deleteprice'];			
                        
                        $marked_for_deletion = false;
                        if ($deleteprice === "1")
                        {
                            $marked_for_deletion = true;
                        }
                        
			// gym: 08/16/11 - make sure we do percentage off discounts.
			// convert to a regular price.
			if($discount > 0)
			{
                                // round to 2 decimal places
				if ($price == 0) {
                                    $price = round(floatval($price1) * (1 - ($discount / 100)),2);
                                }
                                else {
                                    $price = round(floatval($price) * (1 - ($discount / 100)),2);
				}
			}

			// go into admin mode
			Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			$prod = Mage::getModel('catalog/product');
			$productId = $prod->getIdBySku($sku);

                        // for 1.1.x we do not support discounts coming from MOM that have Ctype's or other exclusions (i.e. date ranges, catalog codes)
			// We need to make sure that we don't add them if we have any on the price line coming in that have these modifiers.
			// for 2.0, this is not going to be the case.
			$has_price_modifier = false;
			
			if ($ctype != '' 		||
				$ctype2 != ''		||
				$ctype3 != ''		||
				$coupon != ''		||
				$catalog != ''		||
				$custnum != '')
			{
				$has_price_modifier = true;
			}

			if ($has_price_modifier == true) {
				writeElement("Success", 1);
				writeElement("Message", $sku . " Price ignored, includes price modifier.");
				//Ctype: '" . $ctype . "', ctype2: '" . $ctype2 . "', ctype3: '" . $ctype3 . "', coupon: '" . $coupon . "', catalog: '" . $catalog . "', custnum: '" . $custnum . "'
				writeElement("ResultID", $productId);
				writeElement("ErrorData", "");
				writeCloseTag("SyncPricesResponse");
				return;
			}
                        $price_message = "";
			if (intval($productId) > 0)
			{
                            // Handle base price (Qty is zero)
                            // let's update the base item price first.
                            // Changed to '0' to mean retail price 
                            if ($qty == '0' && !$has_price_modifier)
                            {
                                // 1-3 Handle Normal Retail Selling Price (syncPrices)
                                $price_message = "Retail price";
                                    try
                                    {
                                            //if( intval($productId) > 0 )
                                            //{
                                                    $prod_api =  new Mage_Catalog_Model_Product_Api();
                                                    // this looks like its going to cause a world of hurt,
                                                    // do you need to LOAD the product, then change the price, then save ?
                                                    $product['price'] = $price;
                                                    $prod_api->update($productId, $product, Mage_Core_Model_App::ADMIN_STORE_ID);
                                            //}
                                            //else
                                            //{
                                            //        writeElement("Success", 0);
                                            //        writeElement("Message", "Error no product id found for '" . $sku . "'");
                                            //        writeElement("ResultID", 0);
                                            //        writeElement("ErrorData", "update failed.");
                                            //        writeCloseTag("SyncPricesResponse");
                                            //        return;

                                            //}
                                    } catch (Exception $e) {
                                            writeElement("Success", 0);
                                            writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $sku . "'");
                                            writeElement("ResultID", 0);
                                            writeElement("ErrorData", $e->GetMessage());
                                            writeCloseTag("SyncPricesResponse");
                                            return;
                                    }
                                    
                                    writeElement("Success", 1);
                                    if ($marked_for_deletion) {
                                        writeElement("Message", "[" . $sku . "] " . $price_message . " deleted.");
                                    }
                                    else {
                                        writeElement("Message", "[" . $sku . "] " . $price_message . " updated.");
                                    }        				    
				    writeElement("ResultID", $productId);
				    writeElement("ErrorData", "");
                                    writeCloseTag("SyncPricesResponse");
                                    return;                                    
                            }                            
                                                            
                            // for a quantity of 1, make sure there's no modifiers
                            // then set the price ONLY if the price is less than the sale price on this row.
                            if ($qty == '1' && !$has_price_modifier && $this->enable_special_price == true)
                            {                                
                                // 2-3 Handle special pricing (syncPrices)                             
                                    try
                                    {                                        
                                            //if( intval($productId) > 0 )
                                            //{
                                                    $prod_api =  new Mage_Catalog_Model_Product_Api();
                                                    // this looks like its going to cause a world of hurt,
                                                    // do you need to LOAD the product, then change the price, then save ?
                                                    //$product = $prod_api->info($productId);

                                                    $base_product_price = '';
                                                    $msrp_price = '';
                                                    $special_price = '';

                                                    $todays_date = date("Y-m-d");
                                                    $today = strtotime($todays_date);

                                                    $dtEnd = null;
                                                    $dtStart = null;
       
                                                    $bValidDateRange = true;

                                                    // let's make sure the date range's are ok.
                                                    if (strtotime($from_date) < $today && $from_date != "")
                                                    {
                                                            $dtStart = new DateTime($from_date);
                                                            $bValidDateRange = false;
                                                    }
                                                    if (strtotime($to_date) < $today && $to_date != "")
                                                    {
                                                            $dtEnd = new DateTime($to_date);
                                                            $bValidDateRange = false;
                                                    }
                                                    if (is_object ($dtEnd) || is_object ($dtSart))
                                                    {
                                                            if ($bValidDateRange == false)
                                                            {
                                                                    // ok, dange range is older.
                                                                    if (floatval($price1) > 0 && floatval($price1) > $price)
                                                                    {
                                                                            // set the price to the 'normal retail selling price' in MOM.
                                                                            $product['price'] = $price1;
                                                                            $price_message .= "Date range expired, Base price";
                                                                            // 2013dec17 PJQ - add 4th parameter identifierType to call
                                                                            $prod_api->update($productId, $product, intval($store_id), "id");									
                                                                    } 
                                                                    // do nothing else.
                                                                    writeElement("Success", 1);
                                                                    writeElement("Message", "[" . $sku . "] " . $price_message . " updated.");
                                                                    writeElement("ResultID", $productId);
                                                                    writeElement("ErrorData", "");
                                                                    writeCloseTag("SyncPricesResponse");
                                                                    return;

                                                            }
                                                    }
                                                    
                                                    //if ($this->enable_special_price == true)
                                                    //{                                                                                                            
                                                            /*                          
                                                            writeElement("Success", 0);
                                                            //writeElement("Message", $from_date . "--" . $to_date);
                                                            writeElement("Message", $price1);
                                                            writeElement("ResultID", 0);
                                                            writeElement("ErrorData", "From Date -- To Date");
                                                            writeCloseTag("SyncPricesResponse");
                                                            return;
                                                            */                         
                                                            //if (floatval ($price) < floatval ($price1) && floatval($price) > 0 )
                                                            // *@AA:01/15/16 12:47:08 PM:BSXL-356
                                                            // remove stipulation that special price must be less than retail price
                                                            if (floatval($price) > 0 && !$marked_for_deletion) 
                                                            {
                                                    
                                                                    $special_price = $price;
                                                                    $product['special_price'] = $price;
                                                                    if ($from_date != '')
                                                                    {
                                                                            $product['special_from_date'] = $from_date;
                                                                    } else {
                                                                            $product['special_from_date'] = '';
                                                                    }
                                                                    if ($to_date != '')
                                                                    {
                                                                            $product['special_to_date'] = $to_date;
                                                                    } else {
                                                                            $product['special_to_date'] = '';
                                                                    }	

                                                                    if ($price_message != '')
                                                                    {
                                                                            $price_message = $price_message . " and Special price";
                                                                    } else {
                                                                            $price_message .= "Special price";
                                                                    }
                                                            } else {
                                                                    // clear the special price.
                                                                    $product['special_from_date'] = '';
                                                                    $product['special_to_date'] = '';
                                                                    $product['special_price'] = '';
                                                                    $price_message = "Special price";
                                                            }
                                                    //}

                                                    // 2013dec17 PJQ - add 4th parameter identifierType to call
                                                    $prod_api->update($productId, $product, intval($store_id), "id");
                                            //}
                                            //else
                                            //{
                                            //        writeElement("Success", 0);
                                            //        writeElement("Message", "Error no product id found for '" . $sku . "'");
                                            //        writeElement("ResultID", 0);
                                            //        writeElement("ErrorData", "update failed.");
                                            //        writeCloseTag("SyncPricesResponse");
                                            //        return;
                                            //}
                                    } catch (Exception $e) {
                                            writeElement("Success", 0);
                                            writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $sku . "'");
                                            writeElement("ResultID", 0);
                                            writeElement("ErrorData", $e->GetMessage());
                                            writeCloseTag("SyncPricesResponse");
                                            return;
                                    }
                                    writeElement("Success", 1);
                                    if ($marked_for_deletion) {
                                        writeElement("Message", "[" . $sku . "] " . $price_message . " deleted.");
                                    }
                                    else {
                                        writeElement("Message", "[" . $sku . "] " . $price_message . " updated.");
                                    }
                                    writeElement("ResultID", $productId);
                                    writeElement("ErrorData", "");
                                    writeCloseTag("SyncPricesResponse");
                                    return;
                            }                            
                            //else                            
                            //{
                                
                             if (floatval($qty) > 1 )
                             {
                                // 3-3 Handle Tier Pricing (syncPrices)    
                                $price_message = "Tier price";
				$mcmpata = new Mage_Catalog_Model_Product_Attribute_Tierprice_Api();
				//$tierprices = $mcmpata->info($productId);

				$tierprices2 = $mcmpata->info($productId);
				$tierprices = array();

				foreach ($tierprices2 as $key=>$tierprice)
				{
					if ($tierprice['qty'] != '0' && $tierprice['qty'] != '1'  )
					{
                                                //if ($price <> $price1)
                                                //{
                                                    $tierprices[$key] = $tierprices2[$key];
                                                //}
                                        }
				}
				$bFoundTierPrice = false;				

				foreach ($tierprices as $key=>$tierprice)
				{
					if ($tierprice['qty'] == $qty)
					{
                                            $bFoundTierPrice = true;
                                            // Since tier price matches retail price,
                                            // Don't update, but delete tier                                          
                                            //if ($tierprices[$key]['price'] == $price1)
                                            // *@AA:01/15/16 12:47:08 PM:BSXL-236
                                            if ($marked_for_deletion) {
                                                unset($tierprices[$key]);
                                            }
                                            else {
                                                $tierprices[$key]['price'] = $price ;            						
                                            }
					}

				}
				// if we didn't find it and its not qty of 1, then add it
				if (!$bFoundTierPrice && floatval($qty) > 1 && !$marked_for_deletion)
				{
					$tierprices[] = array('website'           => 'all',
							      'customer_group_id' => 'all',
							      'qty'               => $qty,
							      'price'             => $price);
				}

				try
				{
					$mcmpata->update($productId, $tierprices);
					writeElement("Success", 1);
                                        if ($bFoundTierPrice && $marked_for_deletion) {
                                            writeElement("Message", "[" . $sku . "] " . $price_message . " deleted.");
                                        }
                                        else {
                                            writeElement("Message", "[" . $sku . "] " . $price_message . " updated.");
                                        }
					
					writeElement("ResultID", $productId);
					writeElement("ErrorData", "");

				} catch(Exception $e) {
					writeElement("Success", 0);
					writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $sku . "'");
					writeElement("ResultID", 0);
					writeElement("ErrorData", $e->GetMessage());
				}                            
                             }
                             //}
			} else {
				writeElement("Success", 1);
				writeElement("Message", "[" . $sku . "] " . "does not exist for price line. Sync will continue.");
				writeElement("ResultID", 0);
				writeElement("ErrorData", "Product does not exist.");
			}

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $sku . "'");			
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}
		writeCloseTag("SyncPricesResponse");
	}
	/**
	 * Action_GetCount function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetCount()
	{	  
	    $start = 0;

	    if (isset($_REQUEST['start']))
	    {
		$start = $_REQUEST['start'];
	    }

	    // only get orders through 2 seconds ago
	    $end = date("Y-m-d H:i:s", time() - 2);

	    // Convert to local SQL time
	    $start = toSqlDate($start);

	    // Write the params for easier diagnostics
	    writeStartTag("Parameters");
	    writeElement("Start", $start);
	    writeCloseTag("Parameters");

	    $orders = Mage::getModel('sales/order')->getCollection();
	    $orders->addAttributeToSelect("updated_at")->getSelect()->where("(updated_at > '$start' AND updated_at <= '$end')");
	    $count = $orders->count();

	    writeElement("OrderCount", $count);
	}

	/**
	 * Action_GetOrderPaymentData function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetOrderPaymentData()
	{

		if ($increment_id == "") {
			$increment_id = $this->ActionValues['increment_id'];
		}

		$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);

		if (!is_object ($order))
		{
			writeElement("Success", 0);
			writeElement("Message", "Error on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", "No order id found");
			return;
		}
		$payment = $order->getPayment();
		$cc_owner = $order->getCustomerFirstname() . " " . $order->getCustomerLastname();
		if (!is_object ($payment))
		{
			writeElement("Success", 0);
			writeElement("Message", "Error on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", "No payment object found");
			return;
		}
		$cc_num = $payment->getCcLast4();
		$cc_cvv = "";
		$cc_month = $payment->getCcExpMonth();
		$cc_year = substr($payment->getCcExpYear(), 2);
		$cc_expires = sprintf('%02u%s', $cc_month, $cc_year);

		writeStartTag("Payment");
		$method = Mage::helper('payment')->getMethodInstance($payment->getMethod())->getTitle();

		$data = $payment->getData();
		$auth_time=date ("Y-m-d", strtotime($order->getCreatedAt()));
		// gjm: some confusion about this format.
		//$auth_time=date ("m/d/Y", strtotime($order->getCreatedAt()));
		$auth_amount='';

		if ($data['method'] == "ccsave" || $data['method'] == "authorizenet" || $data['method'] == "linkpoint" || $data['method'] == "sagepaydirectpro")
		{
			$cc_num = $payment->decrypt ($payment->getCcNumberEnc());
			// try to get the cvv
			//$sql = "SELECT cc_cid_enc FROM sales_flat_quote_payment WHERE quote_id =" . $order["quote_id"];
			//$cc_cid_enc = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($sql);
			//$cc_cvv = $payment->decrypt ($cc_cid_enc);
                        // 2016-07-12 - CcCid is not encrypted, no need to decrypt
			//$cc_cvv = $payment->decrypt ($payment->getCcCid());
                        $cc_cvv = $payment->getCcCid();
			$cc_last4 = $payment->getCcLast4();
			$cc_trans_id = trim ($data['cc_trans_id']);
			$cc_approval = trim($data['cc_approval']);
			$cc_avs_status = trim($data['cc_avs_status']);
			$auth_amount = trim($data['amount_authorized']);

			if ($cc_trans_id == '' && $data['method'] != 'ccsave')
			{
				$sql = "SELECT txn_id FROM " . Mage::getSingleton('core/resource')->getTableName('sales_payment_transaction') . " WHERE order_id =" . $order["entity_id"];
				$cc_trans_id = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($sql);
			}

			if ($data['method'] == "ccsave" || $data['method'] == "authorizenet" || $data['method'] == "linkpoint" || $data['method'] == "sagepaydirectpro")
			{
				$cc_num = $payment->decrypt ($payment->getCcNumberEnc());
				// try to get the cvv
				//$sql = "SELECT cc_cid_enc FROM sales_flat_quote_payment WHERE quote_id =" . $order["quote_id"];
				//$cc_cid_enc = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($sql);
				//$cc_cvv = $payment->decrypt ($cc_cid_enc);
                                // 2016-07-12 - CcCid is not encrypted, no need to decrypt
				//$cc_cvv = $payment->decrypt ($payment->getCcCid());
                                $cc_cvv = $payment->getCcCid();
				$cc_last4 = $payment->getCcLast4();
				//$cc_trans_id = trim ($data['cc_trans_id']);
				$cc_approval = trim($data['cc_approval']);
				$cc_avs_status = trim($data['cc_avs_status']);
				$cc_type = trim($data['cc_type']);

				// we're going to get the addtional_information based on the Authorize.net gateway.
				// you need the ChannelBrain_BizSyncXL module installed WITH the authorize.net Mage_Paygate_Model_Authorizenet
				// overridden _registerCard function enabled.
				if ($data['method'] == 'authorizenet')
				{
					/*
					    [id] => ab17933fd495f52a7cab270fcfa4d407
					    [requested_amount] =>
					    [balance_on_card] =>
					    [last_trans_id] => 4274985614
					    [processed_amount] => 1.00
					    [cc_type] => VI
					    [cc_owner] =>
					    [cc_last4] => 5618
					    [cc_exp_month] => 10
					    [cc_exp_year] => 2013
					    [cc_ss_issue] =>
					    [cc_ss_start_month] =>
					    [cc_ss_start_year] =>
					    [cc_avs_status] => N
					    [approval_code] => 06089G
					    [cc_cid] => 973
					    [cc_number] => 411111111111111
					*/
					/* No information coming back from the main object. Try getting it from the additional information */
					if (trim($cc_trans_id) == "" || trim($cc_year) == "")
					{
						$addtional_information = array();
						foreach ($data['additional_information']['authorize_cards'] as $subarray)
						{
							if (is_array($subarray))
							{
								$additional_information = $subarray;
								break;
							}
						}
						if (trim($cc_approval) == '')
						{
							$cc_approval = $additional_information['approval_code'];
						}
						if (trim($cc_trans_id) == '')
						{
							$cc_trans_id = $additional_information['last_trans_id'];
						}
						if (trim($cc_avs_status) == '')
						{
							$cc_avs_status = $additional_information['cc_avs_status'];
						}
						//$cc_trans_id = $additional_information['last_trans_id'];
						if (trim($cc_last4) == '')
						{
							$cc_last4 = $additional_information['cc_last4'];
						}
						if (trim($cc_cvv) == '')
						{
							$cc_cvv = $additional_information['cc_cid'];
						}
						if (trim($cc_num) == '' && $additional_information['cc_number'] != '')
						{
							$cc_num = $additional_information['cc_number'];
						}

						// BSXL-140 2014-12-03 PJQ - don't overwrite variable if we already have it
						if (trim($cc_type) == '')
						{
							$cc_type = $additional_information['cc_type'];
						}
						if (trim($auth_amount) == '')
						{
							$auth_amount = $additional_information['processed_amount'];
						}

						// BSXL-140 2014-12-03 PJQ - don't overwrite variable if we already have it
						if (trim($cc_year) == '')
						{
							$cc_year = $additional_information['cc_exp_year'];
							$cc_month = $additional_information['cc_exp_month'];

							$cc_year  = substr($cc_year, 2);
							$cc_expires = sprintf('%02u%s', $cc_month, $cc_year);
						}
					}


				}

				// we're going to get the addtional_information based on the Sage Pay gateway.
				if ($data['method'] == "sagepaydirectpro")
				{
					$vps_tx_id = $order->getSagepayInfo()->getVpsTxId();
					writeElement ("VpsTxId", $vps_tx_id );
					$vendor_tx_code = $order->getSagepayInfo()->getVendorTxCode();
					writeElement ("VendorTxCode", $vendor_tx_code );
					$security_key = $order->getSagepayInfo()->getSecurityKey();
					writeElement ("SecurityKey", $security_key );
					$tx_auth_no = $order->getSagepayInfo()->getTxAuthNo();
					writeElement ("TxAuthNo", $tx_auth_no );
					$trn_currency = $order->getSagepayInfo()->getTrnCurrency();
					writeElement ("Currency", $trn_currency );

					// not exactly where I wanted to get auth amount from, but its one of the only places with a value
					$auth_amount = $data["base_amount_ordered"];

				}

				if ($data['method'] == "linkpoint")
				{
					$auth_time = date ("y/m/d h:i:s", strtotime("now"));
				}

				writeElement("Method", $payment->getMethod());

				writeStartTag("CreditCard");
				writeElement ("TransactionId", $cc_trans_id);
				writeElement ("ApprovalCode", $cc_approval);
				writeElement ("AVSCode", $cc_avs_status);
				writeElement("Type", $cc_type);
				writeElement("Owner", $cc_owner);
				writeElement("Number", $cc_num);
				writeElement("CVV", $cc_cvv);
				writeElement("Expires", $cc_expires);
				writeElement("Last4", $cc_last4);
				writeElement("AuthAmount", $auth_amount);
				writeElement("AuthTime", $auth_time);
				writeCloseTag("CreditCard");
			}

		}
		if ( $this->ExtendedGiftMessage == true )
		{
			writeStartTag("ExtendedGiftMessage");
			ParseGiftMessage( $order );
			writeCloseTag("ExtendedGiftMessage");
		}
		writeCloseTag("Payment");
		writeStartTag("ServiceMessage");

		writeElement("Success", 1);
		writeElement("Message", "Payment data valid.");
		writeElement("ResultID", 1);
		writeElement("ErrorData", "");

		writeCloseTag("ServiceMessage");
	}

	/**
	 * Action_GetOrders function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetOrders()
	{
	    $start = 0;
	    $maxcount = 50;

	    if (isset($this->ActionValues['start']))
	    {
		$start = $this->ActionValues['start'];
	    }

	    if (isset($this->ActionValues['maxcount']))
	    {
		$maxcount = $this->ActionValues['maxcount'];
	    }

	    // Only get orders through 2 seconds ago.
	    $end = date("Y-m-d H:i:s", time() - 2);

	    // Convert to local SQL time
	    $start = toSqlDate($start);

	    // Write the params for easier diagnostics
	    writeStartTag("Parameters");
	    writeElement("Start", $start);
	    writeElement("End", $end);
	    writeElement("MaxCount", $maxcount);
	    writeCloseTag("Parameters");				    

	    $orders = Mage::getModel('sales/order')->getCollection();
	    $orders->addAttributeToSelect("*")
		->getSelect()
		->where("(status = 'Pending')")
		->order('updated_at', 'asc');
            $orders->setCurPage(1)
                ->setPageSize($maxcount)
                ->loadData();
	    //		->where("(updated_at > '$start' AND updated_at <= '$end')")
	    writeElement("Total", $orders->count());

	    writeStartTag("Orders");

	    $lastModified = null;
	    $processedIds = "";

	    foreach ($orders as $order)
	    {
		// keep track of the ids we've downloaded
		$lastModified = $order->getUpdatedAt();

		if ($processedIds != "")
		{
		    $processedIds .= ", ";
		}
		$processedIds .= $order->getEntityId();

		WriteOrder($order);
	    }

	    // if we processed some orders we may have to get some more
	    if ($processedIds != "")
	    {
		$orders = Mage::getModel('sales/order')->getCollection();
		$orders->addAttributeToSelect("*")->getSelect()->where("updated_at = '$lastModified' AND entity_id not in ($processedIds) ");

		foreach ($orders as $order)
		{
		    WriteOrder($order);
		}
	    }

	    writeCloseTag("Orders");
	}

	/**
	 * Action_GetConfiguration function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetConfiguration()
	{
		$resource = Mage::getSingleton('core/resource');
		$read= $resource->getConnection('core_read');

		$omxTable = $resource->getTableName('omx');

		$select = $read->select()
		   ->from($omxTable)
		   ->where('status',1)
		   ->order('created_time DESC');

		$row = $read->fetchRow($select);
		foreach ($row as $key=>$value)
		{
			writeElement($key, $value);
		}

	}

	/**
	 * Action_TestConnection function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_TestConnection()
	{
	    writeStartTag("TestConnection");
		if ($this->checkAdminLogin())
		{
			writeStartTag("StatusCode");
			writeElement("Code", 0);
			writeElement("Name", "Login succeeded.");
			writeCloseTag("StatusCode");
		} else {
			writeStartTag("StatusCode");
			writeElement("Code", 1);
			writeElement("Name", "Login failed.");
			writeCloseTag("StatusCode");
		}
	    writeCloseTag("TestConnection");
	}

	/**
	 * Action_GetStatusCodes function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetStatusCodes()
	{
	    writeStartTag("Configuration");

	    $statuses_node = Mage::getConfig()->getNode('global/sales/order/statuses');

	    foreach ($statuses_node->children() as $status)
	    {
		writeStartTag("StatusCode");
		writeElement("Code", $status->getName());
		writeElement("Name", $status->label);
		writeCloseTag("StatusCode");
	    }
	    writeCloseTag("Configuration");
	}
	/**
	 * Action_GetShippingMethods function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetShippingMethods()
	{
		$shipping = new Mage_Shipping_Model_Config();
		writeStartTag("ShippingMethods");
		foreach ($shipping->getActiveCarriers() as $name=>$carrier)
		{
			$allowed_methods = $carrier->getAllowedMethods();
			if( count($allowed_methods) == 1 )
			{
				if( $carrier['title'] != "")
				{
					writeStartTag("Method");
					writeElement("Name", $carrier['title'] . " - " . $carrier['name']);
					writeElement ("Carrier", $name);
					writeCloseTag("Method");
				}
				else
				{
					writeStartTag("Method");
					writeElement ("Carrier", $name);
					writeElement("Name", $name . "_" . $name);
					writeCloseTag("Method");
				}
			}
			else
			{
				foreach ($allowed_methods as $method=>$value)
				{

					if (trim($value) != "") 
					{
						writeStartTag("Method");
						writeElement ("Carrier", $name);						
						writeElement ("Name", $name . "_" . $method);
						writeCloseTag("Method");
					}
					else
					{
						// 2011 June 15 - UPS XML labels are not showing up, so ask the UPS class what they are.
						// should we verify that carrier is type Mage_Usa_Model_Shipping_Carrier_Ups ?
						$arr = $carrier->getCode('originShipment');
						$val = $arr['United States Domestic Shipments'][$method];
						writeStartTag("Method");
						//writeElement ("Name", $val);
						writeElement("Name", $name . "_" . $method );
						writeElement ("Carrier", $name);
						writeCloseTag("Method");

					}
				}
			}		
		}
		writeCloseTag("ShippingMethods");
	}

	/**
	 * Action_GetPaymentMethods function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetPaymentMethods()
	{
		$payment = new Mage_Payment_Helper_Data();

		writeStartTag("PaymentMethods");

		foreach ($payment->getStoreMethods() as $method=>$value)
		{
		     if (is_object($value)) {
			writeStartTag("Method");
			writeElement ("Name", $value->getTitle());
			writeElement ("Code", $value->getCode());
			writeCloseTag("Method");
			}
		}
		writeCloseTag("PaymentMethods");
	}
        
        
        /**
	 * Action_GetInfo XML function.
	 * 
	 * @access public
	 * @return void
	 */
        /* *@AA:10/14/16 11:27:10 AM:BSXL-456 */
	public function Action_GetInfo()
	{            
            ?>
            <MagentoModule>
                <ReleaseDate><?php echo $this->moduleDate ?></ReleaseDate>
                <Version>
                    <BizSyncVer><?php echo $this->moduleVersion ?></BizSyncVer>
                    <MagentoVer><?php echo Mage::getVersion(); ?></MagentoVer>
                    <PHPVer><?php echo phpversion(); ?></PHPVer>
                    <ZendVer><?php echo @Zend_Version::getLatest(); ?></ZendVer>  
                </Version>
            </MagentoModule>
            </BizSync>
            <?php		
		die();
                /** when die is issued output is returned and </BizSync> node is not added **/
	}	
        
        /**
	 * Action_GetInfo function.
	 * 
	 * @access public
	 * @return void
	 */
        /* *@AA:10/14/16 11:27:10 AM:BSXL-456 */
	function Action_GetInfoHTML()
	{            
		//global $moduleVersion;
		//global $moduleDate;
?>
		<html>
		<head>
			<title>ChannelBrain BizSyncXL&trade; | Magento Module</title>
			<style type="text/css">
				.datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Geneva, Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #006699; }.datagrid table td, .datagrid table th { padding: 3px 10px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#006699', endColorstr='#00557F');background-color:#006699; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #00557F; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }.datagrid table tbody .alt td { background: #E1EEf4; color: #00557F; }.datagrid table tbody td:first-child { border: none; }
			</style>
		</head>
		<body>
		<div id="wrap" class="group">
			<div id="title" class="full prod group">
				<h1>ChannelBrain BizSyncXL<span class="tm">&trade;</span></h1>
				<h2>Magento Module</h2>
			</div>
			<hr/>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th>
									Component
								</th>
								<th>
									Version / Information
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									BizSyncXL Version
								</td>
								<td>
									<?php echo $this->moduleVersion . " - " . $this->moduleDate; ?>
								</td>
							</tr>
							<tr>
								<td>
									Magento Version
								</td>
								<td>
									<?php echo Mage::getVersion(); ?>
								</td>
							</tr>
							<tr>
								<td>
									PHP Version
								</td>
								<td>
									<?php echo phpversion(); ?>
								</td>
							</tr>

							<tr>
								<td>
									Zend Version
								</td>
								<td>
									<?php echo @Zend_Version::getLatest(); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</body>
		</html>
<?php		
		die();
	}

	/**
	 * Action_GetCreditCartTypes function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_GetCreditCardTypes()
	{
		$cards = new Mage_Payment_Model_Config();

		writeStartTag("CreditCardTypes");
		foreach ($cards->getCcTypes() as $key=>$value)
		{
		     if (trim($value) != "") {
			writeStartTag("Method");
			writeElement ("Name", $key);
			writeCloseTag("Method");
			}
		}
		writeCloseTag("CreditCardTypes");
	}

	/**
	 * Action_UpdateOrder function.
	 * 
	 * Update the status of an order
	 * command: hold,complete,cancel
	 * orderid: order number (alt_order)
	 * tracking: the tracking number of the order
	 *
	 * @access public
	 * @return void
	 */
	public function Action_UpdateOrder()
	{
	    // gather paramtetes
	    if ((!isset($_REQUEST['order']) && !isset($_REQUEST['orderid'])) || 
	    	!isset($_REQUEST['command']) || !isset($_REQUEST['comments']))
	    {
		RestResultError(40, "Not all parameters supplied.");
		return;
	    }

	    if (isset($_REQUEST['order']))
	    {
	    	$orderNumber = (int) $_REQUEST['order'];
	    	$order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
	    }
	    else
	    {
	    	// newer version of BizSync, pull the entity id
		$orderID = (int)$_REQUEST['orderid'];
		$order = Mage::getModel('sales/order')->load($orderID);
	    }

	    $command = (string) $_REQUEST['command'];
	    $comments = $_REQUEST['comments'];
	    $tracking = $_REQUEST['tracking'];
	    $carrierData = $_REQUEST['carrier'];

	    ExecuteOrderCommand($order, $command, $comments, $carrierData, $tracking);
	}
	/**
	 * Action_DeleteProduct function.
	 * 
	 * Delete's a product and its associated products (if it's a configurable product).
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_DeleteProduct()
	{
		writeStartTag("DeleteProduct");
		try
		{
			$sku = GetSKU();
			$store_id = $this->ActionValues['store_id'];

			// go into admin mode
			Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			$product = Mage::getModel('catalog/product');
			$productId = $product->getIdBySku($sku);
			$product->load ($productId);
			$productIds = array ($productId);
			if (intval($productId) > 0 && is_object($product))
			{
				$configurable = false;
				if ($product->getTypeId() == "configurable")
				{
					$associatedProducts = Mage::getSingleton('catalog/product_type')->factory($product)->getUsedProducts($product); 

					foreach($associatedProducts as $associatedProduct) 
					{ 
						$productIds[] = $associatedProduct->getId();
					} 
				}
				$prod_api =  new Mage_Catalog_Model_Product_Api();

				foreach ($productIds as $prodId)
				{
					if ($prod_api->delete($prodId))
					{
						writeElement("Success", 1);
						writeElement("Message", $sku . " deleted.");
						writeElement("ResultID", $prodId);
						writeElement("ErrorData", "");
					} else {
						writeElement("Success", 0);
						writeElement("Message", $sku . " could not be deleted.");
						writeElement("ResultID", 0);
						writeElement("ErrorData", "");
					}
				}


			} else {
				writeElement("Success", 0);
				writeElement("Message", $sku . " not found or invalid sku.");
				writeElement("ResultID", 0);
				writeElement("ErrorData", "");
			} 

		} catch (Exception $e) {
			writeElement("Success", 0);
			writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
			writeElement("ResultID", 0);
			writeElement("ErrorData", $e->GetMessage());
		}

		writeCloseTag("DeleteProduct");
	}

	/**
	 * Action_RemoveCreditCard function.
	 *
	 * Delete's a product and its associated products (if it's a configurable product).
	 *
	 * @access public
	 * @return void
	 */
	function Action_RemoveCreditCard()
	{
		writeStartTag("RemoveCreditCard");

		$increment_id = $this->ActionValues['increment_id'];

		if ($increment_id == "")
		{
			$increment_id = $_REQUEST['increment_id'];
		}

		if ($increment_id != "")
		{
			$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			$payment = $order->getPayment();
			try
			{
				if (!is_object ($order) || !is_object($payment))
				{
					writeElement("Success", 0);
					writeElement("Message", "Error on line " . __LINE__ . " in " . __FUNCTION__);
					writeElement("ResultID", 0);
					writeElement("ErrorData", "No order id found");
					return;
				}
				try {
					$tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_payment');
					if ($tableName != '')
					{
						$sql = "SELECT `cc_number_enc` FROM `" . $tableName . "` WHERE entity_id = " . $order['entity_id'];
						$cc_number_enc = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($sql);
						// BSXL-142 2014-10-08 PJQ
						$sql = "UPDATE `" . $tableName . "` SET  `cc_number_enc` =  '', `additional_information` = '' WHERE  `entity_id`=  '" . $order['entity_id'] . "' LIMIT 1";
						$write = Mage::getSingleton('core/resource')->getConnection('core_write');
						$write->query( $sql );
						$sql = "SELECT `cc_number_enc` FROM `" . $tableName . "` WHERE entity_id = " . $order['entity_id'];
						$cc_number_enc_after = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($sql);
						if ($cc_number_enc != "" && $cc_number_enc_after == '')
						{
							writeElement("Success", 1);
							writeElement("Message", $increment_id . " card was removed.");
							writeElement("ResultID", $increment_id);
							writeElement("ErrorData", "");
						} else {
							writeElement("Success", 0);
							writeElement("Message", $increment_id . " had no card data.");
							writeElement("ResultID", $increment_id);
							writeElement("ErrorData", "");
						}
					} else {
						writeElement("Success", 0);
						writeElement("Message", "No table name for `sales_flat_order_payment` found.");
						writeElement("ResultID", $increment_id);
						writeElement("ErrorData", "");
						return;
					}

				} catch (Exception $e) {
					writeElement("Success", 0);
					writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__ . " with error '" . $e->GetMessage() . "' for '" . $increment_id . "'");
					writeElement("ResultID", 0);
					writeElement("ErrorData", $e->GetMessage());
				}
			} catch (Exception $e) {
				writeElement("Success", 0);
				writeElement("Message", "Exception caught on line " . __LINE__ . " in " . __FUNCTION__);
				writeElement("ResultID", 0);
				writeElement("ErrorData", $e->GetMessage());
			}
		} else {
			writeElement("Success", 0);
			writeElement("Message", "No increment id was passed.");
			writeElement("ResultID", 0);
			writeElement("ErrorData", "");
		}
		writeCloseTag("RemoveCreditCard");
	}

	/**
	 * Action_SyncImages function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_SyncImage()
	{
                
		writeStartTag("SyncImageResponse");

		$stocknumber = $this->ActionValues['sku'];
		$image_data = $this->ActionValues['image_data'];
		$image_name = $this->ActionValues['image_name'];
		$image_type = $this->ActionValues['image_type'];

		// go into admin mode
		//Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		// gjm: 01/16/2013 - Mode wasn't working!?
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($stocknumber);

		// make sure prod is initialized
		if( !is_numeric ($productId) )
		{
			// failed.  return with error.
			writeElement("Success", 0);
			writeElement("Message", "catalog/product failed to load.");
			writeElement("ResultID", 0);
			writeElement("ErrorData", "");
			writeCloseTag("SyncImageResponse");
			// don't continue.
			die();
		}

		if( is_numeric( $productId ) )
		{
			$prod->load($productId);
// BSXL-73 2014-06-16 PJQ - we don't have a value in $prod_type, so lets just leave it alone
//			$prod->setTypeId($prod_type);

			// always's use the magento media/import folder.
			$import_path = "media/import";

			if( strtolower($image_type) == "image" ||
				strtolower($image_type) == "thumbnail" ||
				strtolower($image_type) == "small_image")
			{
				$base_path = $_SERVER["DOCUMENT_ROOT"] . Mage::app()->getRequest()->getBasePath();
				//$fullImagePath = Mage::getBaseDir('media') . "/import/" . $image_name;
				// BSXL-73 2014-06-16 PJQ - if media/import is not available use media folder
				if (is_dir (Mage::getBaseDir('media') . "/import/"))
				{
					$fullImagePath = Mage::getBaseDir('media') . "/import/" . $image_name;
				} else {
					$fullImagePath = Mage::getBaseDir('media') . "/" . $image_name;
				}

				if ($image_data != "")
				{
					file_put_contents($fullImagePath, base64_decode (trim($image_data)));
					if(file_exists($fullImagePath) && is_file($fullImagePath))
					{

						$result = $this->AddProductImage( $productId, $fullImagePath, $image_type );
						if ($result != "")
						{
							writeElement("Success", 1);
							writeElement("Message", $result);
							writeElement("ResultID", $productId);
							writeElement("ErrorData", "");

						} else {
							writeElement("Success", 1);
							writeElement("Message", $image_name . " added.");
							writeElement("ResultID", $productId);
							writeElement("ErrorData", "");
						}

					} else {
						writeElement("Success", 0);
						writeElement("Message", $image_name . " failed." );
						writeElement("ResultID", $productId);
						writeElement("ErrorData", "");
					}
				} else {
					// failed.  
					writeElement("Success", 0);
					writeElement("Message", "Image data empty.");
					writeElement("ResultID", 0);
					writeElement("ErrorData", "");
				}
			} else {
				// failed.  
				writeElement("Success", 0);
				writeElement("Message", "Image type '" . $image_type . "' invalid.");
				writeElement("ResultID", 0);
				writeElement("ErrorData", "");
			}
		}
		writeCloseTag("SyncImageResponse");
	}
	/**
	 * Action_GetProductFields function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	public function Action_GetProductFields()
	{
		$shipping = new Mage_Shipping_Model_Config(); 
		writeStartTag("ProductFields");
		foreach ($shipping->getActiveCarriers() as $carrier)
		{
			foreach ($carrier->getAllowedMethods() as $method=>$value)
			{
				writeStartTag("Method");
				writeElement ("Name", $value);
				writeCloseTag("Method");
			}		
		}
		writeCloseTag("ProductFields");
	}

	/**
	 * SetProductTierPrice function.
	 *
	 * Set the Product "Special Price" in Magento
	 *
	 * @access public
	 * @param mixed $xml
	 * @param integer $productId
	 * @return void
	 */
	function SetProductTierPrice ($xml, $productId, $group_id = "all", $website_id = "all")
	{
		// add tier pricing
		if( intval($productId) > 0 && count($xml->WebStore->ItemData->Item->PriceData->Price) > 0 )
		{
			$mcmpata = new Mage_Catalog_Model_Product_Attribute_Tierprice_Api();
			$tierprices = array();
			$numPr = count($xml->WebStore->ItemData->Item->PriceData->Price);
			// loop through the tier pricing data and add the tier price.
			for($m=0; $m<$numPr; $m++)
			{
				$attr = $xml->WebStore->ItemData->Item->PriceData->Price[$m]->attributes();
				$qty = intval($attr["quantity"]);
				$bMultiplier = $attr["multiplier"];
				$price = floatval($xml->WebStore->ItemData->Item->PriceData->Price[$m]->Amount[0]);
				$SH = floatval($xml->WebStore->ItemData->Item->PriceData->Price[$m]->AdditionalSH[0]);

				$tierprices[] = array(
					'website'=>$website_id,
					'customer_group_id' => $group_id,
					'qty'               => $qty,
					'price'             => $price
				);
			}

			try
			{
				// 2014jan25 PJQ - add 3rd parameter idtype
				$mcmpata->update($productId, $tierprices, "id");
				return true;

			} catch(Exception $e) {
				return "Error: failed in " . __FUNCTION__ . " on line " . __LINE__ . " : " .  $e->GetMessage();
			}
		} else {
			return "Error: failed in " . __FUNCTION__ . " on line " . __LINE__ . ", productId or xml was invalid.";
		}

	}



	/**
	 * SetProductSpecialPrice function.
	 *
	 * Set the Product "Special Price" in Magento
	 *
	 * @access public
	 * @param mixed $xml
	 * @param mixed $product
	 * @return void
	 */
	private function SetProductSpecialPrice ($xml, $product, $SaleType = 1, $Price = "0.00", $SalePrice = "0.00", $SpecialPrice = "0.00", $SaleStartDate = "", $SaleEndDate = "")
	{
		Mage::log("[" . __FUNCTION__ . "] Begin");
		try
		{
			// special pricing.
			// add special pricing. 1.7 / 1.12 and above.
			// used for RMS only.
			if (is_object ($xml) && $xml != null)
			{
				$SaleType = intval($xml->WebStore->ItemData->Item->SaleType[0]);
				$SalePrice = trim($xml->WebStore->ItemData->Item->SalePrice[0]);
				$SaleStartDate = trim($xml->WebStore->ItemData->Item->SaleStartDate[0]);
				$SaleEndDate = trim($xml->WebStore->ItemData->Item->SaleEndDate[0]);
				$Price = trim($xml->WebStore->ItemData->Item->Price[0]);
				$SpecialPrice = trim($xml->WebStore->ItemData->Item->SpecialPrice[0]);
			}

			if (is_object($product))
			{
				Mage::log("SaleEndDate : " . $SaleEndDate);
				Mage::log("SaleStartDate : " . $SaleStartDate);
				Mage::log("SalePrice : " . $SalePrice);
				Mage::log("Price : " . $Price);
				Mage::log("SaleType : " . $SaleType);
				Mage::log("SpecialPrice : " . $SpecialPrice);

				// The item is onsale (i.e. RMS has a flag in the pricing tab.)
				// for MOM we set this to 1 as well in the product XML
				if ($SaleType == 1)
				{
					// go into admin mode
					//Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
					// gjm: 01/16/2013 - Mode wasn't working!?
					Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
					// let's reset everything.
					$product->setSpecialFromDate('');
					$product->setSpecialToDate('');
					$product->setSpecialPrice ('');

					$todays_date = date("Y-m-d");
					$today = strtotime($todays_date);

					if (strtotime($SaleStartDate) >= $today && $SaleStartDate != "")
					{
						$dtStart = new DateTime($SaleStartDate);
					}
					if (strtotime($SaleEndDate) >= $today && $SaleEndDate != "")
					{
						$dtEnd = new DateTime($SaleEndDate);
					}

					// set the start date
					if (is_object ($dtStart))
					{
						Mage::log("Start Date object valid, SaleStartDate is : " . $dtStart->format ("Y-m-d"));
						//$SaleStartDate =  $dtStart->format("Y-m-d");
						if (strtotime($SaleStartDate) >= $today)
						{
							// Sets the Start Date
							$product->setSpecialFromDate($SaleStartDate);
							$product->setSpecialFromDateIsFormated(true);
						} else {
							// let's clear the data.
							Mage::log("Clearing start date, no start date.");
							$product->setSpecialFromDate('');
						}
					} else {
						// let's clear the data.
						Mage::log("Clearing start date, start date range object not valid.");
						$product->setSpecialFromDate('');
					}

					// set the start date
					if (is_object ($dtEnd))
					{
						Mage::log("End Date object is valid, SaleEndDate is : " . $dtEnd->format ("Y-m-d"));
						//$SaleEndDate =  $dtEnd->format("Y-m-d");
						if (strtotime($SaleEndDate) >= $today)
						{

							// Sets the Start Date
							$product->setSpecialToDate($SaleEndDate);
							$product->setSpecialToDateIsFormated(true);
						} else {
							// let's clear the data.
							Mage::log("Clearing end date, no end date.");
							$product->setSpecialToDate('');

						}
					} else {
						// let's clear the data.
						Mage::log("Clearing end date, end date range object not valid.");
						$product->setSpecialToDate('');
					}

					// if there's a sale price.
					if (intval($SalePrice) <= intval($Price) && intval($SalePrice) > 0)
					{
						Mage::log("Setting Special Price to : " . $SalePrice);
						$product->setSpecialPrice ($SalePrice);
					} else {
						// since we're a date range sale, we'll want to clear the dates if the price isn't valid.
						Mage::log("Clearing dates and special price.");
						$product->setSpecialFromDate('');
						$product->setSpecialToDate('');
						$product->setSpecialPrice ('');
					}

					// do we have a special price?
					// intval($SalePrice) <= 0 &&
					if (intval ($SpecialPrice) < intval($Price) && intval($SpecialPrice) > 0)
					{
						$product->setSpecialPrice ($SpecialPrice);
					} else {
						Mage::log("Clearing special price.");
						$product->setSpecialPrice ('');
					}
					$product->save();
				} else {
					$product->setSpecialFromDate('');
					$product->setSpecialToDate('');
					$product->setSpecialPrice ('');
				}// SaleType == 1

				if ($SaleType != 1)
				{
					// do we have a special price?
					if (intval ($SpecialPrice) < intval($Price) && intval($SalePrice) <= 0 && intval($SpecialPrice) > 0)
					{
						$product->setSpecialPrice ($SpecialPrice);
					} else {
						$product->setSpecialPrice ('');
					}

				}

			} // end prod.

		} catch (Exception $e) {
			Mage::log("Exception: `" . __FUNCTION__ . "` " . $e->GetMessage());
			return "Error: failed in " . __FUNCTION__ . " on line " . __LINE__ . " : " .  $e->GetMessage();
		}
		Mage::log("[" . __FUNCTION__ . "] End");
		return true;
	}


	/**
	 * checkAdminLogin function.
	 * 
	 * @access public
	 * @return void
	 */
	public function checkAdminLogin()
	{
            // There's no need to validate the admin anymore for a Magento extension. Bizsync.php needed to create a session on the server.           
            return true;
/*              
	    $loginOK = false;
	    $XLUsername = "";
	    $XLPassword = "";
	    // posted vs. request.
	    try {
		    if (isset($this->ActionValues['XLUsername']) && isset($this->ActionValues['XLPassword']))  
		    {
			$XLUsername = $this->ActionValues['XLUsername'];
			$XLPassword = $this->ActionValues['XLPassword'];

		    } 
                   if ($XLUsername != "" && $XLPassword != "")
			{

			$user = Mage::getSingleton('admin/session')->login($XLUsername, $XLPassword);
			if ($user && $user->getId())
			{
			    $loginOK = true;	    
			}
		    }
                    //$username = $this->ActionValues['Username'];
                    //$password = $this->ActionValues['Password'];
 		    //$adminUser = Mage::getModel('admin/user');
		    //if ($adminUser->authenticate($username, $password)) {
		    //	$loginOK = true;
		    //} else {
                    //    $loginOK = false;
                    //}
   
		    if (!$loginOK)
		    {
			RestResultError(50, "The username or password is incorrect.");
		    }
		} catch (Exception $e) {
			RestResultError(50, "function " . __FUNCTION__ . " threw the exception " . $e->GetMessage());
		}
		return $loginOK;
 */
	}

	/**
	 * syncNewProduct function.
	 * 
	 * @access private
	 * @param mixed $itemcode
	 * @param mixed $data
	 * @param mixed $default_attribute_set_id
	 * @return void
	 */
	private function syncNewProduct($itemcode, $data, $default_attribute_set_id)
	{
		global $magento;
		global $session;
		//$manage_inventory = "";
		//$is_in_stock = "";

		$prod=null;

		try
		{
			$xml = simplexml_load_string ($data);
			if (get_class($xml) != "SimpleXMLElement")
			{
				return "Error: (syncNewProduct) XML invalid and could not parse.";
			}

			$item_attributes = $xml->WebStore->ItemData->Item->attributes();
			$stocknumber = $item_attributes['itemCode'];

			if ($stocknumber == '')
			{
				return "SKU is empty.";
			}

			if ($stocknumber != $itemcode)
			{
				return "Item code '" . $itemcode ."' in XML did not match SKU that was passed.";
			}

			// 2014mar04 PJQ - set it to "2" for taxable and "0" for non taxable and then use it to set value of tax_class_id
			// BSXL-25
			$taxable = ($item_attributes['isTaxable']) == "1" ? 2 : 0;
			$is_active = ($item_attributes['active']) == "True" ? 1 : 2;

			$discontinued = (trim($xml->WebStore->ItemData->Item->Discontinued[0])) == "True" ? 1 : 0;

			$wgt = escapeInventoryFileData($xml->WebStore->ItemData->Item->Weight[0]);

			$short_description = "";
			$description = "";
			$product_name = "";

			$sync_product_longdescription = trim($this->ActionValues['sync_product_longdescription']);
			$sync_product_shortdescription = trim($this->ActionValues['sync_product_shortdescription']);
			$sync_product_name = trim($this->ActionValues['sync_product_name']);
			$manage_inventory = trim($this->ActionValues['manage_inventory']);

                        //$enable_product_status = trim($this->ActionValues['enable_product_status']);

			if ($manage_inventory == "")
			{
				$manage_inventory = "0"; // default to not setting anything.
			}

			// GJM 05-25-2015
			$is_in_stock = trim($this->ActionValues['is_in_stock']);

			if ($is_in_stock == "")
			{
				$is_in_stock = "0"; // default to not setting anything.
			}
                        
                        //*@AA:06/29/16 12:18:22 PM:BSXL-390
                        $upccode = escapeInventoryFileData($xml->WebStore->ItemData->Item->UPCCode[0]);
                        $isbnnumber = escapeInventoryFileData($xml->WebStore->ItemData->Item->ISBNNumber[0]);

                        $product_name = $this->setProductInfo($stocknumber, "ProductName", $xml);    
                        $short_description = $this->setProductInfo($stocknumber, "ShortDescription", $xml);    
                        $description = $this->setProductInfo($stocknumber, "InfoText", $xml);   
                                
			//if ($sync_product_shortdescription == "1") {
			//	$short_description  = trim($xml->WebStore->ItemData->Item->ShortDescription[0]);
			//}

			//if ($sync_product_longdescription == "1") {
			//	$description = trim ($xml->WebStore->ItemData->Item->InfoText[0]);
			//}

			//if ($sync_product_name == "1") {
			//	$product_name = trim ($xml->WebStore->ItemData->Item->ProductName[0]);
			//}

			// we do need a description when we create the intiial product, magento will not allow
			// the product to be created as decriptions are required fields.
			//if ($description == "")
			//{
			//	// we need to make sure that we have SOMETHING, use the SKU.
			//	$description = GetLatinLongDescription();
			//}

			//if ($short_description == "")
			//{
			//	$short_description = GetLatinShortDescription();
			//}

			//if ($product_name == "")
			//{
			//	$product_name = $stocknumber . " " . GetLatinProductName();
			//}

			$raw_data = trim(base64_decode($xml->WebStore->ItemData->Item->RowData[0]));

			$xml_raw = new XMLToArray( $raw_data, array(), array(), true, false );
			$external_data = $xml_raw->GetArray();

			// added for store updating.
			$AssignToWebStore = intval(escapeInventoryFileData($xml->WebStore->ItemData->Item->AssignToWebStore[0]));

			// 28Jun2013 GJM pric should come from the base XML <price> tag, special price could be used.
			//$price = str_replace(",","",escapeInventoryFileData($xml->WebStore->ItemData->Item->PriceData->Price->Amount[0]));
			$price = str_replace(",","",escapeInventoryFileData($xml->WebStore->ItemData->Item->Price[0]));


			// map channelbrain to Magento.
                        //*@AA:06/29/16 12:18:22 PM:BSXL-390
                        // add upccode and isbnnumber 
                        //*@AA:07/06/16 10:57:37 AM:BSXL-433
                        // escape short_description and description only when removing html                        
			$omx2mage_product_data_map = array(
				'name'              => escapeInventoryFileData($product_name),
				'weight'	    => $wgt ? $wgt : 1,
				'short_description' => ($this->remove_html ? escapeInventoryFileData($short_description) : $short_description),
				'description'       => ($this->remove_html ? escapeInventoryFileData($description) : $description),
				'price'             =>  $price,
				'tax_class_id'      => $taxable,
				'status'            => $is_active,
                                'visibility'        => 4,
                                'upccode'           => $upccode,
                                'isbnnumber'        => $isbnnumber                            
			);

			if (count ($external_data) > 0)
			{
				$external_data = serialize ($external_data);
				$external_data_field = array ('external_data'=>$external_data);
				// make sure we add the field into the array to add the data.
				$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $external_data_field);
			}


			$prod_type = "simple";

                        //*@AA:11/20/15 05:35:57 PM:BSXL-320
                        // when checking variation count, test should have been > 0, not > 1 
			// Are we a configurable product with subItems ?
			if (count($xml->WebStore->ItemData->Item->SubItem) > 0 )
			{
				$prod_type = "configurable";
			}

			// go into admin mode
			//Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			// gjm: 01/16/2013 - Mode wasn't working!?
			Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
			$prod = Mage::getModel('catalog/product');
			$productId = $prod->getIdBySku($stocknumber);

                        //syncNewProduct().1
			if ($prod_type == "simple")
			{

				if( $productId > 0 )
				{
					// if updating, we should be in syncExistingProduct(), not here
					return "Error in " . __FUNCTION__ . " on line " . __LINE__ . " : " .  " product id already exists.";
				}
				else
				{
					$prod_api =  new Mage_Catalog_Model_Product_Api();

					// BUG: we need to get the stores this product belongs in and make sure its included with the existing stores.
					// get stores the product is in...
					$websiteids = $prod->getWebsiteIds();
					if ($AssignToWebStore == 1)
					{
						// now add the store id this product was told to go into.
						// BSXL-98 2014-07-23 PJQ - set the website based on the store
						$store_model = Mage::getModel('core/store');
						$store_data =  $store_model->load($this->BizSyncStoreID);
						$websiteids[] = $store_data->getWebsiteId();
						$websites['websites'] = array_unique ($websiteids);
						$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
					}
					$productId = $prod_api->create($prod_type, $default_attribute_set_id, $itemcode, $omx2mage_product_data_map);
				}

				// set the inventory quantity
				$qty = intval($xml->WebStore->ItemData->Item->Available[0]);
				$in_stock = ($qty > 0) ? 1 : 0;
				$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();

				$manage_stock_array = array('qty' => $qty); // always care about the quantity.

				// GJM: On a new product (create) we want to make sure we can set the manage_stock flag.  It's optional from BizSyncXL.
				// however, on a create it's perfectly fine to do this.
				if ($manage_inventory == "1")
				{
					$manage_stock_array = array_merge ($manage_stock_array, array ('manage_stock' => $manage_inventory));
				}
				//if (($is_in_stock == "1") && ($enable_product_status == "1"))
                                if (($is_in_stock == "1")) {
					$manage_stock_array = array_merge ($manage_stock_array, array ('is_in_stock' => $in_stock));
				}

				// why are we setting this?!
				//'use_config_manage_stock'=>$manage_inventory, );
				$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $manage_stock_array);
				$product_stock->update($stocknumber, $omx2mage_product_data_map, intval($this->BizSyncStoreID));
			}

                        //syncNewProduct().2
			// init configurable product and handle sub items
			if( $prod_type == "configurable" )
			{
				$attribute_ids = array();
				$subitem_ids   = array();
				$subitem_skus   = array();
                                $subitem_skus_test = array();

				// do subitems...
				if( count($xml->WebStore->ItemData->Item->SubItem) > 0 )
				{
                                        ///////
                                        /////
                                        ////
                                        ///
                                        //
                                        // TODO: get existing subitem_sku's (use new node items)
                                        // $subitem_skus should be filled with skus from earlier call
                                        // 
                                        // 
                                        ///
                                        ////
                                        /////                
                                        ///////
                                    
                                    
					$result = $this->DoSubItems("1", $xml, $default_attribute_set_id, $omx2mage_product_data_map, $itemcode, $attribute_ids, $subitem_ids, $subitem_skus);
					if ($result != "")
					{
						return $result;
					}
				}
                                //syncNewProduct().3
				if( $productId > 0 )
				{
				    return "Error in " . __FUNCTION__ . " on line " . __LINE__ . " : " .  " product id already exists.";
                                    //return "";
				}
				else
				{
					$prod_api =  Mage::getSingleton("catalog/product_api",array("name"=>"api"));
                                        // DEBUG:
					$omx2mage_product_data_map["associated_skus"] = $subitem_skus;
					// lets ask one of the simple products created in DoSubItems what the config attrs are...
					$prod2 = Mage::getModel('catalog/product');
					$prod2->load($prod2->getIdBySku($subitem_skus[0]));
					foreach($attribute_ids as $the_id)
					{

						$attr = $prod2->getResource()->getAttribute($the_id); //Mage_Catalog_Model_Resource_Eav_Attribute
						//$omx2mage_product_data_map[$attr->getName()] = "";
						$omx2mage_product_data_map[$attr->getAttributeCode()] = "";
					}
					// BUG: we need to get the stores this product belongs in and make sure its included with the existing stores.
					// get stores the product is in...
					$websiteids = $prod2->getWebsiteIds();

					//Mage::Log("websiteids before " . serialize($websiteids), Zend_Log::INFO, "bizsyncphp.log");
					if ($AssignToWebStore == 1)
					{
						// now add the store id this product was told to go into.
						// BSXL-98 2014-07-23 PJQ - set the website based on the store
						$store_model = Mage::getModel('core/store');
						$store_data =  $store_model->load($this->BizSyncStoreID);
						$websiteids[] = $store_data->getWebsiteId();
						$websites['websites'] = array_unique ($websiteids);
						//Mage::Log("websiteids after " . serialize($websites), Zend_Log::INFO, "bizsyncphp.log");
						$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
					}
					$productId = $prod_api->create($prod_type, $default_attribute_set_id, $itemcode, $omx2mage_product_data_map);
				}
				try
				{
					// tell it to use config for stock inventory - use_config_manage_stock
					$prod->load($productId);
					$prod->setTypeId($prod_type);
					$prod->getTypeInstance()->setUsedProductAttributeIds ($attribute_ids) ;
					$ConfigurableAttributesAsArray = $prod->getTypeInstance()->getConfigurableAttributesAsArray($prod);


					//$ConfigurableAttributesAsArray[0]["store_label"] = $ConfigurableAttributesAsArray[0]["frontend_label"];
					//$ConfigurableAttributesAsArray[0]["label"] = $ConfigurableAttributesAsArray[0]["frontend_label"];
					//
					//  2012jul10 - PRICING FOR VARIANTS
					//

					foreach($ConfigurableAttributesAsArray as $key => $val)
					{
						$ConfigurableAttributesAsArray[$key]["store_label"] = $ConfigurableAttributesAsArray[$key]["frontend_label"];
						$ConfigurableAttributesAsArray[$key]["label"] = $ConfigurableAttributesAsArray[$key]["frontend_label"];
						$idx = 0;
						foreach($subitem_ids as $key3 => $val3)
						{
							foreach($val3 as $key2 => $val2)
							{
								$ConfigurableAttributesAsArray[$key]["values"][$idx] = 
                                                                        array(
                                                                            'attribute_id' => $val2['attribute_id'], 
                                                                            'label' => $val2['label'],
                                                                            'value_index' => $val2['value_index'], 
                                                                            'pricing_value' => $val2['pricing_value'], 
                                                                            'is_percent' => 0
                                                                        );
								$idx++;
							}
						}
					}

					//
					//  2012jul10 - END - PRICING FOR VARIANTS
					//
					$prod->setConfigurableAttributesData($ConfigurableAttributesAsArray);
					$prod->setCanSaveConfigurableAttributes(true);
					$prod->setCanSaveCustomOptions(true);
                                        //syncNewProduct().4
                                        // DEBUG: next line adds the associated products
                                        //if ($this->ActionValues["is_final"] == "1")
                                        //{
                                            $prod->setConfigurableProductsData($subitem_ids) ;
                                        //}
					// save.
					$prod->setIsMassupdate(true);
					$prod->setExcludeUrlRewrite(true);
					$prod->getTypeInstance()->save();
				} catch(Exception $e) {
					Mage::Log("Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage(), Zend_Log::ERR, "bizsyncphp.log");
					return "Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage();
				}
			} // end if

			// now do the image data.
			if( is_numeric( $productId ) )
			{

				if( count($xml->WebStore->ItemData->Item->ImageData->Image) > 0 )
				{
					$numAD = count($xml->WebStore->ItemData->Item->ImageData->Image);

					// remove any existing images this product may have had.
					$mediaApi = Mage::getModel("catalog/product_attribute_media_api");

					$items = $mediaApi->items($productId);
					if (count ($items))
					{
						foreach($items as $item)
						{
							$result = $mediaApi->remove($productId, $item['file']);
						}
					}

					for($k=0; $k<$numAD; $k++)
					{
						$attr = $xml->WebStore->ItemData->Item->ImageData->Image[$k]->attributes();
						if( strtolower($attr["type"]) == "image" ||
							strtolower($attr["type"]) == "thumbnail" ||
							strtolower($attr["type"]) == "small_image")
						{
							$filename = $attr['filename'];
							$tag = $attr['tag'];
							$image_type = $attr["type"]; // image, small_image, or thumbnail

							// 1.7 magento appears to not have the media/import folder.
							// use the media folder.
							$base_media_dir = Mage::getBaseDir('media') . "/import";
							if (!is_dir($base_media_dir))
							{
								$base_media_dir = Mage::getBaseDir('media');
							}
							$fullImagePath = $base_media_dir . "/" . $filename;

							if ($attr['image_data'] != "")
							{
								// always overwrite the file.
								file_put_contents($fullImagePath, base64_decode (trim($attr['image_data'])));
								if(file_exists($fullImagePath) && is_file($fullImagePath))
								{
									$result = $this->AddProductImage( $productId, $fullImagePath, $image_type );
								} else {
									return "Image '" . $filename . "' did not exist.";
								}
							}
						}
					}
				}
			}

			$prod = Mage::getModel('catalog/product');
			$prod->load($productId);

			// Set the Product Special Price in Magento
			// If MOM, there has to be a Qty of 1 thats less than what's in the Price1 field.
			// If RMS, there has to be a price in the "Special Price" field and it has to be less than the MSRP.
			$result = $this->SetProductSpecialPrice ($xml,$prod);
			if ($result != true)
			{
				return $result;
			}

			/* GJM: We no longer set the tier price on the product sync. Only do this on SyncPrices.
						$result = SetProductTierPrice ($xml, $productId);
						if ($result != true)
						{
							return $result;
						}
			*/


		} catch (Exception $e) {
			Mage::Log("Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage(), Zend_Log::ERR, "bizsyncphp.log");
			return "Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage();
		}

		return $productId;
	}

	/**
	 * syncExistingProduct function.
	 *
	 * @access private
	 * @param mixed $itemcode
	 * @param mixed $data
	 * @param mixed $default_attribute_set_id
	 * @return void
	 */
	private function syncExistingProduct($itemcode, $data, $default_attribute_set_id)
	{
		$xml = simplexml_load_string ($data);
		if (get_class($xml) != "SimpleXMLElement")
		{
			return "Error: (syncExistingProduct) XML invalid and could not parse.";
		}

		if($itemcode == "")
		{
			return "Item code missing";
		}

		if($data == "")
		{
			return "Data empty";
		}

		if(!is_numeric($default_attribute_set_id))
		{
			return "Default attribute id not set";
		}

                //$enable_product_status = $this->ActionValues['enable_product_status'];
                
		// added for store updating.
		$AssignToWebStore = intval(escapeInventoryFileData($xml->WebStore->ItemData->Item->AssignToWebStore[0]));


		$item_attributes = $xml->WebStore->ItemData->Item->attributes();
		$stocknumber = $item_attributes['itemCode'];
		if ($stocknumber == '')
		{
			return "XML had no stocknumber";
		}

		if ($stocknumber != $itemcode)
		{
			return "XML stocknumber did not match itemcode";
		}

		$is_active = ($item_attributes['active']) == "True" ? 1 : 2;
		
                //*@AA:06/29/16 12:18:22 PM:BSXL-390
                $upccode = escapeInventoryFileData($xml->WebStore->ItemData->Item->UPCCode[0]);
                $isbnnumber = escapeInventoryFileData($xml->WebStore->ItemData->Item->ISBNNumber[0]); 
                
                $wgt = escapeInventoryFileData($xml->WebStore->ItemData->Item->Weight[0]);
		$raw_data = trim(base64_decode($xml->WebStore->ItemData->Item->RowData[0]));
		$xml_raw = new XMLToArray( $raw_data, array(), array(), true, false );
		$external_data = $xml_raw->GetArray();

		// 2014mar04 PJQ - set it to "2" for taxable and "0" for non taxable and then use it to set value of tax_class_id
		// BSXL-25
		$taxable = ($item_attributes['isTaxable']) == "1" ? 2 : 0;

		// 29Jun13 GJM: Price is NOT the first line in the PriceData scope, its the price in the main block
		//$price = str_replace(",","",escapeInventoryFileData($xml->WebStore->ItemData->Item->PriceData->Price->Amount[0]));
		$price = str_replace(",","",escapeInventoryFileData($xml->WebStore->ItemData->Item->Price[0]));
		$manage_inventory = 		trim($this->ActionValues['manage_inventory']);
		if ($manage_inventory == "")
		{
			$manage_inventory = "0"; // default to not setting anything.
		}

		// GJM 05-25-2015
		$is_in_stock = trim($this->ActionValues['is_in_stock']);
		if ($is_in_stock == "")
		{
			$is_in_stock = "0"; // default to not setting anything.
		}

                $short_description = trim($xml->WebStore->ItemData->Item->ShortDescription[0]);
                $description = trim($xml->WebStore->ItemData->Item->InfoText[0]); 
                $product_name = trim($xml->WebStore->ItemData->Item->ProductName[0]);

		// map channelbrain to Magento.
                //*@AA:06/29/16 12:18:22 PM:BSXL-390
                // add upccode and isbnnumber                
                //*@AA:07/06/16 10:57:37 AM:BSXL-433
                // escape short_description and description only when removing html 
                $omx2mage_product_data_map = array(
			'name'              		=> escapeInventoryFileData($product_name),
			'weight'	    		=> $wgt ? $wgt : 1,
			'short_description' 		=> ($this->remove_html ? escapeInventoryFileData($short_description) : $short_description),
			'description'			=> ($this->remove_html ? escapeInventoryFileData($description) : $description),
			'price'				=> $price,
			'tax_class_id'			=> $taxable,
			'status'			=> $is_active,
                        'visibility'                    => 4,
                        'upccode'                       => $upccode,
                        'isbnnumber'                    => $isbnnumber
                );



		if (count ($external_data) > 0)
		{
			$external_data = serialize ($external_data);
			$external_data_field = array ('external_data'=>$external_data);
			// make sure we add the field into the array to add the data.
			$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $external_data_field);
		}


		$sync_product_longdescription = trim($this->ActionValues['sync_product_longdescription']);
		$sync_product_shortdescription = trim($this->ActionValues['sync_product_shortdescription']);


		$sync_product_name = trim($this->ActionValues['sync_product_name']);

		// let's determine if we're syncing the name and the long description.
		if ($sync_product_longdescription == "2") {
			unset ($omx2mage_product_data_map['description']);
		}

		// let's determine if we're syncing the name and the short description.
		if ($sync_product_shortdescription == "2") {
			unset ($omx2mage_product_data_map['short_description']);
		}

		// let's determine if we're syncing the name and the short description.
		if ($sync_product_name == "2") {
			unset ($omx2mage_product_data_map['name']);
		}

		$prod_type = "simple";

		// Are we a configurable product with subItems ?
		if( count($xml->WebStore->ItemData->Item->SubItem) > 0 )
		{
			$prod_type = "configurable";
		}



		// go into admin mode
		//Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		// gjm: 01/16/2013 - Mode wasn't working!?
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
		$prod = Mage::getModel('catalog/product');
		$productId = $prod->getIdBySku($stocknumber);
		$prod->load($productId);

		// let's determine if we're syncing the name and the product name.
		if ($sync_product_name == "2") {
			//unset ($omx2mage_product_data_map['name']);
			$omx2mage_product_data_map['name'] = $prod->getName();
		}


                //syncExistingProduct().1
		if($prod_type == "simple")
		{
			if( $productId > 0 )
			{
				$prod_api =  new Mage_Catalog_Model_Product_Api();

				// BUG: we need to get the stores this product belongs in and make sure its included with the existing stores.
				// get stores the product is in...
				$websiteids = $prod->getWebsiteIds();
				//Mage::Log("websiteids before " . serialize($websiteids), Zend_Log::INFO, "bizsyncphp.log");
				if ($AssignToWebStore == 1)
				{
					// now add the store id this product was told to go into.
					// BSXL-98 2014-07-23 PJQ - set the website based on the store
					$store_model = Mage::getModel('core/store');
					$store_data =  $store_model->load($this->BizSyncStoreID);
					$websiteids[] = $store_data->getWebsiteId();
					$websites['websites'] = array_unique ($websiteids);
					//Mage::Log("websiteids after " . serialize($websites), Zend_Log::INFO, "bizsyncphp.log");
					$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
				}
				// 2013dec17 PJQ - add 4th parameter identifierType to call
				$prod_api->update($productId, $omx2mage_product_data_map, Mage_Core_Model_App::ADMIN_STORE_ID, "id");
			}
			else
			{
				return "Product id was invalid";
			}

			// set the inventory quantity
			$qty = intval($xml->WebStore->ItemData->Item->Available[0]);
			$in_stock = ($qty > 0) ? 1 : 0;
			$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();


			$manage_stock_array = array('qty' => $qty); // always care about the quantity.
			// On a product update, we only want to set the is_in_stock flag -- it's optional. The manage stock flag, we never want to touch.
			//if (($is_in_stock == "1") && ($enable_product_status == "1"))
                        if (($is_in_stock == "1")) {
				$manage_stock_array = array_merge ($manage_stock_array, array ('is_in_stock' => $in_stock));
			}
			$product_stock->update($stocknumber, $manage_stock_array, intval($this->BizSyncStoreID));
		}

                //syncExistingProduct().2
		// init configurable product and handle sub items
		if( $prod_type == "configurable" )
		{
			$attribute_ids = array();
			$subitem_ids   = array();
			$subitem_skus   = array();
                        $subitem_skus_test = array();

                        ///////
                        /////
                        ////
                        ///
                        //
                        // TODO: get existing subitem_sku's (use new node items)
                        // $subitem_skus should be filled with skus from earlier call
                        //if ($this->ActionValues['is_final'] == "1")                             
                        //{
                            //$subitem_skus[] = "9MAGCOLOR YELL";
                            //$subitem_skus[] = "9MAGCOLOR WATERMELON";
                            
                        //}
                        // 
                        // 
                        ///
                        ////
                        /////                
                        ///////
                                        
			// do subitems...
			if( count($xml->WebStore->ItemData->Item->SubItem) > 0 )
			{
				$this->DoSubItems("0", $xml, $default_attribute_set_id, $omx2mage_product_data_map, $itemcode, $attribute_ids, $subitem_ids, $subitem_skus);
			}
                        //syncExistingProduct().3
			if( $productId > 0 )
			{                           
				$prod_api =  new Mage_Catalog_Model_Product_Api();
                                // DEBUG:
				$omx2mage_product_data_map["associated_skus"] = $subitem_skus;

				// get stores the product is in...
				$websiteids = $prod->getWebsiteIds();
				if ($AssignToWebStore == 1)
				{
					// now add the store id this product was told to go into.
					// BSXL-98 2014-07-23 PJQ - set the website based on the store
					$store_model = Mage::getModel('core/store');
					$store_data =  $store_model->load($this->BizSyncStoreID);
					$websiteids[] = $store_data->getWebsiteId();
					$websites['websites'] = array_unique ($websiteids);
					$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
				}
				// 2013dec17 PJQ - add 4th parameter identifierType to call
				$prod_api->update($productId, $omx2mage_product_data_map, Mage_Core_Model_App::ADMIN_STORE_ID, "id");
			}
			else
			{
				return "Product id was invalid";
			}

			try
			{
				//$prod->load($productId);
				$prod->setTypeId($prod_type);

				//
				//  2012jul10 - PRICING FOR VARIANTS
				//

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
							$ConfigurableAttributesAsArray[$key]["values"][$idx] = array('attribute_id' => $val2['attribute_id'], 'label' => $val2['label'],'value_index' => $val2['value_index'], 'pricing_value' => $val2['pricing_value'], 'is_percent' => 0);
							$idx++;
						}
					}
				}
				$prod->setConfigurableAttributesData($ConfigurableAttributesAsArray);

				//
				//  2012jul10 - END - PRICING FOR VARIANTS
				//
                                //syncExistingProduct().4
                                // DEBUG: next line adds the associated products
                                //if ($this->ActionValues["is_final"] == "1")
                                //{
                                $prod->setConfigurableProductsData($subitem_ids) ;    
                                //}				
				$prod->getTypeInstance()->save();

			} catch(Exception $e) {
				Mage::Log("Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage(), Zend_Log::ERR, "bizsyncphp.log");
				return "Exception in " . $e->getFile() . " on line " . $e->getLine() . " : " .  $e->GetMessage();
			}
		}


		// make sure prod is initialized
		if( !is_object($prod) )
		{
			$prod = Mage::getModel('catalog/product');
			$prod->load($productId);
			$prod->setTypeId($prod_type);
		}

		if( is_numeric( $productId ) )
		{

			if( count($xml->WebStore->ItemData->Item->ImageData->Image) > 0 )
			{
				$numAD = count($xml->WebStore->ItemData->Item->ImageData->Image);
				for($k=0; $k<$numAD; $k++)
				{
					$attr = $xml->WebStore->ItemData->Item->ImageData->Image[$k]->attributes();
					if( strtolower($attr["type"]) == "image" ||
						strtolower($attr["type"]) == "thumbnail" ||
						strtolower($attr["type"]) == "small_image")
					{
						$filename = $attr['filename'];
						$image_type = trim($attr["type"]); // image, small_image, or thumbnail
						if (is_dir (Mage::getBaseDir('media') . "/import/"))
						{
							$fullImagePath = Mage::getBaseDir('media') . "/import/" . $filename;
						} else {
							$fullImagePath = Mage::getBaseDir('media') . "/" . $filename;
						}
						if ($attr['image_data'] != "")
						{
							// always overwrite the file.
							file_put_contents($fullImagePath, base64_decode (trim($attr['image_data'])));
							if(file_exists($fullImagePath) && is_file($fullImagePath))
							{
								$result = $this->AddProductImage( $productId, $fullImagePath, $image_type );
							}
						}
					}
				}
			}
		}

		// Set the Product Special Price in Magento
		// If MOM, there has to be a Qty of 1 thats less than what's in the Price1 field.
		// If RMS, there has to be a price in the "Special Price" field and it has to be less than the MSRP.
		$result = $this->SetProductSpecialPrice ($xml,$prod);
		if ($result != true)
		{
			return $result;
		}

		/* GJM: We no longer set the tier price on the product sync. Only do this on SyncPrices.
                    $result = SetProductTierPrice ($xml, $productId);
                    if ($result != true)
                    {
                        return $result;
                    }
        */

		return true;
	}


	/**
	 * DoSubItems function.
	 * 
	 * @access private
         * @param mixed $isnew
	 * @param mixed &$xml
	 * @param mixed $default_attribute_set_id
	 * @param mixed $omx2mage_product_data_map
	 * @param mixed $parent_stocknumber
	 * @param mixed &$attribute_ids
	 * @param mixed &$subitem_ids
	 * @param mixed &$subitem_skus
	 * @return void
	 */
	private function DoSubItems($isnew, &$xml, $default_attribute_set_id, $omx2mage_product_data_map, $parent_stocknumber, &$attribute_ids, &$subitem_ids, &$subitem_skus)
	{
                $sync_product_name = trim($this->ActionValues['sync_product_name']);
                $sync_product_shortdescription = trim($this->ActionValues['sync_product_shortdescription']);
                $sync_product_longdescription = trim($this->ActionValues['sync_product_longdescription']);
                //$enable_product_status = $this->ActionValues['enable_product_status'];
                
		$numSubItems = count($xml->WebStore->ItemData->Item->SubItem);
		if( 0 == $numSubItems )
		{
			return;
		}
		$base_name = $omx2mage_product_data_map["name"];
		$variation_dims = array();
		$map_of_dd = $this->massageDimensionData($xml->WebStore->ItemData->Item->DimensionData, $variation_dims);
		// find the "dimension" attributes
		$prod_attr_api = new Mage_Catalog_Model_Product_Attribute_Api();
		$attrs = $prod_attr_api->items($default_attribute_set_id);
		$num_attra = count($attrs);
		$num_dims = count($variation_dims);

		for($y=0; $y<$num_dims; $y++)
		{
			$bfound = 0;

			$code = strtolower($variation_dims[$y]);
			$label = strtolower($variation_dims[$y]);
			$code = $this->ValidateMageAttributeCode($code);
			for($z=0; $z<$num_attra; $z++)
			{
				if($code == $attrs[$z]["code"] )
				{
					$attribute_ids[$code] = $attrs[$z]["attribute_id"];
					$bfound = 1;
				}
			}

			if( !$bfound )
			{
				$attribute_ids[$code] = $this->AddMageAttribute( $code , $label, $default_attribute_set_id);
			}
		}

		$manage_inventory = trim($this->ActionValues['manage_inventory']);
		if ($manage_inventory == "")
		{
			$manage_inventory = "0"; // default to not setting anything.
		}

		// GJM 05-25-2015
		$is_in_stock = trim($this->ActionValues['is_in_stock']);
		if ($is_in_stock == "")
		{
			$is_in_stock = "0"; // default to not setting anything.
		}

                $product_name = "";
                $short_description = "";
		$description = "";
                
		$prod_api =  new Mage_Catalog_Model_Product_Api();
                                  
		for($i=0; $i<$numSubItems; $i++)
		{

			$sub_attributes = $xml->WebStore->ItemData->Item->SubItem[$i]->attributes();

			//$sub_stocknumber = str_replace("-", "_DASH_", $sub_attributes['itemCode']);
			$sub_stocknumber = strval($sub_attributes['itemCode']);
                        
			//*@AA:10/22/15 07:36:19 PM:BSXL-343
			// Don't use parent status, use sub item status                         
			$omx2mage_product_data_map["status"] = ($sub_attributes['active']) == "True" ? 1 : 2;

			//$sub_sku = strval($sub_attributes['itemCode']);
                        //$sub_sku = $sub_stocknumber;
			//$subitem_skus[] = $sub_sku;
                        $subitem_skus[] = $sub_stocknumber;

			$sizecolor_description = "";
			$the_size = "";
			$the_color = "";
			$sur = 0.00;
			// get the desc and surcharge from dimension data
			$numDims = count($xml->WebStore->ItemData->Item->SubItem[$i]->ItemDimension);
			for($q=0; $q<$numDims; $q++)
			{
				$attr = $xml->WebStore->ItemData->Item->SubItem[$i]->ItemDimension[$q]->attributes();
				$name = $attr["name"];
                                // $key = "size_1" ... "size_N"                                
				$key  = $name . "_" . $xml->WebStore->ItemData->Item->SubItem[$i]->ItemDimension[$q];

				if ($q > 0)
				{
					$sizecolor_description .= " ";
				}

				if( $this->use_subitem_dimension_label_in_name )
				{
					$sizecolor_description .=  trim ($name . " " . $map_of_dd[$key]["Description"]);
				}
				else
				{
					$sizecolor_description .=  trim ($map_of_dd[$key]["Description"]);
				}

				// BSXL-136 2014-12-03 PJQ - don't forget the sizecolor desc
				$sizecolor_description = escapeInventoryFileData($sizecolor_description);

				$sur += $map_of_dd[$key]["Surcharge"];

				$lname = strtolower($name);
				$lname = $this->ValidateMageAttributeCode ($lname);

				if(array_key_exists($lname,$attribute_ids))
				{
					$the_dim_id = 0;
					$the_dim = strval($map_of_dd[$key]["Description"]);

					// we need to get the existings sizes and colors
					//$dim_options = $prod_attr_api->options($attribute_ids[$lname]);
					$attribute_model        = Mage::getModel('eav/entity_attribute');
					$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
					$attribute              = $attribute_model->load($attribute_ids[$lname]);
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
						$attribute              = $attribute_model->load($attribute_ids[$lname]);

						$new_value['option'] = array($the_dim,$the_dim);
						$result = array('value' => $new_value);
						$attribute->setData('option',$result);
						try
						{
							$attribute->save();
						}
						catch( Exception $e )
						{
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
							return "Error in " . __FUNCTION__ . " on line " . __LINE__ . ". Dimension ID was empty. Size color description might be empty as an option.";
						}
					}
					$omx2mage_product_data_map[$lname] = $the_dim_id;
				}
				else
				{
					return "Error in " . __FUNCTION__ . " on line " . __LINE__ . ". Attribute array empty.";
				}

			} // end for

                        //*@AA:06/29/16 12:18:22 PM:BSXL-390
                        // add upccode and isbnnumber
                        $upccode = escapeInventoryFileData($xml->WebStore->ItemData->Item->SubItem[$i]->UPCCode);
                        $isbnnumber = escapeInventoryFileData($xml->WebStore->ItemData->Item->SubItem[$i]->ISBNNumber);                        
                                                
                        //*@AA:10/28/15 05:11:46 PM:BSXL-326
                        // Don't default descriptions to last variation in MOM, use descriptions in each variation
                        $product_name = escapeInventoryFileData($xml->WebStore->ItemData->Item->SubItem[$i]->ProductName);
                        $short_description = trim($xml->WebStore->ItemData->Item->SubItem[$i]->ShortDescription);
                        $description = trim($xml->WebStore->ItemData->Item->SubItem[$i]->InfoText);

                        if ($sync_product_shortdescription == "2") {
                            $short_description = "";                                             
                        }

                        if ($sync_product_longdescription == "2") {
                            $description = "";
                        }
                        
                        if ($sync_product_name == "2") {
                            $product_name = "";
                        } 
                        
			// we do need a description when we create the intiial product, magento will not allow
			// the product to be created as decriptions are required fields.
                        // product is new and descriptoin is blank
			if ($description == "" && $isnew=="1") {				
				$description = GetLatinLongDescription();
			}
		
			if ($short_description == "" && $isnew=="1") {
				$short_description = GetLatinShortDescription();
			}

			if ($product_name == "" && $isnew=="1") {
                                // we need to make sure that we have SOMETHING, use the SKU.
				$product_name = $stocknumber . " " . GetLatinProductName();
			}

                        //*@AA:06/29/16 12:18:22 PM:BSXL-390
                        // add upccode and isbnnumber                        
                        $omx2mage_product_data_map["upccode"] = $upccode;
                        $omx2mage_product_data_map["isbnnumber"] = $isbnnumber;
                                
                        $omx2mage_product_data_map["name"] = $product_name;
                        
                        //*@AA:07/06/16 10:57:37 AM:BSXL-433
                        // escape short_description and description only when removing html
                        $omx2mage_product_data_map["short_description"] = ($this->remove_html ? escapeInventoryFileData($short_description) : $short_description);
                        $omx2mage_product_data_map["description"] = ($this->remove_html ? escapeInventoryFileData($description) : $description);                                                
			//$omx2mage_product_data_map["name"] = $base_name . " " . $sizecolor_description;

                        // if not syncing product name and sub item already exists, don't overwrite
                        if ($sync_product_name == "2" && $isnew =="0") {                   
                                unset ($omx2mage_product_data_map["name"]);                  
                        }

                        if ($sync_product_shortdescription == "2" && $isnew =="0") {                   
                                unset ($omx2mage_product_data_map["short_description"]);                  
                        }
                        
                        if ($sync_product_longdescription == "2" && $isnew =="0") {                   
                                unset ($omx2mage_product_data_map["description"]);                  
                        }
                        
                        // Issue No 242253 2014-11-24 PJQ - set product variations to "Not visible individually"
			$omx2mage_product_data_map['visibility'] = 1;
                                
                        $omx2mage_product_data_map["sku"] = $sub_stocknumber;
                        
			if(abs($sur) > .01)
			{
				$omx2mage_product_data_map["price"] += $sur;
			}

			// added for store updating.
			$AssignToWebStore = intval(escapeInventoryFileData($xml->WebStore->ItemData->Item->AssignToWebStore[0]));

			$prod = Mage::getModel('catalog/product');
			$productId = $prod->getIdBySku($sub_stocknumber);
			$prod->load($productId);

			try
			{
				// this call to info() can throw a "not_exists" exception, if so, catch it and create it

				// BUG: we need to get the stores this product belongs in and make sure its included with the existing stores.
				// get stores the product is in...

				// 2012-Nov14 PJQ - first load the prod object, then call getWebsiteIds()
				$prod->load($prod->getIdBySku($sub_stocknumber));
				$websiteids = $prod->getWebsiteIds();

				if ($AssignToWebStore == 1)
				{
					// now add the store id this product was told to go into.
					// BSXL-98 2014-07-23 PJQ - set the website based on the store
					$store_model = Mage::getModel('core/store');
					$store_data =  $store_model->load($this->BizSyncStoreID);
					$websiteids[] = $store_data->getWebsiteId();
					$websites['websites'] = array_unique ($websiteids);
					$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
				}
				// gjm: for Sku's that were numbers, this was failing. Use the product id 12/31/12.
				$info = $prod_api->info ($productId);
				//$info = $prod_api->info($sub_stocknumber);

				//// Issue No 242253 2014-11-24 PJQ - set product variations to "Not visible individually"
				//$omx2mage_product_data_map[visibility] = 1;


				if( $info["sku"] == $sub_stocknumber )
				{
					$productId = $prod->getIdBySku($sub_stocknumber);
					// 2013dec17 PJQ - add 4th parameter identifierType to call
					$prod_api->update($productId, $omx2mage_product_data_map, Mage_Core_Model_App::ADMIN_STORE_ID, "id");

				}
				else
				{
					$productId = $prod_api->create('simple', $default_attribute_set_id, $sub_stocknumber, $omx2mage_product_data_map);
				}
			}
			catch(Exception $e)
			{
				//$websiteids = $prod->getWebsiteIds();
				if ($AssignToWebStore == 1)
				{
					// now add the store id this product was told to go into.
					// BSXL-98 2014-07-23 PJQ - set the website based on the store
					$store_model = Mage::getModel('core/store');
					$store_data =  $store_model->load($this->BizSyncStoreID);
					$websiteids[] = $store_data->getWebsiteId();
					$websites['websites'] = array_unique ($websiteids);
					$omx2mage_product_data_map = array_merge ($omx2mage_product_data_map, $websites);
				}
				$productId = $prod_api->create('simple', $default_attribute_set_id, $sub_stocknumber, $omx2mage_product_data_map);
			}

			if(abs($sur) > .01)
			{
				$omx2mage_product_data_map["price"] -= $sur;
			}

			// why are we doing this ??? we need the id's of the product as the key to the array to be returned by reference.
			if( is_numeric($productId) )
			{
				$num_attribute_ids = count($attribute_ids);
				$subitem_ids[$productId] = array();
				//for($a=0; $a<$num_attribute_ids; $a++)
				foreach($attribute_ids as $key => $val)
				{
					try
					{
						//$subitem_ids[$productId][$a] = array('attribute_id' => $attribute_ids[$a]);
						$subitem_ids[$productId][$key] = 
                                                        array(
                                                            'attribute_id' => $val, 
                                                            'label' => $sizecolor_description ,
                                                            'value_index' => $omx2mage_product_data_map[$lname], 
                                                            'pricing_value' => $sur, 
                                                            'is_percent' => 0
                                                            );
					}
					catch( Exception $e )
					{
						return "Error: " . __FUNCTION__ . " on line " . __LINE__ . " : " .  $e->GetMessage();
					}
				}
			}
			// set the inventory quantity
			$qty = intval($xml->WebStore->ItemData->Item->SubItem[$i]->Available[0]);
			$in_stock = ($qty > 0) ? 1 : 0;
			$product_stock = new Mage_CatalogInventory_Model_Stock_Item_Api();

			$manage_stock_array = array('qty' => $qty); // always care about the quantity.
			//if (($is_in_stock == "1") && ($enable_product_status == "1"))
                        if (($is_in_stock == "1")) {
				$manage_stock_array = array_merge ($manage_stock_array, array ('is_in_stock' => $in_stock));
			}
			$product_stock->update($sub_stocknumber, $manage_stock_array, intval($this->BizSyncStoreID));
		}
	}
        
        private function setProductInfo($sku, $fieldname, &$xml)
        /**
	 * setproductinfo function.
	 * 
	 * @access public
	 * @param mixed $fieldname
	 * @return void
	 */        
        {          
            $output = "";
            $sync_product_name = trim($this->ActionValues['sync_product_name']);
            $sync_product_shortdescription = trim($this->ActionValues['sync_product_shortdescription']);
            $sync_product_longdescription = trim($this->ActionValues['sync_product_longdescription']);  
            
            switch ($fieldname)
            {
                case 'ProductName': //Magento Product "Name" 
                    if ($sync_product_name != "2") {
			$output = trim ($xml->WebStore->ItemData->Item->ProductName[0]);
                    }

                    if ($output == "") {
                            $output = $sku . " " . GetLatinProductName();
                    } 
                    
                    break;
                case 'InfoText': //Magento Product "Description" 
                    if ($sync_product_longdescription != "2") {
                        $output = trim ($xml->WebStore->ItemData->Item->InfoText[0]);
                    }          

                    // we do need a description when we create the intiial product, magento will not allow
                    // the product to be created as decriptions are required fields.
                    if ($output == "") {
                            // we need to make sure that we have SOMETHING, use the SKU.
                            $output = GetLatinLongDescription();
                    }                    
                    break;                
                case 'ShortDescription': //Magento Product "Short Description" 
                    if ($sync_product_shortdescription != "2") {
                        $output = trim($xml->WebStore->ItemData->Item->ShortDescription[0]);
                    }
		
                    // we do need a description when we create the intiial product, magento will not allow
                    // the product to be created as decriptions are required fields.			
                    if ($output == "") {
                            $output = GetLatinShortDescription();
                    }                    
                    break;
            }                        
            return $output;
        }
        
	/**
	 * massageDimensionData function.
	 * 
	 * @access private
	 * @param mixed $dimensiondata
	 * @param mixed &$variation_theme
	 * @return void
	 */
	private function massageDimensionData($dimensiondata, &$variation_dims)
	{
		$map = array();
		//$variation_theme = "";
		$numdim = count($dimensiondata->Dimension);
		for($i=0; $i<$numdim; $i++)
		{
			$attrs = $dimensiondata->Dimension[$i]->attributes();
			$dimname = $attrs["name"];

			// build variation theme from dimension name
			//$variation_theme .= ($i > 0) ? "-" . $dimname : $dimname;
			$variation_dims[$i] = strval($dimname);

			$numval = count($dimensiondata->Dimension[$i]->Value);
			for($j=0; $j<$numval; $j++)
			{
				$attrs2 = $dimensiondata->Dimension[$i]->Value[$j]->attributes();
				$valueID = $attrs2["valueID"];
				$key = $dimname . "_" . $valueID;
				$desc = $dimensiondata->Dimension[$i]->Value[$j]->Description[0];
				$surcharge = $dimensiondata->Dimension[$i]->Value[$j]->Surcharge[0];

				//*@AA:10/09/15 11:27:32 AM:BSXL-275
				//force value to be float having 4 decimal places so cents are handled correctly.
				$surcharge = floatval(number_format(floatval($surcharge),4,'.',''));
				
				// save in map
				$map[$key]["Description"] = $desc;
				$map[$key]["Surcharge"] = $surcharge;
			}
		}
		return $map;
	}

	/**
	 * AddMageAttribute function.
	 * 
	 * @access private
	 * @param mixed $code
	 * @param mixed $default_attribute_set_id
	 * @return void
	 */
	private function AddMageAttribute( $code , $default_attribute_set_id)
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
			return "Exception in " . __FUNCTION__ . " on line " . __LINE__ . " : " .  $e->GetMessage();
		}
		return $new_id;
	}

	/**
	 * AddProductImage function.
	 * 
	 * @access private
	 * @param mixed $product
	 * @param mixed $fullImagePath
	 * @param mixed $typeofimage
	 * @return void
	 */
	private function AddProductImage( $productId, $fullImagePath, $image_type )
	{
	
		$product = Mage::getModel('catalog/product');
		$product->load($productId);
		//$prod->setTypeId($prod_type);
	
		$image_type = trim($image_type);
		try
		{
		  // check input
			if( !is_object($product) )
			{
				return "product object empty.";
			}
			if(!file_exists($fullImagePath) || !is_file($fullImagePath))
			{
				return $fullImagePath . " image file does not exist";
			}

			$arrayValidImageTypes = array("image", "thumbnail", "small_image");

			if( !in_array($image_type,$arrayValidImageTypes) )
			{
				return "'" . $typeofimage . "'" . " image type not found.";
			}
			
			$bFound = 0;
			
			// no media gallery?
			if( !$product->getMediaGallery () )
			{
				//This call is needed since the media gallery is null for a newly created product.
				$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
			}
			else
			{
				// get the existing media gallery
				$images = $product->getMediaGalleryImages();
				// see if the one we are trying to add already exists
				foreach ($images as $key=>$image)
				{
					$needle = basename($fullImagePath);
					$needle = substr($needle, 0, strrpos($needle,"."));
					if( false !== strpos($image->getfile(),$needle  ) )
					{
						$bFound = 1;
						break;
					}
				}
			}
			if( !$bFound )
			{
				$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
				if ($image_type == "image")
				{
					$product->addImageToMediaGallery ($fullImagePath, array('image'), false, false);
				}
				if ($image_type == "small_image")
				{
					$product->addImageToMediaGallery ($fullImagePath, array('small_image'), false, false);
				}
				if ($image_type == "thumbnail")
				{
					$product->addImageToMediaGallery ($fullImagePath, array('thumbnail'), false, false);
				}
				$product->save();
			}
			return "";

		} catch (Exception $e) {
			return "Exception: "  . $e->GetMessage() . " on line " . __LINE__ . ".";			
		}
	}

	/**
	 * SetWebsite function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function SetWebsite ($stocknumber)
	{
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$product = Mage::getModel('catalog/product');
		$productId = $product->getIdBySku($stocknumber);
		$prod_api =  new Mage_Catalog_Model_Product_Api();
		$prod_attrs['websites'] = array ($storeCode);
		$prod_api->update ($productId,$prod_attrs);
		umask(0);
	}
	/**
	 * GetSKU function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function GetSKU()
	{
		$sku = $this->ActionValues['sku'];
		if (!isset($sku))
		{
			$sku = $_REQUEST['sku'];
		}	
		return $sku;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	public function GetModuleVersion()
	{
		return MODULE_BASE_VERSION;
	}

	/**
	 * Action_AddAttribute function.
	 * 
	 * @access public
	 * @return void
	 */
	public function Action_AddAttribute()
	{
	    writeStartTag("AddAttribute");

	    writeCloseTag("AddAttribute");
	}
        
	/* Magento attribute codes have a rule that they can't have spaces and need to be less than or equal to 30 characters. */
	/* gjm: 12/31/12 */
	function ValidateMageAttributeCode($code)
	{
		// gjm: 12/31/2012, make sure that the attribute_code adheres to the no spaces rule.
		$attribute_code = str_replace ("-", "", $code);
		$attribute_code = str_replace (" ", "_", $attribute_code);
		if (strlen ($attribute_code) >= 30)
		{
			$attribute_code = substr ($attribute_code,0,29);
		}
		return $attribute_code;
	}

	/**
	 * Stop_Magento_Indexer function.
	 * Turn off the indexer to speed up performance.
	 * @access public
	 * @return string
	 */
	function Stop_Magento_Indexer()
	{
		if ($this->restart_indexer == true)
		{
			// 2015-05-18 GJM - Before we start bulk update, stop indexing
                        //*@AA:12/23/15 02:31:59 PM:BSXL-358
			$processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
			$processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL));
			$processes->walk('save');
		}
	}

	/**
	 * Start_Magento_Indexer function.
	 * Turn indexer back on and re-index.
	 * @access public
	 * @return string
	 */
	function Start_Magento_Indexer()
	{
		$names = "";

		if ($this->restart_indexer == true)
		{
			// 2015-05-18 GJM - set indexing mode back to auto and reindex everything
			$processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
                        //*@AA:12/23/15 02:31:59 PM:BSXL-358    
			$processes->walk('setMode', array(Mage_Index_Model_Process::MODE_REAL_TIME));
			$processes->walk('save');
			foreach ($processes as $p) {
				if ($p->getIndexer()->isVisible()) {
					$p->reindexEverything();
					$names .= $p->getIndexer()->getName() . "\n";
				}
			}
			return $names;
		} else {
			return "[No Reindex]";
		}
	}
}; // END class
?>