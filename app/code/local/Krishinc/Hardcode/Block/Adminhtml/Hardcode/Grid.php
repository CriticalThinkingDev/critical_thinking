<?php

class Krishinc_Hardcode_Block_Adminhtml_Hardcode_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('hardcodeGrid');
      $this->setDefaultSort('hardcode_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('hardcode/hardcode')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('hardcode_id', array(
          'header'    => Mage::helper('hardcode')->__('ID'),
          'align'     =>'right',
          'type'  => 'number',
          'width'     => '50px',
          'index'     => 'hardcode_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('hardcode')->__('Name'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $sattribute = Mage::getModel('catalog/resource_eav_attribute')->load(144);
      $sOptions =  $sattribute->getSource()->getAllOptions();
      $attributeOptions2 = array();
      foreach ($sOptions as $value) {
          if(!empty($value['value'])) {
              $attributeOptions2[$value['value']] = $value['label'];
          }

      }
      $this->addColumn('s_id',
          array(
              'header'=> Mage::helper('hardcode')->__('Subject'),
              'align'     =>'left',
              'index' => 's_id',
              'type'  => 'options',

              'options' =>$attributeOptions2,

          ));



      $gattribute = Mage::getModel('catalog/resource_eav_attribute')->load(143);
      $gOptions =  $gattribute->getSource()->getAllOptions();

      $attributeOptions3 = array();
      foreach ($gOptions as $value) {
          if(!empty($value['value'])) {
              $attributeOptions3[$value['value']] = $value['label'];
          }

      }
      $this->addColumn('g_id',
          array(
              'header'=> Mage::helper('hardcode')->__('Grade'),
              'align'     =>'left',
              'index' => 'g_id',
              'type'  => 'options',
              'options' =>$attributeOptions3,

          ));

      $this->addColumn('count', array(
          'header'    => Mage::helper('hardcode')->__('Number Of Products'),
          'align'     =>'left',
          'type'  => 'number',
          'index'     => 'count',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('hardcode')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('sstatus', array(
          'header'    => Mage::helper('hardcode')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'sstatus',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('hardcode')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('hardcode')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('hardcode')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('hardcode')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('hardcode_id');
        $this->getMassactionBlock()->setFormFieldName('hardcode');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('hardcode')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('hardcode')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('hardcode/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('hardcode')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('hardcode')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}