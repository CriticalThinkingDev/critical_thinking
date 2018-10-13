<?php
class Krishinc_Ajaxtocart_Model_Cart extends Mage_Checkout_Model_Cart 
{
	  /**
     * Adding products to cart by ids
     *
     * @param   array $productIds
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProductsByIds($productIds,$qty = 1)
    {  
        $allAvailable = true;
        $allAdded     = true;
 		$productName = '';
        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $productId = (int) $productId;
                if (!$productId) {
                    continue;
                }
                $product = $this->_getProduct($productId);
               // $params = array();
                if ($product->getId() && $product->isVisibleInCatalog()) {
                    try {
                    	//$params['qty'] = $qty;
                      //  $this->getQuote()->addProduct($product,$qty);   
                        $this->addProduct($product,$qty);    
                    } catch (Exception $e){
                    	$productName .= ', '.$product->getName();
                        $allAdded = false;
                    } 
                } else {
                	$productName .= ', '.$product->getName();
                    $allAvailable = false;
                }
            }

            if (!$allAvailable) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('%s of the requested products are unavailable.',ltrim($productName,','))
                );
            }
            if (!$allAdded) {
                $this->getCheckoutSession()->addError(
                    Mage::helper('checkout')->__('%s of the requested products are not available in the desired quantity.')
                );
            }
        }
        return $this;
    }
    
        /**
     * Add product to shopping cart (quote)
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed $requestInfo
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo=null)
    {
    	 
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);
 
        $productId = $product->getId();

        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                && !$this->getQuote()->hasProductId($productId)
            ){
                $request->setQty($minimumQty);
            }
        }

        if ($productId) {
            try {
            	if(isset($requestInfo['parent_bundle_id']) && ($requestInfo['parent_bundle_id']>0)) {
            		$request->setParentBundleId($requestInfo['parent_bundle_id']);
            	}
                $result = $this->getQuote()->addProduct($product, $request);
            } catch (Mage_Core_Exception $e) {
                $this->getCheckoutSession()->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                $redirectUrl = ($product->hasOptionsValidationFail())
                    ? $product->getUrlModel()->getUrl(
                        $product,
                        array('_query' => array('startcustomization' => 1))
                    )
                    : $product->getProductUrl();
                $this->getCheckoutSession()->setRedirectUrl($redirectUrl);
                if ($this->getCheckoutSession()->getUseNotice() === null) {
                    $this->getCheckoutSession()->setUseNotice(true);
                }
                Mage::throwException($result);
            }
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }

        Mage::dispatchEvent('checkout_cart_product_add_after', array('quote_item' => $result, 'product' => $product));
        $this->getCheckoutSession()->setLastAddedProductId($productId);
        return $this;
    }

}