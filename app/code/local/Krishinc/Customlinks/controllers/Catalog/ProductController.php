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

include("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Krishinc_Customlinks_Catalog_ProductController extends  Mage_Adminhtml_Catalog_ProductController
{
	/**
     * Get componentsold products grid and serializer block
     */
    public function componentsoldAction()
    {
    	 
    	//echo 'this is in';exit;
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.componentsold')
        				  ->setProductsComponentsold($this->getRequest()->getPost('products_componentsold', null)); 
        $this->renderLayout(); 
    }    
 
     /**
     * Get Componentsold products grid
     */
    public function componentsoldGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.componentsold')
            ->setProductsRelated($this->getRequest()->getPost('products_componentsold', null));
        $this->renderLayout(); 
    } 
    
    
    /**
     * Get bundlecontent products grid and serializer block
     */
    public function bundlecontentAction()
    {
    	 
    	//echo 'this is in';exit;
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.bundlecontent')
        				  ->setProductsBundlecontent($this->getRequest()->getPost('products_bundlecontent', null)); 
        $this->renderLayout(); 
    }    
 
     /**
     * Get Bundlecontent products grid
     */
    public function bundlecontentGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.bundlecontent')
            ->setProductsRelated($this->getRequest()->getPost('products_bundlecontent', null));
        $this->renderLayout(); 
    } 
    
    
    /**
     * Get otherformat products grid and serializer block
     */
    public function otherformatAction()
    {
    	 
    	//echo 'this is in';exit;
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.otherformat')
        				  ->setProductsOtherformat($this->getRequest()->getPost('products_otherformat', null)); 
        $this->renderLayout(); 
    }    
 
     /**
     * Get Otherformat products grid
     */
    public function otherformatGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.otherformat')
            ->setProductsRelated($this->getRequest()->getPost('products_otherformat', null));
        $this->renderLayout(); 
    } 

}