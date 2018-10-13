<?php
class Krishinc_Newarrival_Block_Newarrival extends Mage_Catalog_Block_Product_List
{
	 
    
	public function _prepareLayout()
    {  
		return parent::_prepareLayout();
    } 
    
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'newarrival/newarrival_toolbar';
   

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    function get_prod_count()
   {
      //unset any saved limits
      Mage::getSingleton('catalog/session')->unsLimitPage();
      return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : 40;
   }// get_prod_count

   function get_cur_page()
   {
      return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
   }// get_cur_page

    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {   
		$todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
  	    $order = 'news_from_date';
        $dir = 'desc';
        if($this->getRequest()->getParam('order'))
        {
	      $order = $this->getRequest()->getParam('order'); 
	      $dir = $this->getRequest()->getParam('dir');
        }
      
        
        

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort($order, $dir)
            ->setPageSize($this->get_prod_count())
			->setCurPage($this->get_cur_page()) 
        ;
	  $this->setProductCollection($collection); 
      return $collection;
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    
        $toolbar = $this->getToolbarBlock();
        

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
        	
            $toolbar->setAvailableOrders($orders);
           
        } 
        if ($sort = $this->getSortBy()) { 
          
            $toolbar->setDefaultOrder($sort);
            
        } 
        
        if ($dir = $this->getDefaultDirection()) {
 
            $toolbar->setDefaultDirection($dir);
          
        } 
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
          
        }
 
        // set collection to tollbar and apply sort
         
        $toolbar->setCollection($collection);     

        $this->setChild('toolbar', $toolbar);
        
        
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        )); 
 
        $this->_getProductCollection()->load();
        Mage::getModel('review/review')->appendSummary($this->_getProductCollection()); 
        
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }
    
  
     
    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
  
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    } 
     
    
}