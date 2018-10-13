<?php
class Krishinc_Offerlanding_Block_Adminhtml_Offerlanding_View extends Mage_Adminhtml_Block_Template
{
   
	
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'offerlanding';
        $this->_controller = 'adminhtml_offerlanding';
        $this->_mode        = 'view'; 

        parent::__construct();
    }
     
    
    protected function _prepareLayout()
    {  
    	 $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('offerlanding')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        
        return parent::_prepareLayout();
    }
    
    
    public function getOffers()
    {
    	if($id = $this->getRequest()->getParam('id'))
    	{
    		 
    		$olData = Mage::getModel('offerlanding/offerlanding')->load($id);
    		if(!empty($olData))
    		{
    			return $olData;
    		}
    	} 
    	
    } 
    
    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    { 
        return $this->getUrl('*/*/');
    }

}