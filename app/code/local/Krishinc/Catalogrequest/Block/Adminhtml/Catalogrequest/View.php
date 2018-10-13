<?php
class Krishinc_Catalogrequest_Block_Adminhtml_Catalogrequest_View extends Mage_Adminhtml_Block_Template
{
   
	
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'catalogrequest';
        $this->_controller = 'adminhtml_catalogrequest';
        $this->_mode        = 'view'; 

        parent::__construct();
    }
     
    
    protected function _prepareLayout()
    {  
    	 $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('catalogrequest')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        
        return parent::_prepareLayout();
    }
    
    
    public function getCatalogRequest()
    {
    	if($id = $this->getRequest()->getParam('id'))
    	{
    		$crData = Mage::getModel('catalogrequest/catalogrequest')->load($id);
    		if(!empty($crData))
    		{
    			return $crData;
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