<?php
/**
 * Open Commerce LLC Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Commerce LLC Commercial Extension License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.tinybrick.com/license/commercial-extension
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tinybrick.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this package to newer
 * versions in the future. 
 *
 * @category   TinyBrick
 * @package    TinyBrick_GiftCard
 * @copyright  Copyright (c) 2010 TinyBrick Inc. LLC
 * @license    http://www.tinybrick.com/license/commercial-extension
 */
class TinyBrick_GiftCard_Block_Adminhtml_Giftcard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('giftcardGrid');
      $this->setDefaultSort('status');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('giftcard/giftcard')->getCollection();
      $collection->getSelect()
      	->joinLeft(array('o' => (string)Mage::getConfig()->getTablePrefix().'sales_flat_order'),
      		'main_table.order_id = o.entity_id',
      		array('increment_id'));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
		$this->addColumn('giftcard_id', array(
		  'header'    => Mage::helper('giftcard')->__('ID'),
		  'width'     => '65px',
		  'align'     =>'left',
		  'index'     => 'giftcard_id',
		));
		$this->addColumn('number', array(
		  'header'    => Mage::helper('giftcard')->__('GC #'),
		  'width'     => '160px',
		  'align'     => 'left',
		  'index'     => 'number',
		));
		$this->addColumn('bal', array(
			'header'    => Mage::helper('giftcard')->__('Balance'),
			'width'     => '95px',
			'align'     =>'left',
			'index'     => 'bal',
		));

		$this->addColumn('type', array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type',
                'type'  => 'options',
                'options' => array(2 => 'Virtual', 1 => 'Physical'),
        ));
        $this->addColumn('shipped', array(
                'header'=> Mage::helper('catalog')->__('Shipped'),
                'width' => '60px',
                'index' => 'shipped',
                'type'  => 'options',
                'options' => array(0 => 'No', 1 => 'Yes'),
        ));
        $this->addColumn('increment_Id', array(
			'header'    => Mage::helper('giftcard')->__('Order Number'),
			'align'     =>'left',
			'index'     => 'increment_id',
		));
        $this->addColumn('created_at', array(
			'header'    => Mage::helper('giftcard')->__('Created'),
			'align'     =>'left',
			'index'     => 'created_at',
		));
		
		
		$this->addExportType('*/*/exportCsv', Mage::helper('giftcard')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('giftcard')->__('XML'));
		
		return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('giftcard');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('giftcard')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('giftcard')->__('Are you sure?')
        ));

/*
        $statuses = Mage::getSingleton('recipe/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('recipe')->__('Change status'),
             'url'  => $this->getUrl('/*//*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('recipe')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
*/
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}