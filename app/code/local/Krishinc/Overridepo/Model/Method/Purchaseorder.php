<?php 

class Krishinc_Overridepo_Model_Method_Purchaseorder extends Mage_Payment_Model_Method_Purchaseorder
{
	
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Purchaseorder
     */
    public function assignData($data)
    {  
    	parent::assignData($data);  
    	$netterm = $data->getNetterm();
    	if(!$netterm) {
	    	 if ($this->getInfoInstance() instanceof Mage_Sales_Model_Order_Payment) {
	    	 	$netterm = $this->getInfoInstance()->getOrder()->getCustomerNetterm();
	    	 }else {
	    	 	$netterm = $this->getInfoInstance()->getQuote()->getCustomerNetterm();
	    	 }
    	}
        $this->getInfoInstance()->setNetterm($netterm);   
        $this->getInfoInstance()->setCreditLimit($data->getCreditLimit());  
       
        return $this;
    }
}