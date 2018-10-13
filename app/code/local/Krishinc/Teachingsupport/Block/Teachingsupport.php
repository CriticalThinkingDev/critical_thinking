<?php
class Krishinc_Teachingsupport_Block_Teachingsupport extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTeachingsupport()     
     { 
     	if($post = $this->getRequest()->getPost())
    	{
    		if(isset($post['product_id']))
    		{
    			$collection = Mage::getResourceModel('teachingsupport/teachingsupport_collection');//->addFieldToFilter('sku',array('in' => array('finset',$post['product_id'])));
    		    $collection->getSelect()->Where('FIND_IN_SET("'.$post['product_id'].'", sku )'); 
    		    if(sizeof($collection) == 1) {
	    		 	foreach ($collection as $ts) {
	    		 		return $ts;
	    		  	}  
    		    }
 
    			 
    		}
    	}
        if (!$this->hasData('teachingsupport')) {
            $this->setData('teachingsupport', Mage::registry('teachingsupport'));
        }
        return $this->getData('teachingsupport');
        
    }
}