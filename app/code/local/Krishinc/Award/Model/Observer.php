<?php

class Krishinc_Award_Model_Observer
{
	  /**
    * Force some values before "blfa_file" EAV attributes save
    * 
    * @param Varien_Event_Observer $observer
    */
    public function onAttributeSaveAfter(Varien_Event_Observer $observer)
    {
    	$attribute = $observer->getEvent()->getAttribute();    	 
        if (($attribute = $observer->getEvent()->getAttribute())
            && ($attribute->getAttributeCode() == 'award')) {   
		    		Mage::getModel('award/award')->updateOptionsToAwards($attribute->getOption()); 
        }
    }
}
    

?>