<?php
class Krishinc_Softwaredemos_Block_Softwaredemos extends Mage_Core_Block_Template
{

	public function __construct()
    {
        parent::__construct();
		$storeId = Mage::app()->getStore()->getId();
		$attributeValuetTable =  (string)Mage::getConfig()->getTablePrefix() . 'eav_attribute_option_value';
        $collection = Mage::getModel('softwaredemos/softwaredemos')->getCollection();

      	$collection->getSelect()
          ->join(
		      array('eb' => $attributeValuetTable),'main_table.subject_id  = eb.option_id',array('eb.value_id','eb.value'))
			   ->where('eb.store_id = '.$storeId)
               ->order('main_table.subject_id DESC')
               ->order(' main_table.sort_order ASC');
			 $this->setCollection($collection);
    }
  

	public function _prepareLayout()
    {
		
		return parent::_prepareLayout();
	    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
	
	 public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
	public function getPrintableSoftwaredemos()
	{
		$storeId = Mage::app()->getStore()->getId();
		$attributeValuetTable =  (string)Mage::getConfig()->getTablePrefix() . 'eav_attribute_option_value';
        $collection = Mage::getModel('softwaredemos/softwaredemos')->getCollection();
      	$collection->getSelect()
          ->join(
		      array('eb' => $attributeValuetTable),'main_table.subject_id  = eb.option_id',array('eb.value_id','eb.value'))
			   ->where('eb.store_id = '.$storeId)
			   ->where('eb.option_id IN(?)', array(121,123,122))
               ->order(new Zend_Db_Expr("FIELD(main_table.subject_id , 123,121,122)"));   
			 return  $collection;
	}
    
    public function getSoftwareDemosCount($valueId)
    {
    	   $count = Mage::getModel('softwaredemos/softwaredemos')->getCollection()->addFieldToSelect('subject_id')->addFieldToFilter('subject_id',(int)$valueId);
 
    	   return sizeof($count);
    }
    
    public function getProductCollectionBySoftwaredemo($softwaredemoID)
    {
    	$collection = Mage::getModel('softwaredemos/softwaredemos_product')
    					->getCollection()
    					->addFieldToSelect('product_id')
    					->addFieldToFilter('softwaredemos_id',$softwaredemoID)->setOrder('position','asc');
		return $collection->getData();
		
    }
    
    public function getProductCollectionOrderByGrades($grade)
    {
        try {
/*            $collection = Mage::getModel('softwaredemos/softwaredemos')->getCollection();
            $collection->getSelect()
                ->join(
                    array('sp' => 'softwaredemos_product'),'main_table.softwaredemos_id  = sp.softwaredemos_id',array('sp.*'));
            //$collection->getSelect()->where('main_table.grades like ? ','%'.$grade.'%');
            $collection->getSelect()->where("FIND_IN_SET('".$grade."', main_table.grades)");
            $collection->getSelect()->group('main_table.softwaredemos_id');*/

            $collection = Mage::getModel('softwaredemos/softwaredemos_product')->getCollection();
            $collection->getSelect()->join(
                array('sd' => 'softwaredemos'),'main_table.softwaredemos_id  = sd.softwaredemos_id',array('sd.*'));
            $collection->getSelect()->where("FIND_IN_SET('".$grade."', sd.grades)");
	    

            if(count($collection)>0)
            {
                return $collection;
            }

            return false;
        } catch(Exception $e) {
            echo $grade . "---". $e->getMessage();
        }

    }
    
    public function getGrageProductsHtml() {
	$block = $this->getLayout()->createBlock(
                    'softwaredemos/freeonlineplay_gradeproducts',
                    'gradeproducts',
                    array('template' => 'softwaredemos/freeonlineplay/gradeproducts.phtml')
                    );
       
        $html = $block->toHtml();
	return $html;
    }
}
