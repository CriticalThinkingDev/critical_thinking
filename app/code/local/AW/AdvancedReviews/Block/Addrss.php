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

class AW_AdvancedReviews_Block_Addrss extends Mage_Core_Block_Template
{
    protected function _prepareLayout(){

        if(Mage::helper('advancedreviews')->getRssEnabled()){
            $category = '';
            $product  = '';

            switch($this->getRequest()->getControllerName()){
                case 'category':
                    $category = $this->getRequest()->getParam('id');
                    break;
                case 'product':
                    $product = $this->getRequest()->getParam('id');
                    break;
            }
            $route = Mage::helper('advancedreviews')->getRssRoute($category,$product);
            $path = $this->getUrl($route);

            $title = 'Advanced Reviews';

            $headBlock = $this->getLayout()->getBlock('head');
            Mage::helper('advancedreviews')->addRss($headBlock,$path,$title);

            return $this;
        }
    }
}
