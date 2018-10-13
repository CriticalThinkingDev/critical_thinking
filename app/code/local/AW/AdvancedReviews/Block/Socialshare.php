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

class AW_AdvancedReviews_Block_Socialshare extends Mage_Core_Block_Template
{
    protected $_reviewId;
    
    public function setReviewId($Value)
    {
        $this->_reviewId = $Value;
        return $this;
    }
    
    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getReviewTitle($id)
    {
        return Mage::helper('advancedreviews')->getReviewTitle($id);
    }

    public function getReviewUrl($id)
    {
        return Mage::helper('advancedreviews')->getReviewUrl($id);
    }

    public function getImageUrl($id)
    {
        return $this->getSkinUrl('advancedreviews/images/');
    }
    
    public function getSocialBlock()
    {
        $html = Mage::helper('advancedreviews')->confSocialHtmlBlock();

        $html = str_replace('__URL__', rawurlencode( $this->getReviewUrl( $this->getReviewId() ) ), $html);
        $html = str_replace('__TITLE__', rawurlencode( $this->getReviewTitle( $this->getReviewId() ) ), $html);
        $html = str_replace('__IMAGEPATH__', $this->getImageUrl( $this->getReviewId() ), $html);

        return $html;
    }

}
