<?php

class CommerceExtensions_Mergecustomers_Adminhtml_MergecustomersController extends Mage_Adminhtml_Controller_Action
{
  public function gridAction()
  {
    try {
      $customerId = $this->getRequest()->getParam('id');
      if(!$customerId) {
        Mage::throwException($this->_getHelper()->__('Customer ID not present.'));
      }

      $model = Mage::getModel('customer/customer')->load($customerId);
      if(!$model->hasData()) {
        Mage::throwException($this->_getHelper()->__('Invalid Customer ID specified.'));
      }
      Mage::register('current_customer', $model);

      $this->loadLayout();
      $this->renderLayout();
    } catch(Exception $e) {
      $this->_getSession()->addError($this->_getHelper()->__($e->getMessage()));
      $this->_redirectReferer();
    }
    return;
  }

  public function formAction()
  {
    try {

      $masterId = $this->getRequest()->getParam('master');
      if(!$masterId) {
        Mage::throwException($this->_getHelper()->__('Master Customer ID not present.'));
      }

      $mergeId = $this->getRequest()->getParam('merge');
      if(!$mergeId) {
        Mage::throwException($this->_getHelper()->__('Merge Customer ID not present.'));
      }

      if($mergeId == $masterId) {
        Mage::throwException($this->_getHelper()->__('The Master Customer and the Merge Customer must be different. You have selected to merge the Master Customer into itself.'));
      }

      $master = Mage::getModel('customer/customer')->load($masterId);
      if(!$master->hasData()) {
        Mage::throwException($this->_getHelper()->__('Invalid Master Customer ID specified.'));
      }
      Mage::register('master_customer', $master);

      $merge = Mage::getModel('customer/customer')->load($mergeId);
      if(!$merge->hasData()) {
        Mage::throwException($this->_getHelper()->__('Invalid Merge Customer ID specified.'));
      }
      Mage::register('merge_customer', $merge);

      $this->loadLayout();
      $this->renderLayout();

    } catch(Exception $e) {
      $this->_getSession()->addError($this->_getHelper()->__($e->getMessage()));
      $this->_redirectReferer();
    }
    return;
  }

  public function mergeAction()
  {
    try {

      $direction = $this->getRequest()->getParam('merge_direction');
      if(!$direction) {
        Mage::throwException($this->_getHelper()->__('Merge Direction parameter not present.'));
      }

      $customerAId = $this->getRequest()->getParam('customer_a_id');
      if(!$customerAId) {
        Mage::throwException($this->_getHelper()->__('Customer A id parameter not present.'));
      }

      $customerBId = $this->getRequest()->getParam('customer_b_id');
      if(!$customerBId) {
        Mage::throwException($this->_getHelper()->__('Customer B id parameter not present.'));
      }

      $master = Mage::getModel('customer/customer');
      $merge  = Mage::getModel('customer/customer');

      if($direction == 'reverse') {
        $master->load($customerBId);
        $merge->load($customerAId);
      } else {
        $master->load($customerAId);
        $merge->load($customerBId);
      }

      /** Reassign Merge Customer Addresses to Master Customer */
      $i = 0;
      foreach($merge->getAddresses() as $address) {
        $address->setParentId($master->getId());
        $address->setCustomerId($master->getId());
        if($address->save()) {
          $i++;
        }
      }

      if($i) {
        $this->_getSession()->addSuccess($this->_getHelper()->__('%s addresses were merged into the master customer.', $i));
      }

      /** instantiate models to update customer ids */
      $models = array(
        'order(s)'           => Mage::getModel('sales/order'),
        'product ratings(s)' => Mage::getModel('rating/rating_option_vote')
      );

      foreach($models as $label => $model) {
        $i          = 0;
        $collection = $model->getCollection();
        $collection->addFieldToFilter('customer_id', $merge->getId());
        foreach($collection as $object) {
          $object->setCustomerId($master->getId());
          if($object->save()) {
            $i++;
          }
        }
        if($i) {
          $this->_getSession()->addSuccess($this->_getHelper()->__(sprintf('%s %s were merged into the master customer.', $i, $label)));
        }
      }

      /** modify the tables directly here */
      $resource = Mage::getSingleton('core/resource');
      $adapter  = $resource->getConnection('core_write');
      $tables   = array(
        'product review(s)'    => 'review_detail',
        'invoice(s)'           => 'sales_flat_invoice',
        'shipment(s)'          => 'sales_flat_shipment',
        'downloadable link(s)' => 'downloadable_link_purchased',
        'price alert(s)'       => 'product_alert_price',
        'stock alert(s)'       => 'product_alert_stock',
        'tag(s)'               => 'tag_relation',
        'poll vote(s)'         => 'poll_vote',
        'sales_flat_order',
        'sales_flat_order_address',
        'sales_flat_order_grid',
        'sales_flat_quote',
        'sales_flat_quote_address'
      );

      foreach($tables as $label => $table) {
        $table  = $resource->getTableName($table);
        $column = 'customer_id';

        if($this->_tableExists($adapter, $table)) {
          if($adapter->tableColumnExists($table, $column)) {
            $bind  = array($column => $master->getId());
            $where = array("{$column} = ?" => $merge->getId());
            $count = $adapter->update($table, $bind, $where);
            if($count && is_string($label)) {
              $this->_getSession()->addSuccess($this->_getHelper()->__(sprintf('%s %s were merged into the master customer.', $count, $label)));
            }
          }
        }
      }

      /** Merge Wishlist */
      $masterWishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($master);
      $mergeWishlist  = Mage::getModel('wishlist/wishlist')->loadByCustomer($merge);
      $successMsg = $this->_getHelper()->__('Wishlist merged into the master customer.');
      if(!$masterWishlist->getId() && $mergeWishlist->getId()) {
        $count = $adapter->update('wishlist', array('customer_id' => $master->getId()), array('customer_id = ?' => $merge->getId()));
        if($count) {
          $this->_getSession()->addSuccess($successMsg);
        }
      } elseif($masterWishlist->getId() && $mergeWishlist->getId()) {
        $count = $adapter->update('wishlist_item', array('wishlist_id' => $masterWishlist->getId()), array('wishlist_id = ?' => $mergeWishlist->getId()));
        if($count) {
          $this->_getSession()->addSuccess($successMsg);
        }
        $mergeWishlist->delete();
      }
      $merge->delete();
      $message = sprintf('MERGE COMPLETE. Customer ID# %s, %s, is now the master customer. Customer ID# %s, %s, has been deleted.', $master->getId(), $master->getName(), $merge->getId(), $merge->getName());
      $this->_getSession()->addSuccess($this->_getHelper()->__($message));

    } catch(Exception $e) {
      $this->_getSession()->addError($this->_getHelper()->__($e->getMessage()));
      $this->_redirectReferer();
      return;
    } catch(Mage_Core_Model_Store_Exception $e){
      $this->_getSession()->addError($this->_getHelper()->__($e->getMessage()));
      $this->_redirectReferer();
      return;
    }
    $this->_redirect('*/mergecustomers/grid', array('id' => $master->getId()));
    return;
  }

  /**
   * @param \Varien_Db_Adapter_Pdo_Mysql $adapter
   * @param                              $table
   *
   * @return bool
   */
  protected function _tableExists(Varien_Db_Adapter_Pdo_Mysql $adapter, $table)
  {
    $statement = $adapter->query("SHOW TABLES LIKE '{$table}'");
    $result    = $statement->fetch();
    return $result !== false;
  }

  /**
   * @return bool
   */
  protected function _isAllowed()
  {
    return Mage::getSingleton('admin/session')->isAllowed('admin/customer/mergecustomers');
  }
}
