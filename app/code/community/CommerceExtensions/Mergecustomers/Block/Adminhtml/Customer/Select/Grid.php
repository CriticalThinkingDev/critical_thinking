<?php

/**
 * Grid.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Grid
 * @package    Mergecustomers
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */
class CommerceExtensions_Mergecustomers_Block_Adminhtml_Customer_Select_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
  /**
   * CommerceExtensions_Mergecustomers_Block_Adminhtml_Select_Customer_Grid constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultLimit(25);
  }

  /**
   * @return \Mage_Core_Block_Abstract
   */
  protected function _prepareLayout()
  {
    $this->setCustomer(Mage::registry('current_customer'));
    return parent::_prepareLayout();
  }

  /**
   * @return null
   */
  protected function _prepareMassaction()
  {
    return null;
  }

  protected function _prepareColumns()
  {
    parent::_prepareColumns();
    unset($this->_columns['website_id']);
    unset($this->_columns['action']);
    unset($this->_columns['group']);
    unset($this->_columns['customer_since']);
    unset($this->_exportTypes);
  }

  /**
   * @param $row
   *
   * @return string
   */
  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/form',array('master' => $this->getCustomer()->getId(),'merge' => $row->getId()));
  }
}