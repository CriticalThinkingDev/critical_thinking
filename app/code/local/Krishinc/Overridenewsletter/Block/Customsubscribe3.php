<?php 
class Krishinc_Overridenewsletter_Block_Customsubscribe3 extends Mage_Core_Block_Template
{
	public function __construct()
    {
       parent::__construct(); 
     /*  $session = Mage::getSingleton('core/session');
       
       	if($session->getRedirectFromResale()) 
       	{
       		$this->setTemplate('overridenewsletter/resale_info.phtml');
       		
       	} else {
    	   	$this->setTemplate('overridenewsletter/subscribe.phtml');
       	}
        */
	}
	
	public function _prepareLayout()
	{    
		$param = $this->getRequest()->getParams();
		if(isset($param))
		{
			if(isset($param['resale']))
			{ 
			  $this->setTemplate('overridenewsletter/resale_info.phtml');
			} else {
    	   	$this->setTemplate('overridenewsletter/subscribe3.phtml');
       	}
		}  else {
    	   	$this->setTemplate('overridenewsletter/subscribe3.phtml');
       	}
		return parent::_prepareLayout(); 
	}
    
	public function getSuccessMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getSuccess();
        return $message;
    }

    public function getErrorMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getError();
        return $message;
    }
    
	/**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('overridenewsletter/subscriber/save', array('_secure' => true));
    }
    
    
    
   
    
}
