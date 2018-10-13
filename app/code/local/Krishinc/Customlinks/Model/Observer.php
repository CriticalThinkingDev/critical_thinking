<?php
/**
 * created : 11/20/12
 * 
 * @category Krishinc
 * @package Krishinc_Customlinks
 * @author Bijal Bhavsar
 * @copyright Krishinc - 2012 - http://www.krishinc.com
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Created observer to save component sold products 
 * Used to add Component Sold products in product 
 * @package Krishinc_Customlinks
 */
class Krishinc_Customlinks_Model_Observer extends Mage_Catalog_Model_Product {
	/**
	 * call save reverse links methods when product is saved 
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function saveReverseLinks(Varien_Event_Observer $observer) {
		
		$product = $observer->getEvent()->getProduct();
		
		$links = Mage::app()->getRequest()->getPost('links');
	    ///Added for custom componentsold listing
        if (isset($links['componentsold']) && !$product->getComponentsoldReadonly()) {
            $product->setComponentsoldLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['componentsold']));
        }
        
           ///Added for custom bundlecontent listing
        if (isset($links['bundlecontent']) && !$product->getBundlecontentReadonly()) {
            $product->setBundlecontentLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['bundlecontent']));
        }   
           ///Added for custom otherformat listing
        if (isset($links['otherformat']) && !$product->getOtherformatReadonly()) {
            $product->setOtherformatLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['otherformat']));
        }
	}
	
	 
}
?>