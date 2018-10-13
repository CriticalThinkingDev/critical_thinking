<?php
class Krishinc_Softwaredemos_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout(); 
    }
    
    /* Removed by pankil - now this action will not be called because ajax is removed */
    //public function gradeproductsAction() {
    //    $result = array();
    //    
    //    $block = $this->getLayout()->createBlock(
    //                'softwaredemos/freeonlineplay_gradeproducts',
    //                'gradeproducts',
    //                array('template' => 'softwaredemos/freeonlineplay/gradeproducts.phtml')
    //                );
    //   
    //    $html = $block->toHtml();
    //    echo $html;
    //    exit;
    //}
}