<?php
class Krishinc_Award_Block_Adminhtml_Award_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('awardGrid');
        // This is the primary key of the database
        $this->setDefaultSort('award_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
   protected function _prepareCollection() {

        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                        ->setCodeFilter('award')->getFirstItem();
        
		$collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setPositionOrder('asc')
                        ->setAttributeFilter($attributeInfo->getAttributeId())
                        ->setStoreFilter(Mage::app()->getStore()->getId());
        
		$collection->getSelect()->joinLeft(
                array('award' => $collection->getTable('award/award')),
                'award.award_option_id = main_table.option_id', 
                array('award.name as award_name','award.award_url as award_url','award.image as image','award.awarddate as awarddate','award.is_companyaward as is_companyaward')
        ); 
	 
		$session = Mage::getSingleton('adminhtml/session');
		if($this->getRequest()->getParam('dir'))
			$dir=$this->getRequest()->getParam('dir');
		else
			$dir=(($awardGrid=$session->getData('awardGrid')) ? $awardGrid : 'ASC');
	
		if($session->getData('awardGridsort'))
			$awardGridsort = $session->getData('awardGridsort');
		else 
			$awardGridsort = 'main_table.option_id';
	
		if($sort=$this->getRequest()->getParam('sort'))
			$collection->getSelect()->order("$sort $dir");
		else
			$collection->getSelect()->order("$awardGridsort $dir");

				
		$this->setCollection($collection);
        return parent::_prepareCollection(); 
    } 
 
     
    protected function _prepareColumns() {

    	$this->addColumn('option_id', array(
            'header' => Mage::helper('award')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'option_id',
            'filter_index' => 'main_table.option_id',
            'type'  => 'number',
            'sortable'  => true
        ));
        
        $this->addColumn('default_value', array(
            'header' => Mage::helper('award')->__('Name'),
            'align' => 'left',
            'index' => 'default_value',
            'filter_index' => 'award.name', 
            'sortable'  => true
        ));        
        
        $this->addColumn('award_url', array(
            'header' => Mage::helper('award')->__('Link'),
            'align' => 'left',
            'index' => 'award_url',
            'filter_index' => 'award.award_url',
            'sortable'  => true  
        ));
        
        $this->addColumn('image', array(
            'header' => Mage::helper('award')->__('Image'),
         		'width' => '75px',
                'index' => 'award.image',
                'filter'    => false,
                'sortable'  => false,
           		'renderer'  => 'award/renderer_image',
        ));
       $this->addColumn('awarddate', array(
          'header'    => Mage::helper('award')->__('Date'), 
          'align'     =>'left',
          'index'     => 'awarddate',
          'type'      => 'date',
      )); 
        $this->addColumn('is_companyaward', array(
          'header'    => Mage::helper('award')->__('Company Award'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_companyaward',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              2 => 'No',
          ),
      ));     
        $this->addColumn('action',
                array(
                    'header' => Mage::helper('award')->__('Action'),
                    'width' => '100',
                    'type' => 'action',
                    'getter' => 'getId',
            	    'actions'   => array(
	                    array(
	                        'caption'   => Mage::helper('award')->__('Delete'),
	                        'url'       => array('base'=> '*/*/delete'),
	                        'field'     => 'id',
	                        'confirm'  => Mage::helper('award')->__('Are you sure?')
	                    )
	                ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        )); 

        $this->addExportType('*/*/exportCsv', Mage::helper('award')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('award')->__('XML'));

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
            $this->setMassactionIdField('award_option_id');
            $this->getMassactionBlock()->setFormFieldName('award');

            $this->getMassactionBlock()->addItem('delete', array(
                 'label'    => Mage::helper('award')->__('Delete'),
                 'url'      => $this->getUrl('*/*/massDelete'),
                 'confirm'  => Mage::helper('award')->__('Are you sure?')
            ));

           // $statuses = Mage::getSingleton('award/status')->getOptionArray();

           
            return $this;
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getAllManu()     
    {       
        $product = Mage::getModel('catalog/product');       
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', 'award');      
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());       
        $award = $attribute->getSource()->getAllOptions(false);      
         return $award;                  
     }
     
     
  }