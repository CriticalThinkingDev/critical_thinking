<?php
class Krishinc_Paymentrestriction_Model_Adminhtml_System_Config_Source_Payment_Methods
{
    protected $_options;
    public function toOptionArray()
    {
        $model = Mage::getSingleton('payment/config');
        $methods = $model->getAllMethods();
        $activeMethods = $model->getActiveMethods();
        $activeMethods = array_keys($activeMethods);
        if (!$this->_options) {
            foreach ($methods as $method) {
                $code = $method->getId();
                $methodTitle = Mage::getStoreConfig('payment/'.$code.'/title', Mage::app()->getStore()->getId());
                $methodTitle = trim($methodTitle);
                if (empty($methodTitle)) {
                    continue;
                }
                if (in_array($code, $activeMethods)) {
                    $methodTitle = sprintf('%s (currently active)', $methodTitle);
                } else {
                    $methodTitle = sprintf('%s (currently inactive)', $methodTitle);
                }
                $this->_options[] = array('value'=>$code, 'label'=>$methodTitle);
            }
        }
        $options = $this->_options; 
        array_unshift($options, array('value'=>'', 'label'=> ''));
        return $options;
    }
}