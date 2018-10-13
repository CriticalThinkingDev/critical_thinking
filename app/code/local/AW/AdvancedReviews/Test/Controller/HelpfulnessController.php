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


class AW_AdvancedReviews_Test_controller_HelpfulnessController extends EcomDev_PHPUnit_Test_Case_Controller {

    public function setUp() {
        //      $query = 'SET FOREIGN_KEY_CHECKS=0;';

        $resource = Mage::getModel('core/resource');
        $connection = $resource->getConnection('core_write');


        $FKscope = array(
            'aw_advancedreviews_helpfulness' => array(
                'FK_reviews_helpfulness',
            ),
        );

        foreach ($FKscope as $table => $fks) {
            foreach ($fks as $fk) {
                try {
                    $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`"));
                    $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP KEY `{$fk}`"));
                } catch (Exception $e) {
                    //  echo 'fail';
                }
            }
        }
        parent::setUp();
        //parent::setUpBeforeClass();
        $session = Mage::getSingleton('customer/session', array('name' => 'frontend'))->start();
        $session->setHelpfulnessReviews(array());
    }

    /**

     * @test
     * @loadFixture review
     * @loadFixture review_store
     * @loadFixture review_detail
     * @loadFixture advancedreviews_pc
     * @dataProvider provider_test_postajaxAction
     */
    public function postajaxAction($id, $action, $cfg, $twice, $expected) {
        Mage::app()->getStore(1)->setConfig('advancedreviews/access_options/allow_only_logged_vote', $cfg);
        $this->dispatch("advancedreviews/helpfulness/postajax/reviewId/{$id}/actionName/{$action}/");
        if ($twice) {
            $this->dispatch("advancedreviews/helpfulness/postajax/reviewId/{$id}/actionName/{$action}/");
        }
        $this->assertResponseBodyContains($expected);
    }

    public function provider_test_postajaxAction() {
        return array(
            array(50, 'Yes', 0, 0, 'Thank you for your vote'),
            array(50, 'Yes', 1, 0, 'You need log in to vote review'),
            array(50, 'Yes', 0, 1, 'You can vote only once for the same review'),
            array(51, 'No', 0, 0, 'Thank you for your vote'),
            array(52, 'Yes', 0, 0, 'Thank you for your vote'),
        );
    }

    /**

     * @test
     * @loadFixture review
     * @loadFixture review_store
     * @loadFixture review_detail
     * @loadFixture advancedreviews_pc
     * @dataProvider provider_test_viewAction
     */
    public function postAction($id, $action, $is_real, $cfg) {
        Mage::app()->getStore(1)->setConfig('advancedreviews/access_options/allow_only_logged_vote', $cfg);
        $session = Mage::getSingleton('core/session');
        $this->dispatch("advancedreviews/helpfulness/post/reviewId/{$id}/actionName/{$action}/real/{$is_real}");
        $this->dispatch("advancedreviews/helpfulness/post/reviewId/{$id}/actionName/{$action}/real/{$is_real}");
        //var_dump($session->getMessages());
        //$this->dispatch("advancedreviews/helpfulness/post/reviewId/{$id}/actionName/{$action}/real/{$is_real}");
        //var_dump($session->getMessages());
    }

    public function provider_test_viewAction() {
        return array(
            array(50, 'Yes', 1, 0),
            array(50, 'Yes', 1, 1),
            array(50, 'Yes', 0, 0),
            array(50, 'Yes', 0, 1),
            array(51, 'No', 1, 0),
            array(51, 'No', 1, 1),
            array(51, 'No', 0, 0),
            array(51, 'No', 0, 1),
        );
    }

}