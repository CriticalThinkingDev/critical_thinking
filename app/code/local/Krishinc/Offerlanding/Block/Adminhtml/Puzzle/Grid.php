<?php

class Krishinc_Offerlanding_Block_Adminhtml_Puzzle_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('puzzleGrid');
        $this->setDefaultSort('puzzle_id');
        $this->setDefaultDir('ASC');

        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('offerlanding/puzzle')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('offerlanding_id', array(
            'header'    => Mage::helper('offerlanding')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'offerlanding_id',
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('offerlanding')->__('First Name'),
            'align'     =>'left',
            'index'     => 'firstname',
        ));
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('offerlanding')->__('Last Name'),
            'align'     =>'left',
            'index'     => 'lastname',
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('offerlanding')->__('Email'),
            'align'     =>'left',
            'index'     => 'email',
        ));

        $this->addColumn('supply', array(
            'header'    => Mage::helper('offerlanding')->__('Supply'),
            'align'     =>'left',
            'index'     => 'supply',
        ));

        $this->addColumn('email_list', array(
            'header'    => Mage::helper('offerlanding')->__('Email List'),
            'align'     =>'left',
            'index'     => 'emaillist',
        ));
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('offerlanding')->__('Created Time'),
            'align'     =>'left',
            'index'     => 'created_time',
            'type'	  => 'datetime'
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('offerlanding')->__('Updated Time'),
            'align'     =>'left',
            'index'     => 'update_time',
            'type'	  => 'datetime'
        ));



        /*$this->addColumn('status', array(
            'header'    => Mage::helper('offerlanding')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));*/


        $this->addExportType('*/*/exportCsv', Mage::helper('offerlanding')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('offerlanding')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('puzzle_id');
        $this->getMassactionBlock()->setFormFieldName('puzzle');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('offerlanding')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('offerlanding')->__('Are you sure?')
        ));


        return $this;
    }



}