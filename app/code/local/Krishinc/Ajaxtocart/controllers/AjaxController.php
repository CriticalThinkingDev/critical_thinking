<?php

require_once(getcwd().'/app/code/core/Mage/Checkout/controllers/CartController.php');
class Krishinc_Ajaxtocart_AjaxController extends Mage_Checkout_CartController
{
	
	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
	public function addAction()
	{
		$cart   = $this->_getCart();
	    $params = $this->getRequest()->getParams(); 
	    try { 
		/* update add to cart for bundle product */
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            
			if(isset($params['series_qty'])) {
            	$params['series_qty'] = array_filter($params['series_qty']);
			}
            $product = '';
            if(isset($params['product'])) {
                $product = Mage::getModel('catalog/product')->load($params['product']);//$this->_initProduct();
            }

            $related = $this->getRequest()->getParam('related_product');
			
			/* message flag if no quantity added */
			$flag = 0;
			
            /**
             * Check product availability
             */
            if (!$product) {
            	 $result['error']= true;
            	 $result['msg']= 'No product available';
                 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            } 
		 
            $params['qty'] = (isset($params['qty']))?$params['qty']:1;
            $is_breakout_main = $product->getData('is_breakout_bundles');
			$qty = $params['qty'];			
			/* add the product if default related is set and breakout is enable for current product */
			
			if(isset($params['series_qty']))
			{				
				foreach ($params['series_qty'] as $key => $val)
				{	
					if(is_array($val))
					{
						foreach($val as $vkey=>$vval)
						{
							$subprod = $vkey;
							$qty = $val[$vkey]; 
						}
						
					}
					else
					{
						$subprod = $key;
						$qty = $val; 
					}
					
					if($qty>0)
					{
						$flag = 1;
						
						$_mainthisProduct = Mage::getModel('catalog/product')->load($key); 
						
						if($_mainthisProduct->getTypeId() == 'grouped')
						{
							$_selectedProduct = Mage::getModel('catalog/product')->load($subprod);
							$is_breakoutProduct = $_selectedProduct->getData('is_breakout_bundles');
						
							if(isset($is_breakoutProduct) && $is_breakoutProduct==1)
							{ 
								$upsellProducts = $_selectedProduct->getUpSellProductCollection()
														->setPositionOrder()
														->addStoreFilter();
								$upscount = count($upsellProducts->getItems());									
								if(count($upscount)>0)
								{										
									foreach($upsellProducts as $associatedProduct)
									{	
										$_Product = Mage::getModel('catalog/product')->load($associatedProduct->getId());
										$cart->addProduct($_Product, array('qty' => $qty,'parent_bundle_id'=>$_mainthisProduct->getId()));
									} 
								}
							}
							else
							{	$_associate = array();
								$_group_parameters = array();
									
								$_associate[$subprod] = $qty;	
								$_group_parameters['product'] = $key;								
								$_group_parameters['super_group'] =  $_associate;
								$_group_parameters['related_product'] = '';	
								$cart->addProduct($_mainthisProduct, $_group_parameters);
							}
						}	
						else
						{
							if(isset($params['series_related_products'][$key]))
							{
								$relateditems = explode(',', $params['series_related_products'][$key]);
								$relateditems = array_filter($relateditems);   	            		
								$_group_parameters = array();
								
								$keybreak = $_mainthisProduct->getData('is_breakout_bundles');
							
								if(isset($keybreak) && $keybreak==1)
								{								
									if(count($relateditems)>0)
									{
										foreach($relateditems as $_rel)
										{
											$_thisProduct = Mage::getModel('catalog/product')->load($_rel); 
											
											if ($_thisProduct->getTypeId() == 'grouped'){
												
												$associatedProducts = $_thisProduct->getTypeInstance(true)->getAssociatedProducts($_thisProduct);
												$_group_parameters['product'] = $_rel;
												$_group_parameters['related_product'] = '';
												$_group_parameters['super_group'] = $_rel;
												$_group_parameters['parent_bundle_id'] = $_thisProduct->getId();
												
												$_associate = array();
												
												if(count($associatedProducts)>0)
												{
													foreach($associatedProducts as $associatedProduct)
													{								
														$_associate[$associatedProduct->getId()] = $qty;
													}
													$_group_parameters['super_group'] = $_associate;	
													
													$cart->addProduct($_thisProduct, $_group_parameters);
												}
											}
											else
											{	
											
												$_params = array();
												$_params['qty'] = $qty;	 
											    if($_mainthisProduct->getProductType() == '125') {
													$_params['parent_bundle_id'] = $_mainthisProduct->getId();	
												}
												$cart->addProduct($_thisProduct, $_params);     
											}
									
										}
									}
								}
								else
								{
									$cart->addProduct($_mainthisProduct, array('qty' => $qty));
								}
							}
							else
							{			
								$cart->addProduct($_mainthisProduct, array('qty' => $qty));    
							}
						}
							
					}

				}
			}
			else
			{
				if($product->getTypeId() == 'grouped')
				{
					$superdata = $params['super_group'];
					if(count($superdata)>0)
					{
						foreach($superdata as $skey=>$sval)
						{
							if($sval>0)
							{
								$flag = 1;
								$qty = $sval;
								
								$_Product = Mage::getModel('catalog/product')->load($skey);
								$is_breakoutProduct = $_Product->getData('is_breakout_bundles');
								
								if(isset($is_breakoutProduct) && $is_breakoutProduct==1)
								{
									$upsellProducts = $_Product->getUpSellProductCollection()
														->setPositionOrder()
														->addStoreFilter();
									$upscount = count($upsellProducts->getItems());									
									if(count($upscount)>0)
									{										
										foreach($upsellProducts as $associatedProduct)
										{	
											$_Product = Mage::getModel('catalog/product')->load($associatedProduct->getId());
											 
											$cart->addProduct($_Product, array('qty' => $qty,'parent_bundle_id'=>$product->getId()));
										} 
									}
								}
								else
								{
									$_group_parameters['product'] =  $params['product'];
									$_group_parameters['related_product'] = '';
									$_group_parameters['super_group'] = '';
									$_associate = array();
									
									$_associate[$skey] = $qty;
									$_group_parameters['super_group'] = $_associate;	
										
									$cart->addProduct($product, $_group_parameters);
								}
							}
						}
					}
				}
				else
				{		
					
					$flag = 1;
					
					if($product->getProductType() == 125)
					{
						if(isset($is_breakout_main) && $is_breakout_main==1)
						{
							if(isset($related) && $related!='')
							{ 
								$_related_products = explode(',', $related);						
								foreach($_related_products as $_rel)
								{
									$_thisProduct = Mage::getModel('catalog/product')->load($_rel);
									$cart->addProduct($_thisProduct, array('qty' => $qty,'parent_bundle_id'=>$product->getId()));							
								}
							}	
						}
						else
						{
							$cart->addProduct($product, array('qty' => $qty));    
						}
					}
					else
					{
						$cart->addProduct($product, array('qty' => $qty));    
					}	
				}
			}
			
			if($flag==0)
			Mage::throwException($this->__('Please specify the quantity of product(s).')); 
			
			
			$cart->save();
		
			$this->_getSession()->setLastAddedProductQtyAdded($params['qty']);  
            $this->_getSession()->setCartWasUpdated(true);
	
            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){				
					
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                }
                if (isset($redirectUrl)) {
		            $result['redirect'] = $redirectUrl;
		        }
                 $result['success']= true;
                 $result['msg']= $message;
                 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        } catch (Mage_Core_Exception $e) {
           if ($this->_getSession()->getUseNotice(true)) {
                //$this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
                 $result['msg']= $e->getMessage();
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                $resultMes = '';
                foreach ($messages as $message) {
                    //$this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                    $resultMes .=$message;
                }
                 $result['msg']= $resultMes;
            } 
        	$result['error']= true;
       
 		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

            
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $result['error']= true;
            $result['msg']= $e;
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
	}
	
	public function loadrecommendationAction(){
        $this->loadLayout();
        $recommendation = $this->getLayout()->createBlock('bestseller/bestseller')->setTemplate('catalog/product/recommended.phtml')->toHtml();
        $result['recommendation'] = $recommendation;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
	public function getInfoAction()
	{
		$lastAddedItem =  $this->_getSession()->getLastAddedProductId();
		$lastAddedItemQty =  $this->_getSession()->getLastAddedProductQtyAdded();
		
		$product_id = $this->getRequest()->getParam('form_productid');
		$product = Mage::getModel('catalog/product')->load($product_id);
		$is_breakout = $product->getData('is_breakout_bundles');
		$is_series = $product->getData('series');
		$is_master_grouped = $this->getRequest()->getParam('is_master_grouped');
		
		
		/* Added by Krish dev.(13 Nov) - check that product has component sold product and display msg */
		$component_sold = 0;
		if(!(isset($is_series) && $is_series == 1)) {
			$collection = $product->getComponentsoldProductCollection();
			if(count($collection) > 0) {
				$component_sold = 1;
			}
		}
		
		if((isset($is_breakout) && $is_breakout==1 ) || $product->getProductType() == 125 ) {
			$result['product_message'] = $this->__('You have added all bundle items into your shopping cart.');
			if($component_sold == 1) {
				$result['product_message'] .= '<span><br><br><font color="#CC3333"<strong>**Required and/or Optional Components sold separately for this product.**</strong></font><br>Please read the description carefully.</span>';
			}
		}
	 
		else if(isset($is_series) && $is_series == 1)
		{
			$result['product_message'] = $this->__('You have added all selected series items into your shopping cart.');
			$series_products = $this->getRequest()->getParam('series_qty');
			foreach($series_products as $productId => $qty) {
				if($qty != "" && $qty > 0) {
					$series_product_data = Mage::getModel('catalog/product')->load($productId);
					if($hascomponent = $series_product_data->getHasRequiredComponent()) {
						$result['product_message'] .= '<span><br><br><font color="#CC3333"<strong>**Required and/or Optional Components sold separately for this product.**</strong></font><br>Please read the description carefully.</span>';
						$component_sold = 1;
						break;
					}
				}
			}
		}
		elseif(isset($is_master_grouped) && $is_master_grouped == 1)
		{
			$result['product_message'] = '';
		}
		else
		{
			$product = Mage::getModel('catalog/product')->load($lastAddedItem);
			$result['product_message'] = ''; 
		}
		
		$this->loadLayout();    
        $sidebar = $this->getLayout()->getBlock('cart_sidebar');  
        $result['thumbnail'] = $product->getThumbnailUrl(80,80);
        $result['name'] = $product->getName();   
        $result['sidebar'] = $sidebar->toHtml();  
    	$result['count'] = $sidebar->getSummaryCount();
		$result['component_sold'] = $component_sold;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));  
		 
	}
	
	 /**
     * Initialize coupon
     */
//    public function couponPostAction()
//    {
//        /**
//         * No reason continue with empty shopping cart
//         */
//        if (!$this->_getCart()->getQuote()->getItemsCount()) {
//            $this->_goBack();
//            return;
//        }
//
//        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
//        if ($this->getRequest()->getParam('remove') == 1) {
//            $couponCode = '';
//            Mage::getSingleton('customer/session')->setCoupon($couponCode);
//        }
//        $oldCouponCode = $this->_getQuote()->getCouponCode();
//
//        if ($couponCode!=$oldCouponCode) {
//            Mage::getSingleton('customer/session')->setCoupon($couponCode);
//        }
//
//        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
//            $this->_goBack();
//            return;
//        }
//
//        try {
//        	/***START:: Added by bijal to restrict Flat/free Shipping related coupons for  virtual/downloadable products***/
//        	if($this->_getQuote()->isVirtual()) {
//	        	$ruleId = Mage::getModel('salesrule/coupon')->loadByCode($couponCode)->getRuleId();
//	        	$coupon = Mage::getModel('salesrule/rule')->load($ruleId);
//
//	        	if($coupon->getSimpleFreeShipping() > 0)
//	        	{
//	        		       $this->_getSession()->addError(
//	                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
//	                    );
//	        	}
//        	} else {/**ELSE**/
//	            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
//	            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
//	                ->collectTotals()
//	                ->save();
//
//	            if (strlen($couponCode)) { //echo $couponCode.'||'.$this->_getQuote()->getCouponCode();die;
//	                if ($couponCode == $this->_getQuote()->getCouponCode()) {
//	                    $this->_getSession()->addSuccess(
//	                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
//	                    );
//	                }
//	                else {
//	                    $this->_getSession()->addError(
//	                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
//	                    );
//	                }
//	            } else {
//	                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
//	            }
//        	}/**END**/
//
//        } catch (Mage_Core_Exception $e) {
//            $this->_getSession()->addError($e->getMessage());
//        } catch (Exception $e) {
//            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
//            Mage::logException($e);
//        }
//
//        $this->_goBack();
//    }
}
