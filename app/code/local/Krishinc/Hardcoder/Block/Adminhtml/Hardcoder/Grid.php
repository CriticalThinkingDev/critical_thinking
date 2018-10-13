<?php

class Krishinc_Hardcoder_Block_Adminhtml_Hardcoder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('hardcoderGrid');
      $this->setDefaultSort('hardcoder_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('hardcoder/hardcoder')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('hardcoder_id', array(
          'header'    => Mage::helper('hardcoder')->__('ID'),
          'align'     =>'right',
          'type'  => 'number',
          'width'     => '50px',
          'index'     => 'hardcoder_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('hardcoder')->__('Name'),
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
              'header'=> Mage::helper('hardcoder')->__('Subject'),
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
              'header'=> Mage::helper('hardcoder')->__('Grade'),
              'align'     =>'left',
              'index' => 'g_id',
              'type'  => 'options',
              'options' =>$attributeOptions3,

          ));

 $this->addColumn('product_type1', array(
          'header'            => Mage::helper('catalog')->__('Product Type'),
          'name'              => 'product_type1',
          'width'             => 60,
          'type'              => 'text',
       
          'renderer' => new Krishinc_Hardcoder_Block_Renderer_Type,
         
          
      ));

      $this->addColumn('count', array(
          'header'    => Mage::helper('hardcoder')->__('Number Of Products'),
          'align'     =>'left',
          'type'  => 'number',
          'index'     => 'count',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('hardcoder')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('sstatus', array(
          'header'    => Mage::helper('hardcoder')->__('Status'),
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
                'header'    =>  Mage::helper('hardcoder')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('hardcoder')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('hardcoder')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('hardcoder')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('hardcoder_id');
        $this->getMassactionBlock()->setFormFieldName('hardcoder');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('hardcoder')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('hardcoder')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('hardcoder/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('hardcoder')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('hardcoder')->__('Status'),
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
