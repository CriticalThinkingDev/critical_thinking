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
 * @package    AW_AdvancedReviews
 * @version    2.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_AdvancedReviews_Model_System_Config_Source_Ordering_Items {

    public function toOptionArray() {
        return array(
            array('value' => 'by_date', 'label' => Mage::helper('advancedreviews')->__('Date')),
            array('value' => 'by_rating', 'label' => Mage::helper('advancedreviews')->__('Average rating')),
            array('value' => 'by_helpfulness', 'label' => Mage::helper('advancedreviews')->__('Helpfulness')),
        );
    }

}
