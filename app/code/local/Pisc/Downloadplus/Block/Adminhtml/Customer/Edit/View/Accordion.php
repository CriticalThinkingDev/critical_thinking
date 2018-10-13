<?php
/**
 * Customer Edit Downloads Tab Admin block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Block_Adminhtml_Customer_Edit_View_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    protected function _prepareLayout()
    {
        //$this->setId('customerViewDownloadsAccordion');
        //$this->updateAccordion($this);
    }

    public function updateAccordion($accordion)
    {
        $customer = Mage::registry('current_customer');

        $collectionCount = 0;
        if ($collection = Mage::getSingleton('downloadplus/customer_download')
    					->setCustomer($customer)
    					->getLinkPurchasedItemCollection()) {
    		$collectionCount = $collection->getSize();
		}

        $accordion->addItem('currentDownloads', array(
            'title'   		=> Mage::helper('downloadplus')->__('Purchased Downloadable Products - %d items', $collectionCount),
        	'ajax'			=> true,
            'content_url' 	=> $this->getUrl('adminhtml/downloadplus_customer_edit/viewPurchasedDownloads', array('_current' => true))
        ));
    }

}
