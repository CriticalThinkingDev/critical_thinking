<?php
 
class  Krishinc_Overridepo_Block_Adminhtml_Sales_Order_Ordertype extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    protected $_ordertype;

    /**
     * @return string|null
     */
    public function getOrderType()
    {
        if (null === $this->_ordertype) {
        	$this->_ordertype = $this->getOrder()->getOrderType();
        }
        return $this->_ordertype;
    }
}
