<?php
/**
 * Campaign Campaign associate products tab grid block
 *
 * @module   Campaign
 * @author   Ktpl
 */
class Krishinc_Hardcode_Block_Adminhtml_Hardcode_Edit_Tab_Associatedproducts extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('hardcoded_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(array('in_products'=>1));
        $this->setSaveParametersInSession(false);

        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag

        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedCustomers();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if($this->getRequest()->getParam('id')) {
            $data = $this->getSubjectGrade();
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*');
            if ($data['subject']) {
                $allowedSubject = array(
                    array(
                        "finset" => array($data['subject'])
                    )
                );

                $collection->addFieldToFilter('subject', $allowedSubject);
            }

            if ($data['grade']) {
                $allowedGrade = array(
                    array(
                        "finset" => array($data['grade'])
                    )
                );

                $collection->addFieldToFilter('grade', $allowedGrade);
            }

            $collection->addAttributeToFilter('status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

            $collection->addAttributeToFilter('visibility', array('in' => array(3, 4)));

        }else{
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', 99898);
        }
        $this->setCollection($collection);
        //p($collection->getFirstItem()->getData());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->getSelectedAssociatedproductsc();

            $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'customer',
                'values'            =>$this->_getSelectedCustomers(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku',

        ));

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();
        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ));
        $sattribute = Mage::getModel('catalog/resource_eav_attribute')->load(144);
        $sOptions =  $sattribute->getSource()->getAllOptions();
        $attributeOptions2 = array();
        foreach ($sOptions as $value) {
            if(!empty($value['value'])) {
                $attributeOptions2[$value['value']] = $value['label'];
            }

        }
        $this->addColumn('subject',
            array(
                'header'=> Mage::helper('catalog')->__('Subject'),
                'width' => '60px',
                'index' => 'subject',
                'type'  => 'options',
                'renderer' => new Inchoo_Productfilter_Block_Renderer_Subject,
                'options' =>$attributeOptions2,
                'filter_condition_callback' => array($this, '_filterCondition'),
            ));

        $gattribute = Mage::getModel('catalog/resource_eav_attribute')->load(143);
        $gOptions =  $gattribute->getSource()->getAllOptions();

        $attributeOptions3 = array();
        foreach ($gOptions as $value) {
            if(!empty($value['value'])) {
                $attributeOptions3[$value['value']] = $value['label'];
            }

        }
        $this->addColumn('grade',
            array(
                'header'=> Mage::helper('catalog')->__('Grade'),
                'width' => '60px',
                'index' => 'grade',
                'type'  => 'options',
                'options' =>$attributeOptions3,
                'renderer' => new Inchoo_Productfilter_Block_Renderer_Grade,
                'filter_condition_callback' => array($this, '_filterCondition'),
            ));

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'renderer' => new Krishinc_Hardcode_Block_Renderer_Position,
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/associatedproductsgrid', array('_current'=>true));
    }
    public function _getSelectedCustomers()   // Used in grid to return selected customers values.
    {
        $customers = array_keys($this->getSelectedCustomers());
        return $customers;
    }

    public function getSelectedCustomers()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('hardcode/hardcode')->load($id);
        $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput( $model->getContent());
       return $data;
    }
    protected function _getSelectedProducts()
    {

            $products = array_keys($this->getSelectedAssociatedproductsc());

        return $products;
    }

    protected function getSelectedAssociatedproducts()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('hardcode/hardcode')->load($id);
        $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput( $model->getContent());
         $m = array_keys($data);
        return $m;
       return $data;
    }

    protected function getSelectedAssociatedproductsc()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('hardcode/hardcode')->load($id);
        $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput( $model->getContent());


        return $data;
    }
    public function getSubjectGrade(){
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('hardcode/hardcode')->load($id);
        return array('subject'=>$model->getSId(),'grade'=>$model->getGId());
    }

    protected function _filterCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addAttributeToFilter($column->getId(), array('finset' => $value));
    }

}