<?php
/**
 * Downloadplus Uploader Multiple Block
 * for HTML5 File Uploader (Magento 1.9.3+)
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2016 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Uploader_Single extends Mage_Uploader_Block_Multiple
{

    public function addElementIdsMapping($additionalButtons = array())
    {
        return $this->_addElementIdsMapping($additionalButtons);
    }

}