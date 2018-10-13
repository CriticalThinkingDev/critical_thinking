<?php
/**
 * Downloadable Admin Download Redirect controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Adminhtml_Downloadplus_RedirectController extends Mage_Adminhtml_Controller_action
{

    /**
     * Initialize order model instance
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order');
        $order = Mage::getModel('sales/order')->loadByIncrementId($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

	public function viewOrderAction()
	{
		$order = $this->_initOrder();
		$params = Array();

		if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        $params['order_id'] = $order->getId();

	    $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', $params);

	    $this->getResponse()->setRedirect($url);
	}

}