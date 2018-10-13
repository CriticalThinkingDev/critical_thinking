<?php
class Krishinc_Fieldtester_Block_Adminhtml_Fieldtester_View extends Mage_Adminhtml_Block_Template
{
   
	
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'fieldtester';
        $this->_controller = 'adminhtml_fieldtester';
        $this->_mode        = 'view'; 

        parent::__construct();
    }
     
    
    protected function _prepareLayout()
    {  
    	 $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('fieldtester')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        
        return parent::_prepareLayout();
    }
    
    
    public function getFieldtesters()
    {
    	if($id = $this->getRequest()->getParam('id'))
    	{
    		 
    		$ftData = Mage::getModel('fieldtester/fieldtester')->load($id);
    		if(!empty($ftData))
    		{
    			return $ftData;
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