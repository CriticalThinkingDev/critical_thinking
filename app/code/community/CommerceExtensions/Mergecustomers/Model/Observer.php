<?php

/**
 * Observer.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Observer
 * @package    Mergecustomers
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */
class CommerceExtensions_Mergecustomers_Model_Observer extends Varien_Event_Observer
{
  public function addButton($observer)
  {
    if(Mage::getSingleton('admin/session')->isAllowed('admin/customer/mergecustomers')) {
      $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
      if(in_array('adminhtml_customer_edit', $handles)) {
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Adminhtml_Block_Customer_Edit) {
          $block->addButton('merge', array(
            'label'   => Mage::helper('mergecustomers')->__('Merge Customers'),
            'onclick' => 'mergecustomers.showGrid();',
            'class'   => 'go'
          ));
        }
      }
    }
    return $this;
  }
}


