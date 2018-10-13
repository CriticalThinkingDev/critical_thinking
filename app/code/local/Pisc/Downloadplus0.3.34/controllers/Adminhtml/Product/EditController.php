<?php
/**
 * Downloadable Admin Product Edit Details controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'Catalog'.DS.'ProductController.php';

class Pisc_Downloadplus_Adminhtml_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{

    /**
     * Load downloadable detail tab fieldsets
     */
    public function downloadDetailAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail', 'admin.product.downloadplus.detail')
                ->toHtml()
        );
    }

    /**
     * Load downloadable detail tab fieldsets
     */
    public function additionalDownloadsAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_downloads', 'admin.customer.downloadplus.product.downloads')
                ->toHtml()
        );
    }

    /**
     * Load serialnumber tab fieldsets
     */
    public function serialnumbersAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_serialnumbers', 'admin.customer.downloadplus.product.serialnumbers')
                ->toHtml()
        );
    }

    /**
     * Load serialnumber tab fieldsets
     */
    public function serialnumbersAvailableAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_serialnumbers_available', 'admin.customer.downloadplus.product.serialnumbers.available')
                ->toHtml()
        );
    }

    /**
     * Load settings tab fieldsets
     */
    public function settingsAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_settings', 'admin.customer.downloadplus.product.settings')
                ->toHtml()
        );
    }

    /**
     * Load "Other Available Links"
     */
    public function viewAvailableLinksAction()
    {
    	$this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablelinks', 'admin.product.downloadplus.detail.availablelinks')
                ->toHtml()
        );
    }

    /**
     * Load "Other Available Linksamples"
     */
    public function viewAvailableLinksamplesAction()
    {
    	$this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablelinksamples', 'admin.product.downloadplus.detail.availablelinksamples')
                ->toHtml()
        );
    }

    /**
     * Load "Other Available Samples"
     */
    public function viewAvailableSamplesAction()
    {
    	$this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_catalog_product_edit_tab_detail_availablesamples', 'admin.product.downloadplus.detail.availablesamples')
                ->toHtml()
        );
    }

}