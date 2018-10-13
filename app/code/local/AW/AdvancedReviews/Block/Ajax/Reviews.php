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

	
class AW_AdvancedReviews_Block_Ajax_Reviews extends AW_AdvancedReviews_Block_Product_Reviews
{

    protected $_collection = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('advancedreviews/ajax/reviews.phtml');
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        if(!$this->_collection){
            $collection = Mage::getResourceModel('advancedreviews/review')->getFilteredReviews($this->getRequest()->getParam('id'),null,1,10,null,'DESC',$this->getRequest()->getParam('customerId'));
            $this->_collection = $collection;
        }
        return $this->_collection;
    }
    public function getRatingVotes($id){
        return $votesCollection = Mage::getModel('rating/rating_option_vote')
            ->getResourceCollection()
            ->setReviewFilter($id)
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->addRatingInfo(Mage::app()->getStore()->getId())
            ->load();
    }

}