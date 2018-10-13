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

class AW_AdvancedReviews_Block_Allmylink extends Mage_Core_Block_Template
{
    protected $_review;
    protected $_reviewId;
    protected $_customerId;

    protected function _reviewHasCustomer()
    {
        if ($this->_review !== null)
        {
            return ($this->_review->getCustomerId() !== null);
        }
        else
        {
            return 0;
        }        
    }

    public function setReviewId($Value)
    {
        $this->_reviewId = $Value;        
        return $this;
    }
    
    /*
     * This set ReviewId and current Customer id for current Review id     
     * @var object
     */
    public function setReview($Value)
    {
        $this->_review = $Value;
        $this->_reviewId = $this->_review->getId();
        $this->_customerId = $this->_review->getCustomerId();
        return $this;
    }

    public function getAllMyLink()
    {
        return Mage::getUrl('advancedreviews/allmy/list', array('customerId' => $this->_customerId));
    }

    public function getCustomerReviewsCount()
    {
        if ($this->_customerId)
        {
            return Mage::getModel('review/review')->getCollection()
                   ->getCustomerReviewsCount($this->_customerId);
        }
        else
        {
            return '0';
        }        
    }

    protected function _notInAllMy()
    {       
        return $this->getRequest()->getControllerName() !== 'allmy';
    }

    protected function _toHtml()
    {
        if (Mage::helper('advancedreviews')->confShowAllMyLinks() 
            && $this->_reviewHasCustomer()
            && $this->_notInAllMy())
        {
            return parent::_toHtml();
        }               
    }
}
