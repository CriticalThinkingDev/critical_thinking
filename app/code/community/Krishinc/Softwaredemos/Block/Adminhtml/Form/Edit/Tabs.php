<?php

class Krishinc_Softwaredemos_Block_Adminhtml_Form_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('edit_home_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('softwaredemos')->__('Tabs'));
    }

    /**
     * add tabs before output
     *
     * @return Krishinc_Softwaredemos_Block_Adminhtml_Form_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('softwaredemos')->__('General'),
            'title'     => Mage::helper('softwaredemos')->__('General'),
            'content'   => $this->getLayout()->createBlock('softwaredemos/adminhtml_form_edit_tab_general')->toHtml(),
        ));

        $product_content = $this->getLayout()->createBlock('softwaredemos/adminhtml_form_edit_tab_product', 'softwaredemos_products.grid')->toHtml();
        $serialize_block = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serialize_block->initSerializerBlock('softwaredemos_products.grid', 'getSelectedProducts', 'products', 'selected_products');
        $serialize_block->addColumnInputName('position');
        $product_content .= $serialize_block->toHtml();
        $this->addTab('associated_products', array(
            'label'     => Mage::helper('softwaredemos')->__('Products'),
            'title'     => Mage::helper('softwaredemos')->__('Products'),
            'content'   => $product_content
        ));  
        


        

        return parent::_beforeToHtml();
    }

}