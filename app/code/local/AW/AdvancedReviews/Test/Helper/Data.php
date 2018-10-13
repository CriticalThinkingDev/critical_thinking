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


class AW_AdvancedReviews_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {
    /*
     * 

     *  @dataProvider provider_getPagerToolbar
     */

    //($product = null, $reviews = null, $page = null, $limit = null)
    public function test_getPagerToolbar($product, $reviews, $page, $limit) {
        Mage::helper('advancedreviews')->getPagerToolbar($product, $reviews, $page, $limit);
    }

    public function provider_getPagerToolbar() {
        return array(
            array(
                0, //product
                array(1, 2), // reviews
                1, //page
                10 //limit
            ),
            array(
                0, //product
                array(1, 2), // reviews
                1, //page
                10 //limit
            ),
            array(
                0, //product
                array(1, 2), // reviews
                1, //page
                10 //limit
            ),
        );
    }

    /*
     * 
     * @test
     * @dataProvider provider__isHelpfulnessRegistered
     */

    public function test_registerHelpfulness($id) {
        Mage::helper('advancedreviews')->registerHelpfulness($id);
    }

    /*
     * 
     * @test
     * @dataProvider provider__isHelpfulnessRegistered
     */

    public function test_isHelpfulnessRegistered($id) {
        Mage::helper('advancedreviews')->isHelpfulnessRegistered($id);
    }

    public function provider__isHelpfulnessRegistered() {
        return array(
            array(1),
            array(2),
            array(3),
            array(4),
        );
    }

}
