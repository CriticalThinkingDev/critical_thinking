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

$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `{$this->getTable('review/review_detail')}` ADD `location` VARCHAR(255) NOT NULL AFTER `nickname`;");
/**
 * Change columns
 */
//$tables = array(
//  $installer->getTable('review/review_detail') => array(
//        'columns' => array(
//            'location' => array(
//                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
//                'length'    => 255,
//                'nullable'  => false,
//                'comment'   => 'User Location'
//            ),
//            'comment' => 'Review detail information'
//   	 )
//    )
//    
//    );
//$installer->getConnection()->modifyTables($tables);
$installer->endSetup();
