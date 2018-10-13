<?php

class Krishinc_Hardcoder_Block_Renderer_Position extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $id = $this->getRequest()->getParam('id');

        $content = Mage::getModel('hardcoder/hardcoder')->load($id)->getContent();
        $content = Mage::helper('adminhtml/js')->decodeGridSerializedInput($content);
        $pId = $row->getData('entity_id');
        $position =$content[$pId]['position'];

        return $position."<input type='text' value='$position' name='position' class='input-text validate-number''>";


    }

}