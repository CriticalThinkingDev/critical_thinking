<?php

class Krishinc_Mastergrouped_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMasterGroupedProductSearch($_product) {
        
        $block_catalogsearch = Mage::app()->getLayout()->createBlock(
                                    'mastergrouped/catalogsearch_list',
                                    'mastergrouped_catalogsearch',
                                    array('template' => 'mastergrouped/catalogsearch/list.phtml')
                                );
        $block_catalogsearch->assign('_product',$_product);
        return $block_catalogsearch->toHtml();
    }
    
     public function getFlagNotification($_product) {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('00:00:00')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                    
            $html = '';
        /* if($_product->getIsSale() == 1):
                $html .= '<img width="58" height="40" class="sale-icon" alt="Sale" src="'.Mage::getDesign()->getSkinUrl('images/icon_sale.png').'"/>';
        endif;
        
        if((strtotime($_product->getNewsFromDate()) <= strtotime($todayStartOfDayDate)) && (strtotime($_product->getNewsToDate()) >= strtotime($todayEndOfDayDate))): 
                $html .= '<img width="56" height="40" class="new-icon" alt="New" src="'.Mage::getDesign()->getSkinUrl('images/icon_new.png').'"/>';
        endif;*/
        
            if($_product->getCoreCurriculum() == 1):
                $html .= '<span class="core-curriculum-icon"> Full curriculum</span>';	
        endif;
            
            return $html;
    }
    
     public function hasFlagNotification($_product) {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('00:00:00')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                    
        $result = false;
        if(($_product->getIsSale() == 1) &&(!isset($_GET['is_sale']))):
            $result = true;
        endif;
        
        if((strtotime($_product->getNewsFromDate()) <= strtotime($todayStartOfDayDate)) && (strtotime($_product->getNewsToDate()) >= strtotime($todayEndOfDayDate))): 
            $result = true;
        endif;
        
        if($_product->getCoreCurriculum() == 1):
            $result = true;
        endif;
        
        return $result;
    }
}
