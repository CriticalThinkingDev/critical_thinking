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

class AW_Marketsuite_Model_Order_Mag13 extends AW_Marketsuite_Model_Order_Abstract
{
    public function addAdditionalParams()
    {   
        $orderStatusSelect = $this->conn_read
                    ->select()
                    ->from(array('order_varchar' => $this->resource->getTableName('sales/order').'_varchar'))
                    ->where('entity_id = ?', $this->getEntityId())
                    ->joinInner(array('eav_attribute' => $this->resource->getTableName('eav/attribute')),
                    'order_varchar.attribute_id = eav_attribute.attribute_id', array())
                    ->where('eav_attribute.attribute_code=?', 'state');
        $orderStatus = $this->conn_read->fetchRow($orderStatusSelect);
        if (isset($orderStatus['value'])) $this->setOrderStatus($orderStatus['value']);

        $this->setOrderDate(substr($this->getCreatedAt(), 0, 10));
        
        
        if (version_compare(Mage::getVersion(), '1.3.3.0', '<=')) {

            $this->setOrderStoreId((array) $this->getStoreId());
        }
        
        

        return $this;
    }

}