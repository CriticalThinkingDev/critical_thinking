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


class AW_AdvancedReviews_Test_Model_Mysql4_Review extends EcomDev_PHPUnit_Test_Case {

    public static function setUpBeforeClass() {
        //      $query = 'SET FOREIGN_KEY_CHECKS=0;';

        $resource = Mage::getModel('core/resource');
        $connection = $resource->getConnection('core_write');


        $FKscope = array(
            'review' => array(
                'FK_REVIEW_ENTITY',
                'FK_REVIEW_STATUS',
                'FK_REVIEW_PARENT_PRODUCT',
            ),
            'review_store' => array(
                'FK_REVIEW_STORE_STORE',
                'FK_REVIEW_STORE_REVIEW',
            ),
            'review_detail' => array(
                'FK_REVIEW_DETAIL_REVIEW',
                'FK_REVIEW_DETAIL_STORE',
                'FK_REVIEW_DETAIL_CUSTOMER',
            ),
        );

        foreach ($FKscope as $table => $fks) {
            foreach ($fks as $fk) {
                try {
                    $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`"));
                    $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP KEY `{$fk}`"));
                } catch (Exception $e) {
                    echo 'fail';
                }
            }
        }
        //parent::setUp();
        parent::setUpBeforeClass();
    }

    /**

     * @test
     * @loadFixture review
     * @loadFixture review_store
     * @loadFixture review_detail
     * 
     * @dataProvider provider__test_getFilteredReviews
     */
    public function getFilteredReviews($storeId, $product, $reviews, $page, $limit, $sortBy, $sortDir, $customerId, $result=null) {

        Mage::app()->getStore()->setId($storeId);
        $test = Mage::getResourceModel('advancedreviews/review')->getFilteredReviews($product, $reviews, $page, $limit, $sortBy, $sortDir, $customerId);
        // var_dump($test->getData());
    }

    public function provider__test_getFilteredReviews() {
        return array(
            array(
                1, // store
                21, // product
                1, // reviews
                1, // page
                10, // $limit 
                '', //sort_by     by_date, by_rating, by_helpfulness
                'DESC', // ASC
                '', //customerId
            ),
            array(
                1, // store
                22, // product
                null, // reviews
                1, // page
                10, // $limit 
                'by_date', //sortBy     by_date, by_rating, by_helpfulness
                'DESC', // ASC
                '', //customerId
            ),
            array(
                3, // store
                8, // product
                1, // reviews
                1, // page
                10, // $limit 
                'by_rating', //sort_by     by_date, by_rating, by_helpfulness
                'DESC', // ASC
                '', //customerId
            ),
            array(
                1, // store
                8, // product
                1, // reviews
                1, // page
                10, // $limit 
                'by_helpfulness', //sort_by     by_date, by_rating, by_helpfulness
                'ASC', // ASC
                '', //customerId
            ),
        );
    }

    /**
     *   
     * @test
     * @loadFixture review
     * @loadFixture review_store
     * @loadFixture review_detail
     * @loadFixture advancedreviews_pc
     * 
     * @dataProvider provider__test_getReviewsByProscons
     */
    public function getReviewsByProscons($storeId, $proscons, $product_id, $customerId, $result) {
        //var_dump(func_get_args());
        Mage::app()->getStore()->setId($storeId);
        $test = Mage::getResourceModel('advancedreviews/review')->getReviewsByProscons($proscons, $storeId, $product_id, $customerId);
        //  var_dump($test['votes']);
        $arr = array();
        if (count($test['votes'])) {
            foreach ($test['votes'] as $value) {
                $arr[] = $value['id'];
            }
        }
        sort($arr);
        $test['votes'] = $arr;
        //var_dump($test['votes']);

        $this->assertEquals($test, $result);
    }

    public function provider__test_getReviewsByProscons() {

        return array(
            array(
                1, // store
                '1,3', // proscons
                21, // product_id
                0, // customerId

                array(
                    "reviews" => array(47),
                    "votes" => array(1,2,3),
                )
            ),
            array(
                1, // store
                '', // proscons
                22, // product_id
                0, // customerId
                array(
                    "reviews" => array(39, 40, 41),
                    "votes" => array(),
                )
            ),
            array(
                1, // store
                '', // proscons
                21, // product_id
                2, // customerId
                array(
                    "reviews" => array(47, 48),
                    "votes" => array(1,2,3,4,5),
                )
            ),
        );
    }

    /**
     *   
     * @test
     * @loadFixture review
     * @loadFixture review_store
     * @loadFixture review_detail
     * @loadFixture advancedreviews_pc
     * 
     * @dataProvider provider__test_addRateVotes
     */
    public function addRateVotes($id) {
        Mage::getResourceModel('advancedreviews/review')->addRateVotes($id);
    }

    public function provider__test_addRateVotes() {
        return array(
            array(0),
            array(1),
        );
    }

}