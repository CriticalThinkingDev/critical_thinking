<?php
require_once(Mage::getModuleDir('controllers','Mage_CatalogSearch').DS.'AdvancedController.php');
class Krishinc_General_AdvancedController extends Mage_CatalogSearch_AdvancedController
{
    public function resultAction()
    {
        $this->loadLayout();
        try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
            $this->_redirectError(
                Mage::getModel('core/url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $searchCriterias =  Mage::getSingleton('catalogsearch/advanced')->getSearchCriterias();
        $middle = ceil(count($searchCriterias) / 2);
        $left = array_slice($searchCriterias, 0, $middle);
        $right = array_slice($searchCriterias, $middle);
        $searchCriteriasCombine = array_merge($left,$right);

        $i=1;  $cnt = sizeof($searchCriteriasCombine);
        $pageTitle = '';
        foreach($searchCriteriasCombine as $criteria):
            if((strtolower($criteria['value']) == 'yes') || (strtolower($criteria['value']) == 'no')):
                $pageTitle .= Mage::helper('core')->escapeHtml($criteria['name']);
            else:
                $pageTitle .= Mage::helper('core')->escapeHtml($criteria['value']);
            endif;
            if($i++ < $cnt): $pageTitle .= ' : ';endif;
        endforeach;
        if($pageTitle){
            $pageTitle .=Mage::helper('core')->escapeHtml(' - The Critical Thinking Co.™');
            $this->getLayout()->getBlock('head')->setTitle($pageTitle);
        }
      


        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

}