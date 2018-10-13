<?php

class Krishinc_Softwaredemos_Block_Adminhtml_Softwaredemos_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
{

    parent::__construct();
    $this->setId('softwaredemosGrid');
    $this->setDefaultSort('softwaredemos_id');
    $this->setDefaultDir('ASC');
    $this->setSaveParametersInSession(true);
}

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('softwaredemos/softwaredemos')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

  protected function _prepareColumns()
  {
  
      $this->addColumn('softwaredemos_id', array(
          'header'    => Mage::helper('softwaredemos')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'softwaredemos_id',
      ));

      

	  
	    $this->addColumn('softname', array(
          'header'    => Mage::helper('softwaredemos')->__('Software demo name'),
          'align'     =>'left',
          'index'     => 'softname',
      ));
       /*Subject Id*/
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', 'subject');
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        $subject = array('-1'=>'Select Subject');
        $subjectcollection = $attribute->getSource()->getAllOptions(false);
        foreach ($subjectcollection as $subjectcoll){
            $subject[$subjectcoll['value']]= $subjectcoll['label'];
        }
        /**/ 
      
      $this->addColumn('subject_id', array(
			'header'    => Mage::helper('softwaredemos')->__('Subject'),
			'width'     => '150px',
			'index'     => 'subject_id',
			'type'		=> 'options',
			'options'	=> $subject
			
      ));
       $this->addColumn('type', array(
          'header'    => Mage::helper('softwaredemos')->__('Software Type'),
          'align'     => 'left',
          'width'     => '120px',
          'index'     => 'type',
          'type'      => 'options',
          'options'   => array(
              'play' => 'Play Online Demo',
              'download' => 'Download Demo',
              
          ),
      ));
      
	   $this->addColumn('sort_order', array(
			'header'    => Mage::helper('softwaredemos')->__('Sort Order'),
			'width'     => '150px',
			'index'     => 'sort_order',  
      ));
	  

//      $this->addColumn('status', array(
//          'header'    => Mage::helper('softwaredemos')->__('Status'),
//          'align'     => 'left',
//          'width'     => '80px',
//          'index'     => 'status',
//          'type'      => 'options',
//          'options'   => array(
//              1 => 'Enabled',
//              2 => 'Disabled',
//          ),
//      ));
	
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('softwaredemos')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('softwaredemos')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ) 
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('softwaredemos')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('softwaredemos')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('softwaredemos_id');
        $this->getMassactionBlock()->setFormFieldName('softwaredemos');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('softwaredemos')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('softwaredemos')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('softwaredemos/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('softwaredemos')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('softwaredemos')->__('Status'),
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