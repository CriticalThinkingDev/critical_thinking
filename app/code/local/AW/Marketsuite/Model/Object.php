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
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Marketsuite_Model_Object extends Varien_Object
{
    protected static $selectCache = array();

    protected $resource;

    /**
     * Connection for read from DB
     * @var mixed
     */
    protected $conn_read;

    protected $select;

    public function __construct()
    {
        $this->resource = Mage::getSingleton('core/resource');
        $this->conn_read  = $this->resource->getConnection('log_read');
    }

    /**
     * Default select from DB
     * @return Zend_Db_Select
     */
    public function getSelect()
    {
        return clone $this->select;
    }

}