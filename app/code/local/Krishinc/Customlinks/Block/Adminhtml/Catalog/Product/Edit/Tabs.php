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
 * Override of product edit tabs block
 * Used to add Component Sold tab in product 
 * @package Krishinc_Customlinks
 */
class Krishinc_Customlinks_Block_Adminhtml_Catalog_Product_Edit_Tabs extends  Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	 private $parent;
 
    protected function _prepareLayout()
    {//echo 'this ex';exit; 
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
	        $this->addTabAfter('componentsold', array(
	                'label'     => Mage::helper('catalog')->__('Component Sold'),
	                'url'       => $this->getUrl('*/*/componentsold', array('_current' => true)),
	                'class'     => 'ajax',
	            ),'upsell');   
	
	            //New tab for bundlecontent
//	        $this->addTabAfter('bundlecontent', array(
//	                'label'     => Mage::helper('catalog')->__('Bundle Content'),
//	                'url'       => $this->getUrl('*/*/bundlecontent', array('_current' => true)),
//	                'class'     => 'ajax',
//	            ),'upsell');      
//	            //New tab for otherformat
//	        $this->addTabAfter('otherformat', array(
//	                'label'     => Mage::helper('catalog')->__('Other Available Format'),
//	                'url'       => $this->getUrl('*/*/otherformat', array('_current' => true)),
//	                'class'     => 'ajax',
//	            ),'upsell'); 
	            
	        $this->setTabData('related','after','upsell');
//	        $this->setTabData('related','title','Recommended at Detail Page');
//	        $this->setTabData('related','label','Recommended at Detail Page');  
//	        $this->setTabData('crosssell','title','Recommended at Cart Page');
//	        $this->setTabData('crosssell','label','Recommended at Cart Page'); 
	        return $this->parent;
        }
        
    }
	
}