<?php
class Krishinc_Sourcecode_Model_Observer
{
	
	const ORDER_ATTRIBUTE_FHC_ID = 'source_code';
		
    /**
     * Event Hook: checkout_type_onepage_save_order
     * 
     * @author Branko Ajzele
     * @param $observer Varien_Event_Observer
     */
	public function hookToOrderSaveEvent()
	{
		//check in community/dh/shipnote module observer file. added code to save source code in order object.
		/**
		* NOTE:
		* Order has already been saved, now we simply add some stuff to it,
		* that will be saved to database. We add the stuff to Order object property
		* called "sourecode"
		*/
		$order = new Mage_Sales_Model_Order();
		$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order->loadByIncrementId($incrementId);
		
		//Fetch the data from select box and throw it here
		$_sourcecode_data = null;
		$_sourcecode_data = Mage::getSingleton('core/session')->getKrishincSourcecode();
		
		//Save fhc id to order obcject
		$order->setData(self::ORDER_ATTRIBUTE_FHC_ID, $_sourcecode_data);
		$order->save();
	}



}