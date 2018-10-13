<?php
class MageCoders_QuickOrder_IndexController extends Mage_Core_Controller_Front_Action{
  
	public function suggestAction(){
	 
		$params = $this->getRequest()->getParams();
		$str = strip_tags(trim($params['q']));
		$query = mysql_escape_string($str);
		
		$query = '%'.$query.'%';
		
		if($query==''){ return; }
		
		$data = array();
		$isCollection = false;
		$isSku = false;					

		$visibility = $this->getConfig('visibility_filter');
		$visibility = explode(',',$visibility);
		
		$sort_column = $this->getConfig('sort_column');
		$limit =  $this->getConfig('number_result');
		
		$types = Mage::helper('quickorder/protected')->getProductTypes();
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$str);
		
		$constant_helper = Mage::helper('grouped/constants');
        $proType = array($constant_helper::PRODUCT_TYPE_ANDROID_APP, $constant_helper::PRODUCT_TYPE_IOS_APP,$constant_helper::PRODUCT_TYPE_WIN_APP );
		
		
		if($product){
					$isSku = true;
					$json = array();
					
					if($this->isProductAllowed($product)){ 
                                         
				   	 if(!in_array($product->getProductType(),$proType)){
					$imageUrl = '';
					if($product->getStatus() && in_array($product->getVisibility(),$visibility)){
						
						if($product->getVisibility() == 1){
							$grouped_product_model = Mage::getModel('catalog/product_type_grouped');
							 $groupedParentId = $grouped_product_model->getParentIdsByChild($product->getId()); 
							if(($count = count($groupedParentId)) >0){  
								$grouped = Mage::getModel('catalog/product')->load($groupedParentId[$count-1]);
								$imageUrl = $this->getImageUrl($grouped);
							} 
						}else{
							$imageUrl = $this->getImageUrl($product); 
						}
						$json['value'] = $product->getSku();
			 			$json['name'] = $product->getName();
						$json['image'] = $imageUrl;
						$json['is_sku'] = $isSku;
						$data[] =  $json; 
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
						return;
					}
                                   }
				}
		}
		
		$collection = Mage::getModel('catalog/product')->getCollection()
								->addFieldToFilter('name',array('like'=>$query))
								->addAttributeToSelect(array('sku','name','small_image','is_salable','image','thumbnail'))
								->addAttributeToFilter('visibility',array('in'=>$visibility))
								->addAttributeToFilter('type_id',array('in'=>$types))
								->addAttributeToFilter('product_type',array('nin'=>$proType))
								->addAttributeToSort($sort_column)
								->addAttributeToFilter('status',1)								
								->setPageSize($limit);

		if($collection->count()>0){
			$isCollection = true; 
		}else{
			$collection = Mage::getModel('catalog/product')->getCollection()
								->addFieldToFilter('sku',array('like'=>$query))
								->addAttributeToSelect(array('name','sku','small_image','image','is_salable','thumbnail'))
								->addAttributeToFilter('visibility',array('in'=>$visibility))
								->addAttributeToFilter('type_id',array('in'=>$types))
								->addAttributeToFilter('product_type',array('nin'=>$proType))
								->addAttributeToFilter('status',1)
								->addAttributeToSort($sort_column)
								->setPageSize($limit);	
			$isSku = true;					
			$isCollection = true;								
		}
		if($isCollection){ 

		//Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		//Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
			
			foreach($collection as $_product){
					
					if(!$this->isProductAllowed($_product)){ continue; }
					$imageUrl = '';
					if($_product->getVisibility() == 1){
							$grouped_product_model = Mage::getModel('catalog/product_type_grouped');
							$groupedParentId = $grouped_product_model->getParentIdsByChild($_product->getId()); 
							if(($count = count($groupedParentId)) >0){  
								$grouped = Mage::getModel('catalog/product')->load($groupedParentId[$count-1]);
							  	$imageUrl = $this->getImageUrl($grouped);
							} 
						}else{  
							$imageUrl = $this->getImageUrl($_product);
						}
					$json = array();
					$json['value'] = $_product->getSku();
					$json['name'] = $_product->getName();
					$json['image'] = $imageUrl;
					$json['is_sku'] = $isSku;
					$data[] =  $json;
			}	
		}
	
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
		
	}
	
	public function getImageUrl($_product){
		$product = $_product->getData();
		if($product['thumbnail']!=''){
			$image = $product['thumbnail'];
			$attr = 'thumbnail';
		}elseif($product['small_image']!=''){
			$image = $product['small_image'];
			$attr = 'small_image';
		}else{
			$image = $product['image'];
			$attr = 'image';			
		}
		  
		$url = (string)$_product->getMediaConfig()->getMediaUrl($image);
		if(file_exists($url)){
			$imageUrl =	(string)Mage::helper('catalog/image')->init($_product, $attr)->resize(85,95);
		}else{
			$imageUrl = (string)Mage::helper('catalog/image')->init($_product, $attr);
		}
		return $imageUrl;
		
	}
	
	
	public function loadproductAction(){
		$sku = $this->getRequest()->getParam('sku');
		
		$sku = mysql_escape_string(trim($sku));		
		if($sku==''){ return; }
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		$data = '';
		
		if($product){
			$data = Mage::helper('quickorder/protected')->loadProduct($product);
		}	
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
	}
	
	 protected function _getConfigurableHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('configurable_product_details');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	
	
	protected function _initProduct($sku){
	
        if ($sku) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId());
			$product->load($product->getIdBySku($sku));
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
	}
	
	
	 /**
     * Add product to shopping cart action
     */
    public function addcartAction()
    {
        $products = '';
        $params = $this->getRequest()->getParams();
        
		$products = isset($params['product'])?$params['product']:'';
		
		$json = array();
		$c = 0;
		foreach($params as $k=>$v){
			if(strstr($k,'as_values_')){
				if(!empty($v)){ $c++;} 
			}
		}
		
		if($c==0){
			$json['success'] = false;
			$json['message'] = $this->__('There are no valid products to add in cart.');	
		}elseif(!empty($products)){
			$products = array_filter($products,array($this,'filterProducts'));
			if(count($products)>0){
				if($this->addProduct($products)){
					$json['success'] = true;
					$json['message'] = $this->__('Items added to cart successfully.');
					$json['redirect_url'] = Mage::helper('checkout/cart')->getCartUrl();
				}
			}else{
				$json['success'] = false;
				$json['message'] = $this->__('There are no products to add in cart.');	 
			}	
		}else{
		  $json['success'] = false;
		  $json['message'] = $this->__('There are no products to add in cart.');	 
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
          
       
    }
	protected function addProduct($products){ 
		$cart = Mage::getSingleton('checkout/cart');
	 	try {
			
			foreach($products as $params){ 
				if (isset($params['qty'])) {
					$params['qty'] = (int)$params['qty'];
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}
				$product = $this->_initProduct($params['sku']);
				if (!$product) {
					return;
				}
				$params['product'] = $product->getId(); 
				$parentId =  $product->getId(); 
				/*****START:: added condition  by krish developer to add associated products as a group product****/
				if($product->getVisibility() == 1){
					$grouped_product_model = Mage::getModel('catalog/product_type_grouped');
					$groupedParentId = $grouped_product_model->getParentIdsByChild($product->getId()); 
					if(($count = count($groupedParentId)) >0){  
						$parentId =$groupedParentId[$count-1];
						//$params['product'] =$groupedParentId[0];   
					//	$params['super_group'][$product->getId()] = $params['qty'];     
						//$product = Mage::getModel('catalog/product')->load($groupedParentId[0]); 
						
					}
				}
 
				
				/*********END********/
			/*****START:: added condition  by krish developer to add related product to cart bundle product****/
			 
		     $product1 = Mage::getModel('catalog/product')->load($params['product']);
		   
		     $is_breakout = $product1->getData('is_breakout_bundles'); 
		
			if(isset($is_breakout) && $is_breakout==1)
			{	 
					$upsellProducts = $product1->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
					 
					if (count($upsellProducts)>0){
						
					    	 
						
							foreach($upsellProducts as $_rel)
							{
								$_group_parameters = array();
								$_thisProduct = Mage::getModel('catalog/product')->load($_rel->getId());
								
								if ($_thisProduct->getTypeId() == 'grouped'){
									
									$associatedProducts = $_thisProduct->getTypeInstance(true)->getAssociatedProducts($_thisProduct);
									$_group_parameters['product'] = $_rel->getId();
									$_group_parameters['related_product'] = '';
									$_group_parameters['super_group'] = $_rel->getId();
									$_associate = array();
									
									if(count($associatedProducts)>0)
									{
										foreach($associatedProducts as $associatedProduct)
										{								
											$_associate[$associatedProduct->getId()] = $params['qty'];
										}
										$_group_parameters['super_group'] = $_associate;	
										$_group_parameters['parent_bundle_id'] = $parentId;   
 
										$cart->addProduct($_thisProduct, $_group_parameters);
									}
								}
								else
								{   
									$_group_parameters['parent_bundle_id'] = $parentId;
									$_group_parameters['qty'] =$params['qty'];  	
									$cart->addProduct($_thisProduct,$_group_parameters);     
								}
						
							}
					
						
					}
					else
					{				
						$cart->addProduct($product1, $params);    
					}
			}
			else
			{			
					$related = '';
					$cart->addProduct($product, $params);    
			}			
				/*********END********/
				unset($params['product']);
			}
			 $cart->save();
	         $this->_getCheckout()->setCartWasUpdated(true);

            if (!$this->_getCheckout()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                  //  $this->_getCheckout()->addSuccess($message);
                }

				return true;
            }
        } catch (Mage_Core_Exception $e) {
			print_r($e);
		
			//  return false;
        } catch (Exception $e){
		}
	
	}
//	protected function addProduct($products){
//		$cart = Mage::getSingleton('checkout/cart');
//	 	try {
//			
//			foreach($products as $params){
//				if (isset($params['qty'])) {
//					$params['qty'] = (int)$params['qty'];
//					$filter = new Zend_Filter_LocalizedToNormalized(
//						array('locale' => Mage::app()->getLocale()->getLocaleCode())
//					);
//					$params['qty'] = $filter->filter($params['qty']);
//				}
//				$product = $this->_initProduct($params['sku']);
//				if (!$product) {
//					return;
//				}
//				$params['product'] = $product->getId();
//				/*****START:: added condition  by krish developer to add associated products as a group product****/
//				if($product->getVisibility() == 1){
//					$grouped_product_model = Mage::getModel('catalog/product_type_grouped');
//					$groupedParentId = $grouped_product_model->getParentIdsByChild($product->getId()); 
//					if(sizeof($groupedParentId) >0){
//						$params['product'] =$groupedParentId[0];   
//						$params['super_group'][$product->getId()] = $params['qty'];    
//						$product = Mage::getModel('catalog/product')->load($groupedParentId[0]); 
//					}
//				}   
//				/*********END********/
//			/*****START:: added condition  by krish developer to add related product to cart bundle product****/
//			$is_breakout = $product->getData('is_breakout_bundles');
//			
//			if(isset($is_breakout) && $is_breakout==1)
//			{	
//					$upsellProducts = $product->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
//					
//					if (count($upsellProducts)>0){
//						
//						 $_group_parameters = array();
//						
//							foreach($upsellProducts as $_rel)
//							{
//								$_thisProduct = Mage::getModel('catalog/product')->load($_rel->getId());
//								
//								if ($_thisProduct->getTypeId() == 'grouped'){
//									
//									$associatedProducts = $_thisProduct->getTypeInstance(true)->getAssociatedProducts($_thisProduct);
//									$_group_parameters['product'] = $_rel->getId();
//									$_group_parameters['related_product'] = '';
//									$_group_parameters['super_group'] = $_rel->getId();
//									$_associate = array();
//									
//									if(count($associatedProducts)>0)
//									{
//										foreach($associatedProducts as $associatedProduct)
//										{								
//											$_associate[$associatedProduct->getId()] = $params['qty'];
//										}
//										$_group_parameters['super_group'] = $_associate;	
//										
//										$cart->addProduct($_thisProduct, $_group_parameters);
//									}
//								}
//								else
//								{			
//									$cart->addProduct($_thisProduct, array('qty' => $params['qty']));    
//								}
//						
//							}
//					
//						
//					}
//					else
//					{				
//						$cart->addProduct($product, $params);    
//					}
//			}
//			else
//			{			
//					$related = '';
//					$cart->addProduct($product, $params);    
//			}			
//				/*********END********/
//				//$cart->addProduct($product, $params);
//				unset($params['product']);
//			}
//			 $cart->save();
//	         $this->_getCheckout()->setCartWasUpdated(true);
//
//            if (!$this->_getCheckout()->getNoCartRedirect(true)) {
//                if (!$cart->getQuote()->getHasError()){
//                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
//                    $this->_getCheckout()->addSuccess($message);
//                }
//
//				return true;
//            }
//        } catch (Mage_Core_Exception $e) {
//			//print_r($e);
//		
//			   return false;
//        } catch (Exception $e){
//		}
//	
//	}
	
	
	
	protected function filterProducts($value){
		if($value['sku']!=''){
			return $value;
		}
	}

	protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	
	
	protected function _getSession(){
		return Mage::getSingleton('core/session');
	} 

	
	protected function getConfig($key){
		if($key!=''){
			return Mage::getStoreConfig('quickorder/settings/'.$key);
		}
	}
	
	protected function isProductAllowed($product){
		return Mage::helper('quickorder/protected')->isProductAllowed($product);
	}
	

}

