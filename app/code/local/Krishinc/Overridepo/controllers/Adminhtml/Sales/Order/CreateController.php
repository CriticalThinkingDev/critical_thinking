<?php
require_once('Mage/Adminhtml/controllers/Sales/Order/CreateController.php');
class Krishinc_Overridepo_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
   
    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        $postdata = $this->getRequest()->getPost(); 
	if(isset($postdata['subscription']) && $postdata['subscription']=='on')
                $this->_getOrderCreateModel()->getQuote()->setIsSubscribed(1);
	parent::saveAction();
        
    }

    
}
