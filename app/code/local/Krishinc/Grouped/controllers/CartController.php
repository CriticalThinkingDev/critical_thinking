<?php
require_once('Mage/Checkout/controllers/CartController.php');

class Krishinc_Grouped_CartController extends Mage_Checkout_CartController
{
    /********* Implemented To resolve ERROR of AMASTY - XCOUPON cancellation Issue *******/
    public function couponPostAction()
    {  
        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        
        if ($this->getRequest()->getParam('remove') == 1) { 
            $couponCode = '';
            Mage::getSingleton('customer/session')->setCoupon($couponCode);
        }        
        
        if($oldCouponCode = $this->_getQuote()->getCouponCode()) {
             if ($couponCode!=$oldCouponCode) {
                Mage::getSingleton('customer/session')->setCoupon($couponCode);
            }
        }
        parent::couponPostAction();        
    }
    /********* Implemented To resolve ERROR of AMASTY - XCOUPON cancellation Issue *******/
    
//  	public function addAction()
//    {
//        $cart   = $this->_getCart();
//	    $params = $this->getRequest()->getParams();
//		/*Add Product Manualy*/
//		if(isset($_POST["series_qty"]))
//		{
//			$seriesproduct = $_POST["series_qty"];		
//			$pModel = Mage::getSingleton('catalog/product');
//			$i=0;
//			$flag=0;
//			print_r($seriesproduct);
//			foreach($seriesproduct as $key => $value)
//			{
//			 if($value > 0)
//			 {
//				$flag=1;
//				$pModel= $this->customInitProduct($key);
//				$pModel->load($key);
//				$cart->addProduct($pModel, array('qty' => $value));
//			 }
//			}
//			$cart->save(); 		
//		}
//		
//        try {
//            if (isset($params['qty'])) {
//                $filter = new Zend_Filter_LocalizedToNormalized(
//                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
//                );
//                $params['qty'] = $filter->filter($params['qty']);
//            }
//	        $product = $this->_initProduct();
//            $related = $this->getRequest()->getParam('related_product');
//	        /**
//             * Check product availability
//             */
//            if (!$product) {
//                $this->_goBack();
//                return;
//            }
//	        $cart->addProduct($product, $params);
//            if (!empty($related)) {
//                $cart->addProductsByIds(explode(',', $related));
//            }
//
//            $cart->save();
//
//            $this->_getSession()->setCartWasUpdated(true);
//	        /**
//             * @todo remove wishlist observer processAddToCart
//             */
//            Mage::dispatchEvent('checkout_cart_add_product_complete',
//                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
//            );
//	     if (!$this->_getSession()->getNoCartRedirect(true)) {
//                if (!$cart->getQuote()->getHasError()){
//                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
//                    $this->_getSession()->addSuccess($message);
//                }
//                $this->_goBack();
//            }
//        } catch (Mage_Core_Exception $e) {
//            if ($this->_getSession()->getUseNotice(true)) {
//				if($flag == 1)
//				{
//                	$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
//                    $this->_getSession()->addSuccess($message);
//				}else
//				{
//					$this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
//				}
//            } else {
//                $messages = array_unique(explode("\n", $e->getMessage()));
//                foreach ($messages as $message) {
//                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
//                }
//            }
//
//            $url = $this->_getSession()->getRedirectUrl(true);
//            if ($url) {
//                $this->getResponse()->setRedirect($url);
//            } else {
//                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
//            }
//        } catch (Exception $e) {
//            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
//            Mage::logException($e);
//            $this->_goBack();
//        }
//    }
	
//	protected function customInitProduct($productId)
//    {
//        //$productId = (int) $this->getRequest()->getParam('product');
//        if ($productId) {
//            $product = Mage::getModel('catalog/product')
//                ->setStoreId(Mage::app()->getStore()->getId())
//                ->load($productId);
//            if ($product->getId()) {
//                return $product;
//            }
//        }
//        return false;
//    }

	
}