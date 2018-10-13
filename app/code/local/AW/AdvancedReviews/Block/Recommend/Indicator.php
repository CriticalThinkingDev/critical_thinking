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

class AW_AdvancedReviews_Block_Recommend_Indicator extends Mage_Core_Block_Template
{
    protected $_reviewId;
    protected $_collection;
    
    public function setReviewId( $reviewId )
    {
        $this->_reviewId = $reviewId;
        return $this;
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getCollection()
    {
        if ( ! $this->_collection )
        {
            $this->_collection = Mage::getModel('review/review')
                                 ->getCollection()
                                 ->addStoreFilter(Mage::app()->getStore()->getId())
                                 ->addStatusFilter('approved')
                                 ->addRecommendSummary();

            if ( $customerId = Mage::registry('advancedreviews_proscons_is_customer') )
            {
                $this->_collection->addCustomerFilter( $customerId );
            }
            elseif ( $productId = Mage::registry('advancedreviews_proscons_is_product') )
            {
                $this->_collection->addEntityFilter('product', $productId );
            }
        }                
        return $this->_collection;
    }

    public function canShow()
    {
        return ( Mage::helper('advancedreviews')->confShowRecommend()
                 && $this->_isInCollection( $this->_reviewId )
                 && $this->getAnswer() );
    }
    
    protected function _getLabel( $value )
    {
        foreach ( Mage::helper('advancedreviews')->confRecommendItemsArray() as $option )
        {
            if ( $option['value'] == $value )
            {
                return $option['label'];
            }
        }
        return '';
    }

    public function getAnswer()
    {
        return $this->_getLabel( $this->_getCollectionReview( $this->_reviewId )->getRecommend() );
    }

    protected function _getCollectionReview( $reviewId )
    {
        if ( count( $this->getCollection() ) )
        {
            foreach ( $this->getCollection() as $review )
            {
                if ( $review->getId() === $reviewId )
                {
                    return $review;
                }
            }
        }        
        return null;
    }

    protected function _isInCollection( $reviewId )
    {      
        return ( $this->_getCollectionReview( $reviewId ) !== null );
    }

}