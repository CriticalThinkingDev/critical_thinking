<?php

/**
 * @category   Krishinc
 * @package    Krishinc_Sourcecode
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Krishinc_Sourcecode_Block_Adminhtml_Sales_Order_Note extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    /**
     * @var Krishinc_Sourcecode_Model_Note
     */
    protected $_note;

    /**
     * @return Krishinc_Sourcecode_Model_Note|null
     */
    public function getSourceCode()
    {
    	 
        if (null === $this->_note) {
        	$this->_note = $this->getOrder()->getSourceCode();
        }
        return $this->_note;
    }
}
