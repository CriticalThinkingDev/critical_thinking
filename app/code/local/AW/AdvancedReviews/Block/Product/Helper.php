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

class AW_AdvancedReviews_Block_Product_Helper extends Mage_Review_Block_Helper{
    
    public function getReviewsUrl()
    {
        if(preg_match('/(-review-)(\d)*/', $this->getRequest()->getOriginalPathInfo()))
            return $this->getRequest()->getServer('HTTP_REFERER');

        $params = $this->getRequest()->getParams();
 
        if(Mage::getStoreConfigFlag('web/seo/use_rewrites') && $shortLink = Mage::helper('advancedreviews')->getFuRewiewsUrl($params['id'],(isset($params['category'])?$params['category']:null)

        )){
         
            return $shortLink;
        }
        else{
        
            return Mage::getUrl('review/product/list', array(
               'id'        => $this->getProduct()->getId(),
               'category'  => $this->getProduct()->getCategoryId()
            ));
        }
    }
}

