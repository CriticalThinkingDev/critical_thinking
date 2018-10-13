<?php
class Krishinc_Dealerskit_Block_Adminhtml_Dealerskit_View extends Mage_Adminhtml_Block_Template
{
   
	
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'dealerskit';
        $this->_controller = 'adminhtml_dealerskit';
        $this->_mode        = 'view'; 

        parent::__construct();
    }
     
    
    protected function _prepareLayout()
    {  
    	 $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('dealerskit')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        
        return parent::_prepareLayout();
    }
    
    
    public function getDealerskitRequest()
    {
    	if($id = $this->getRequest()->getParam('id'))
    	{
    		$drData = Mage::getModel('dealerskit/dealerskit')->load($id);
    		if(!empty($drData))
    		{
    			return $drData;
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