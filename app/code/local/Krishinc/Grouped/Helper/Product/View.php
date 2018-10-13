<?php

class Krishinc_Grouped_Helper_Product_View extends Mage_Catalog_Helper_Product_View
{
	/**
     * Prepares product view page - inits layout and all needed stuff
     *
     * $params can have all values as $params in Mage_Catalog_Helper_Product - initProduct().
     * Plus following keys:
     *   - 'buy_request' - Varien_Object holding buyRequest to configure product
     *   - 'specify_options' - boolean, whether to show 'Specify options' message
     *   - 'configure_mode' - boolean, whether we're in Configure-mode to edit product configuration
     *
     * @param int $productId
     * @param Mage_Core_Controller_Front_Action $controller
     * @param null|Varien_Object $params
     *
     * @return Mage_Catalog_Helper_Product_View
     */
    public function prepareAndRender($productId, $controller, $params = null)
    {
        // Prepare data
        $productHelper = Mage::helper('catalog/product');
        if (!$params) {
            $params = new Varien_Object();
        }

        // Standard algorithm to prepare and rendern product view page
        $product = $productHelper->initProduct($productId, $controller, $params);
        if (!$product) {
            throw new Mage_Core_Exception($this->__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));

        if ($params->getSpecifyOptions()) {
            $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
            Mage::getSingleton('catalog/session')->addNotice($notice);
        }

        Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());

        $this->initProductLayout($product, $controller);
		/**Added to change series product layout**/
        if($product->getSeries()):
	        $layout       = $controller->getLayout();
			$product_info = $layout->getBlock('product.info');
			$product_info->setTemplate('catalog/product/series.phtml');
			$product_info_grouped = $layout->getBlock('product.info.grouped');
			$product_info_grouped->setTemplate('catalog/product/view/type/grouped_series.phtml');
//	elseif($product->getIsMasterGroupProduct()):
	    else:
		
		$master_product = array();
		if($product->getIsMasterGroupProduct()) {
			$master_product = $product;
		} else {
			$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
			
			if(count($parentIds) > 1) {
				foreach ($parentIds as $parentId) {
					$parent = Mage::getModel('catalog/product')->load($parentId);
					if($parent->getIsMasterGroupProduct()) {
						$master_product = $parent;
					}
				}
			} else {
				if(isset($parentIds[0])){
					$parent = Mage::getModel('catalog/product')->load($parentIds[0]);
					if($parent->getIsMasterGroupProduct()) {
						$master_product = $parent;
					}
				}
				
			}
			
			if(empty($master_product)) {
				$layout       = $controller->getLayout();
				$series_block = $layout->createBlock(
					'grouped/product_view_seriesproducts',
					'product.associated.series.view',
					array('template' => 'catalog/product/view/seriesproducts_mastergroup.phtml')
				);
				$series_block->setBlockAlias('seriesproducts_view');
				$layout->getBlock('content')->append($series_block);
			}
			
		}
		
		
		if(!empty($master_product)) :

			Mage::register('parent_product',$master_product);
			
			$layout       = $controller->getLayout();
			$product_info = $layout->getBlock('product.info');
			$product_info->setTemplate('catalog/product/mastergroup.phtml');
			
			if($product->getIsMasterGroupProduct()) {
				$product_info_grouped = $layout->getBlock('product.info.grouped');
				$product_info_grouped->setTemplate('catalog/product/view/type/grouped_mastergroup.phtml');
			}
			
			$series_block = $layout->createBlock(
				'grouped/product_view_seriesproducts',
				'product.associated.series',
				array('template' => 'catalog/product/view/seriesproducts_mastergroup.phtml')
			);
			$series_block->setBlockAlias('seriesproducts');
			$layout->getBlock('content')->append($series_block);
		endif;
	    
	endif;
	/**END**/

        $controller->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'))
            ->renderLayout();
 
        return $this;
    }
}
