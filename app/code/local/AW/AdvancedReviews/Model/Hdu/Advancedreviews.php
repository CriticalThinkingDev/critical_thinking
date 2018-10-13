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

/**
 * Implements Source for HDU Proto
 */
class AW_AdvancedReviews_Model_Hdu_Advancedreviews extends AW_Helpdeskultimate_Model_Proto_Source_Abstract{

    /**
     * Returns founded customer or preset entity
     * @param AW_Helpdeskultimate_Model_Proto $Proto Proto with sender data
     * @return Varien_Object
     */
    public function getCustomer(AW_Helpdeskultimate_Model_Proto $Proto)
    {
        $Customer = $this->_findCustomer($Proto->getFromEmail());
        if($Proto->getFromEmail() == Mage::getStoreConfig('trans_email/ident_general/email') || !$Customer){
            $customer = new Varien_Object(array(
                            'email'=>Mage::registry('advancedreviews_guest_email'),
                            'name'=>Mage::helper('advancedreviews')->__('Guest'),
                            'id'=>0,
                        ));
            return $customer;
        }else{
            return $this->_findCustomer($Proto->getFromEmail());      
        }
    }
}