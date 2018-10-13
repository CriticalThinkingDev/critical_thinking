<?php

class CommerceExtensions_Mergecustomers_Block_Adminhtml_Customer_Merge_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  /**
   * @return \Mage_Adminhtml_Block_Widget_Form
   */
  protected function _prepareForm()
  {
    $master = Mage::registry('master_customer');
    $merge  = Mage::registry('merge_customer');

    $form = new Varien_Data_Form(
      array(
        'id'      => 'edit_form',
        'action'  => $this->getUrl('*/mergecustomers/merge'),
        'method'  => 'post',
        'enctype' => 'multipart/form-data'
      ));

    $fieldset = $form->addFieldset('main_fieldset', array('legend' => $this->__('Merge Options')));
    $fieldset->addField('merge_direction', 'select', array(
      'name'     => 'merge_direction',
      'label'    => $this->__('Merge Direction'),
      'title'    => $this->__('Merge Direction'),
      'required' => true,
      'options'  => array('forward' => 'Merge Customer B into Customer A', 'reverse' => 'Merge Customer A into Customer B'),
    ));

    $note = <<<EOD
<p style="max-width:400px;">The customer being merged into will become the master customer and the other customer will be deleted. For example, if you choose to merge Customer B into Customer A, then Customer A will become the master customer and Customer B will be deleted. The same is true of the reverse.</p>
<p style="max-width:400px;">During the merge all possible customer data will be merged into the master customer. This includes, but is not limited to addresses, orders, product reviews, etc...</p>
<p style="max-width:400px;">The data contained in the Master Customer's "Account Information" tab will be unchanged by the merge.</p>
EOD;

    $fieldset->addField('merge_note', 'note', array(
      'name'     => 'merge_note',
      'label'    => $this->__('Note'),
      'title'    => $this->__('Note'),
      'required' => true,
      'text'     => $this->__($note),
    ));

    $keys = array('a' => $master, 'b' => $merge);
    foreach($keys as $key => $model) {
      $fieldset = $form->addFieldset("customer_{$key}_fieldset", array('legend' => $this->__('Customer %s', strtoupper($key))));
      $fieldset->addField("customer_{$key}", 'note', array(
        'name'  => "customer_{$key}",
        'label' => $this->__('Customer ID'),
        'title' => $this->__('Customer ID'),
        'text'  => $this->__($model->getId())
      ));
      $fieldset->addField("customer_{$key}_name", 'note', array(
        'name'  => "customer_{$key}_id",
        'label' => $this->__('Customer Name'),
        'title' => $this->__('Customer Name'),
        'text'  => $this->__($model->getName())
      ));
      $fieldset->addField("customer_{$key}_id", 'hidden', array(
        'name'  => "customer_{$key}_id",
        'value' => $model->getId()
      ));
    }

    $form->setUseContainer(true);
    $this->setForm($form);
    return parent::_prepareForm();
  }

}