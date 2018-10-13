<?php

class CommerceExtensions_Mergecustomers_Block_Adminhtml_Customer_Merge_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  /**
   * CommerceExtensions_Mergecustomers_Block_Adminhtml_Customer_Merge_Edit constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->_objectId   = 'id';
    $this->_blockGroup = 'mergecustomers';
    $this->_controller = 'adminhtml_customer_merge';

    $this->_removeButton('save');
    $this->_removeButton('reset');
    $this->_updateButton('back','onclick',"setLocation('{$this->_getBackUrl()}')");
    $this->_addButton('cancel', array(
      'label'   => $this->__('Cancel Merge'),
      'onclick' => "setLocation('{$this->_getBackUrl()}')",
      'class'   => 'delete',
    ), -50);
    $this->_addButton('merge_final', array(
      'label'   => $this->__('Complete Merge'),
      'onclick' => "confirmSubmit();",
      'class'   => 'save',
    ), -100);

    $confirm = $this->__('Are you sure you want to merge these customers? This cannot be undone.');
    $this->_formScripts[] = "
      function confirmSubmit(){
        if(confirm('{$confirm}')){
           editForm.submit();
        }
      }
    ";
  }

  /**
   * @return string
   */
  public function getHeaderText()
  {
    return $this->__('Merge Customers');
  }

  /**
   * @return string
   */
  protected function _getBackUrl()
  {
    return $this->getUrl('*/mergecustomers/grid',array('id' => Mage::registry('master_customer')->getId()));
  }
}