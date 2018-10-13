<?php
class Interactone_Fixedshipping_Model_Rule_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract
{
	/**
	 * * Please check core file for the same. as abstract class cannot be overriden
	 *
	 * @param Varien_Object $object
	 * @return unknown
	 */
	public function validate(Varien_Object $object)
    { 
    	/******START:: Added by bijal to apply coupon for subtotal condition without virtual product price added*****/
    	$rule = $this->getRule();  
    	if($rule->getSimpleFreeShipping() > 0) {
		    if($this->getAttribute() == 'base_subtotal')
		    {
		    	$value =  $object->getBaseSubtotal() - $object->getBaseVirtualAmount();
		    }else{
		    	$value = $object->getData($this->getAttribute());
		    }
	  		return $this->validateAttribute($value); 
    	}
	    /*****END***/
        return $this->validateAttribute($object->getData($this->getAttribute())); 
    }
}