<?php
/**
 * Downloadable Admin Customer Edit controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml').DS.'CustomerController.php';

class Pisc_Downloadplus_Adminhtml_EditController extends Mage_Adminhtml_CustomerController
{

    /**
     * Load downloadable detail tab fieldsets
     */
    public function currentDownloadsAction()
    {
	
        $this->_initCustomer();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads', 'admin.customer.downloadplus.currentDownloads')
                ->toHtml()
        );
    }

    /**
     * Load downloadable detail accordion
     */
    public function viewPurchasedDownloadsAction()
    {
        $this->_initCustomer();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_customer_edit_view_downloads_purchasedlinks', 'admin.customer.view.downloadplus.currentDownloads')
                ->toHtml()
        );
    }

    /**
     * Load Serialnumber accordion
     */
    public function viewSerialnumbersAction()
    {
        $this->_initCustomer();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads_serialnumbers', 'admin.customer.view.downloadplus.currentSerialnumbers')
                ->toHtml()
        );
    }

    /**
     * Load Customer Additional Downloads accordion
     */
    public function viewAdditionalDownloadsAction()
    {
        $this->_initCustomer();
        $this->getResponse()->setBody(
            $this->getLayout()
            	->createBlock('downloadplus/adminhtml_customer_edit_tab_downloads_additional', 'admin.customer.view.downloadplus.currentCustomerDownloads')
                ->toHtml()
        );
    }

}