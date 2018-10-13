<?php 

class Krishinc_Overridepo_Model_Method_Checkmo extends Mage_Payment_Model_Method_Checkmo
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
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);  
        } 

    	$checkNumber = $data->getCheckNumber(); 
        $this->getInfoInstance()->setCheckNumber($checkNumber);
    	$checkAmt = $data->getCheckAmt();    
        $this->getInfoInstance()->setCheckAmt($checkAmt); 
       	//print_r($this->getInfoInstance()->getData());exit;
        return $this;
    }
}