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

	
class AW_AdvancedReviews_Block_Ajax_Proscons extends Mage_Core_Block_Template
{

    protected $_collection;

	public function __construct()
	{
		$this->setTemplate('advancedreviews/ajax/proscons.phtml');
        parent::__construct();
	}

    protected function _toHtml()
    {
        return $this->renderView();
    }

    public function getReviewsAndFilters(){

        $product_id = $this->getRequest()->getParam('id');
        $customerId = $this->getRequest()->getParam('customerId');
        $proscons = $this->getRequest()->getParam('proscons');
        $reviews = Mage::getResourceModel('advancedreviews/review')->getReviewsByProscons($proscons,Mage::app()->getStore()->getId(),$product_id,$customerId);
        return $reviews;
    }

    public function getSelectedFilters(){
        return explode(',',$this->getRequest()->getParam('proscons'));
    }
}