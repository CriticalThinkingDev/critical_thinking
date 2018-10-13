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
 

class AW_AdvancedReviews_Model_Urlrewrite extends Mage_Core_Controller_Varien_Router_Abstract
{
    /*
     * Suffix of the AdvancedReviews page URLs
     */
    const page_suffix   = '-reviews';
    const review_suffix = '-review-';

    public function init($observer)
    {
        $front = $observer->getEvent()->getFront();

        $advancedReviews = new AW_AdvancedReviews_Model_Urlrewrite();
        $front->addRouter('advancedreviews', $advancedReviews);

    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $path = $request->getPathInfo();

        if(strpos($path, self::review_suffix)){
            preg_match_all('/(-review-)(\d)*/', $path, $matchesarray);
            $reviewId = substr(implode('', reset($matchesarray)), strlen(self::review_suffix));

            if($reviewId){
    			$request->setModuleName('review')
            			->setControllerName('product')
                    	->setActionName('view')
                        ;
                $request->setParam('id', $reviewId);
                return true;
            }
        }
        elseif(strpos($path, self::page_suffix)){

            $path = str_replace(self::page_suffix, '', $path);

            $collection = Mage::getModel('core/url_rewrite')->getCollection();
            $collection->getSelect()
                    ->where('request_path = ?',substr($path, 1))
                    ->where('store_id = ?',Mage::app()->getStore()->getId())
                    ;

            foreach($collection->getData() as $rule){
                    $id = $rule['product_id'];
                    $category = $rule['category_id'];
            }

            if(isset($id)){
    			$request->setModuleName('review')
            			->setControllerName('product')
                    	->setActionName('list')
                        ;
                $request->setParam('id', $id);
                if($category)
                    $request->setParam('category', $category);

                return true;
            }
        }
    }
}