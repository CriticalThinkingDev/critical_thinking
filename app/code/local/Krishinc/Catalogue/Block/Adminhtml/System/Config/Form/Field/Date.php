<?php

/**
 * System configuration form field renderer for mapping Date and product fields with Magento
 * attributes.
 *
 * @category   Krishinc
 * @package    Krishinc_Catalogue
 * @author     Krishinc Team <info@krishinc.com>
 */
class Krishinc_Catalogue_Block_Adminhtml_System_Config_Form_Field_Date extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
  
//    protected $_selectRenderer;
//    protected $_selectMonthRenderer;
//
//    protected function _selectRenderer()
//    { 
//		
//        if (!$this->_selectRenderer) {
//            $this->_selectRenderer = $this->getLayout()->createBlock(
//                'catalogue/adminhtml_products', '',
//                array('is_render_to_js_template' => true)
//            );
//            $this->_selectRenderer->setClass('customer_group_select');
//            $this->_selectRenderer->setExtraParams('style="width:120px"');
//        } 
//        return $this->_selectRenderer; 
//    } 
//    protected function _selectMonthRenderer()
//    { 
//		
//        if (!$this->_selectMonthRenderer) {
//            $this->_selectMonthRenderer = $this->getLayout()->createBlock(
//                'catalogue/adminhtml_month', '',
//                array('is_render_to_js_template' => true)
//            );
//            $this->_selectMonthRenderer->setClass('customer_group_select1');
//            $this->_selectMonthRenderer->setExtraParams('style="width:120px"');
//        } 
//        return $this->_selectMonthRenderer; 
//    }
	/*
	* add/set data for column(type) to dynamic block 
	*/
    protected function _prepareToRender()
    {
	
       $this->addColumn('month', array(
            'label' => Mage::helper('catalogue')->__('Month'),
            'style' => 'width:120px',
        ));  
         
    
        $this->addColumn('product', array(
            'label' => Mage::helper('catalogue')->__('Product'), 
            'style' => 'width:120px',
        ));
        $this->_addAfter = false; 
        $this->_addButtonLabel = Mage::helper('catalogue')->__('Add Field');
    }
	/*
	* render input for all column
	*/
 	public function _renderCellTemplate($columnName)
	{
				
		$column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

	 
		if ($columnName=="product") {
			return $this->_getSearchFieldRenderer()
					->setName($inputName.'[]')
					->setTitle($columnName)
					->setExtraParams('style="width:160px"')
					//->setExtraParams('multiple="multiple"')
					->setOptions()
					->toHtml();
		} else if($columnName == "month") {
 

	 
		 	return $this->_getSearchMonthFieldRenderer()
					->setName($inputName.'[]')
					->setTitle($columnName)
					->setExtraParams('style="width:160px"')
					//->setExtraParams('multiple="multiple"')
					->setOptions()
					->toHtml();
		} 
		 

		 
		//return parent::_renderCellTemplate($columnName);
	} 
	/*
	* set row data for dynamic content on edit action
	*/
	protected function _prepareArrayRow(Varien_Object $row)
    {
			
		$product = $row->getData('product');
		
		foreach($product as $pro)
		{
			$row->setData(
			'option_extra_attr_' . $this->_getSearchFieldRenderer()->calcOptionHash(
								   $pro),
			'selected="selected"'
			);
		}
		$month = $row->getData('month'); 
		foreach($month as $mnth)
		{
			$row->setData(
			'option_extra_attr_' . $this->_getSearchMonthFieldRenderer()->calcOptionHash(
								   $mnth),
			'selected="selected"'
			);
		}
		 
    }
	
    /**
	 * Get select block for type
	 */
//	protected function _getTypeRenderer()
//	{
//	  if (!$this->_typeRenderer) {
//		  $this->_typeRenderer = $this->getLayout()
//				  ->createBlock('catalogue/adminhtml_products')
//				  ->setIsRenderToJsTemplate(true);           
//	  }
//	  return $this->_typeRenderer;
//	}
 
	/**
	 * Get select block for search field
	 *
	 * @return Msat_Yourmodule_Block_Select
	 */
	protected function _getSearchFieldRenderer()
	{
		if (!$this->_searchFieldRenderer) {
			$this->_searchFieldRenderer = $this->getLayout()
					->createBlock('catalogue/adminhtml_products')
					->setIsRenderToJsTemplate(true);           
		}
		return $this->_searchFieldRenderer;
	}
	/**
		 * Get select block for search field
		 *
		 * @return Msat_Yourmodule_Block_Select
		 */
		protected function _getSearchMonthFieldRenderer()
		{
			if (!$this->_searchMonthFieldRenderer) {
				$this->_searchMonthFieldRenderer = $this->getLayout()
						->createBlock('catalogue/adminhtml_month')
						->setIsRenderToJsTemplate(true);           
			}
			return $this->_searchMonthFieldRenderer; 
		}

}