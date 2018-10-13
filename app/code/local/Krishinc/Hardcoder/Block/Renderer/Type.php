<?php

class Krishinc_Hardcoder_Block_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
       if($row->getData('p_id')){
           $pIds = explode(',', $row->getData('p_id'));

           $productModel = Mage::getModel('catalog/product');
           $attr = $productModel->getResource()->getAttribute("product_type");
           $merge = '';
           foreach($pIds as $pId){
               $merge  .= $attr->getSource()->getOptionText($pId);
               $merge  .= ', ';

           }
           return $merge;
       }


    }

}