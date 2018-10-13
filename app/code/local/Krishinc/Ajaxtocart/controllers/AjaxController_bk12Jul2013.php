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
            
            if(isset($params['series_qty'])){
            	$params['series_qty'] = array_filter($params['series_qty']);
            }
            $product = Mage::getModel('catalog/product')->load($params['product']);//$this->_initProduct(); 
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
            	 $result['error']= true;
            	 $result['msg']= 'No product available';
                 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            } 
		 
			$is_breakout = $product->getData('is_breakout_bundles');
			
			if(isset($is_breakout) && $is_breakout==1)
			{
	            if (!empty($related)) {  
	            
	            	$qty = $params['qty']?$params['qty']:1; 
	            	/* if($qty > 1) {
	                	$cart->addProductsByIds(explode(',', $related),$qty);  
	            	}else { */
						
						$_group_parameters = array();
						
						$_related_products = explode(',', $related);
						if(count($_related_products)>0)
						{
							foreach($_related_products as $_rel)
							{
								$_thisProduct = Mage::getModel('catalog/product')->load($_rel); 
								
								if ($_thisProduct->getTypeId() == 'grouped'){
									
									$associatedProducts = $_thisProduct->getTypeInstance(true)->getAssociatedProducts($_thisProduct);
									$_group_parameters['product'] = $_rel;
									$_group_parameters['related_product'] = '';
									$_group_parameters['super_group'] = $_rel;
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
									$cart->addProduct($_thisProduct, array('qty' => $qty));    
								}
						
							}
						}
						
	            	/* } */
	            }
				elseif(isset($params['series_related_products']))
				{ 
	            	foreach ($params['series_qty'] as $key => $val)
	            	{	
	            		if(isset($params['series_related_products'][$key]) && $params['series_related_products'][$key]!='' && is_array($val))
	            		{ 
							if($params['grouped_items'] && $params['grouped_items']!='')
							{
								$qty = $val[$params['grouped_items']]; 
							}
							else
							{
								$qty = $val?$val:1; 
							}
							if($qty!=0)
							{
								$relateditems = explode(',', $params['series_related_products'][$key]);
								$relateditems = array_filter($relateditems);   	            		
								$_group_parameters = array();
							
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
											$cart->addProduct($_thisProduct, array('qty' => $qty));    
										}
								
									}
								}
							}
	            		} 
						else if(!is_array($val) && isset($params['series_related_products'][$key]) && $params['series_related_products'][$key]!='')
						{ 
							
							$qty = $params['series_qty'][$key];
							
							if($qty>0)
							{
								
								$relatedItems = explode(',',$params['series_related_products'][$key]);
								
								$_group_parameters = array();
								$_associate = array();
								
								$_group_parameters['product'] = $key;
								$_group_parameters['related_product'] = '';
								$_group_parameters['super_group'] = $_rel;							
								
								foreach($relatedItems as $prod)
								{
									if(isset($prod) && $prod!='')
									{
										$_thisProduct = Mage::getModel('catalog/product')->load($prod); 						
										$_associate[$prod] = $qty;
										
										$_group_parameters['super_group'] = $_associate;	
										$cart->addProduct($_thisProduct, $_group_parameters);
								
									}	
									
								}
								
								
							}
						}
						else if(is_array($val) && !isset($params['series_related_products'][$key]))
						{ 
							foreach($val as $prod => $pqty)
							{
								$qty = $pqty; 
								$_associate = array();
								$_group_parameters = array();
								if($qty>0)
								{ 
									$_thisProduct = Mage::getModel('catalog/product')->load($key); 
									
									$_associate[$prod] = $qty;	
									$_group_parameters['product'] = $key;								
									$_group_parameters['super_group'] =  $_associate;
									$_group_parameters['related_product'] = '';													
									$cart->addProduct($_thisProduct, $_group_parameters);
								}
							}
							
						}
						else
						{ 
							$qty = $val; 
							if($qty>0)
							{ 
								$_thisProduct = Mage::getModel('catalog/product')->load($key); 
								$cart->addProduct($_thisProduct, array('qty' => $qty));   
							}
									
						}
	            	}
					
				}
				else
				{
					$cart->addProduct($product, $params);
				}
			}	
			else
			{
				$related = '';
				$cart->addProduct($product, $params);
			}
			
			
			
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
			/* update add to cart for bundle product */
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
	
	
    
	public function getInfoAction()
	{
		$lastAddedItem =  $this->_getSession()->getLastAddedProductId();
		$lastAddedItemQty =  $this->_getSession()->getLastAddedProductQtyAdded();
		
		$product_id = $this->getRequest()->getParam('form_productid');
		$product = Mage::getModel('catalog/product')->load($product_id);
		$is_breakout = $product->getData('is_breakout_bundles');
		
		
		if(isset($is_breakout) && $is_breakout==1 && $product->getProductType() == 125 )
		$result['product_message'] = $this->__('You have added all bundle items into your shopping cart.');  
		
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
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));  
		 
	}
}