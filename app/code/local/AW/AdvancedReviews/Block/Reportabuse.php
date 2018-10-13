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

class AW_AdvancedReviews_Block_Reportabuse extends Mage_Core_Block_Template
{
    protected $_reviewId;

    protected function _construct()
    {
        parent::_construct();
        //Mage::helper('advancedreviews')->setReviewsBackUrl( $this->getRedirectBackUrl() );
    }

    public function setReviewId($reviewId)
    {
        $this->_reviewId = $reviewId;
        return $this;
    }

    public function getRedirectBackUrl()
    {
        $params = $this->getRequest()->getParams();

        if(Mage::getStoreConfigFlag('web/seo/use_rewrites') && isset($params['id'])){
            return Mage::helper('advancedreviews')->getFuRewiewsUrl($params['id'],(isset($params['category'])?$params['category']:null));
        }
        else{
            if (isset($params['id'])) unset($params['id']);
            if (isset($params['category'])) unset($params['category']);
            $urlParams = array();
            $urlParams['_current'] = true;
            $urlParams['_escape'] = true;
            $urlParams['_use_rewrite'] = true;
            $urlParams['_query'] = $params;
            return $this->getUrl('*/*/*', $urlParams);
        }
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getAbuseUrl()
    {
        if ( $this->_reviewId )
        {
           return Mage::getUrl('advancedreviews/abuse/reportajax', array('reviewId' => $this->_reviewId) );
        }
        else
        {
           return '#'; 
        }        
    }
}

