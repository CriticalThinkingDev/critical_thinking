<?php
class Krishinc_Customcontact_Block_Adminhtml_Customcontact_View extends Mage_Adminhtml_Block_Template
{
   
	
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'customcontact';
        $this->_controller = 'adminhtml_customcontact';
        $this->_mode        = 'view'; 

        parent::__construct();
    }
     
    
    protected function _prepareLayout()
    {  
    	 $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('customcontact')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        
        return parent::_prepareLayout();
    }
    
    
    public function getCustomcontacts()
    {
    	if($id = $this->getRequest()->getParam('id'))
    	{
    		 
    		$ccData = Mage::getModel('customcontact/customcontact')->load($id);
    		if(!empty($ccData))
    		{
    			return $ccData;
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