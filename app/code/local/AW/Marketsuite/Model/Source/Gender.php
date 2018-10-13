<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Marketsuite_Model_Source_Gender extends AW_Marketsuite_Model_Source_Abstract
{
    const NOT_SPECIFIED = -1;
    const NOT_SPECIFIED_LABEL = 'Not Specified';

    protected function _toOptionArray()
    {
        $_helper = $this->_getHelper();
        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        $options = $attribute->getSource()->getAllOptions();
        foreach($options as &$option) {
            if ($option['label'] == '' && $option['value'] == '') {
                $option['label'] = self::NOT_SPECIFIED_LABEL;
                $option['value'] = self::NOT_SPECIFIED;
            }
            $option['label'] = $_helper->__($option['label']);
        }
        return $options;
    }
}
