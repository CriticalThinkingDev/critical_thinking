<?php

/**
 * @category   Krishinc
 * @package    Krishinc_Sourcecode
 * @license    http://opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */
class Krishinc_Sourcecode_Block_Adminhtml_Sales_Order_Create_Sourcecode extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function getSourceCode()
    {
        return $this->htmlEscape($this->getQuote()->getSourceCode());
    }

}
