<?php
class Krishinc_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_Abstract
{
	
	
	public function __construct()
	{
		parent::__construct(); 
	}
	
	public function getCollectionBySubjectFilter($subjectId)
	{
		$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getResourceModel('reports/product_collection')
					->addOrderedQty()
					->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'description', 'grade','subject','award','available_text'));
		if(!empty($subjectId))
		{
				$products = $products->addAttributeToFilter('subject',$subjectId);	
		}
		
		$products = $products->setStoreId($storeId)
					->addStoreFilter($storeId)
					->setOrder('ordered_qty', 'desc');//->setPageSize(10);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		return $products;	   
	}
	
	public function getSubjectOptions()
	{
		$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'subject');
		if ($attribute->usesSource()) {
		    $options = $attribute->getSource()->getAllOptions(false);
		}
		return $options;
	}
	
	public function getCollectionByProductTypeFilter($productTypeId)
	{

$query = Mage::app()->getRequest()->getQuery();
       $grade = '';
        if(isset($query['grade']['0'])){
            $grade = $query['grade']['0'];
        }

        $subject = '';
        if(isset($query['subject']['0'])){
            $subject = $query['subject']['0'];
        }
        $typeP = '';
        if(isset($query['product_type']['0'])){
            $typeP = $query['product_type']['0'];
        }
        if($typeP==125 || $typeP==287){
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', 99898);
            return $products;
        }


       
      
        $model = Mage::getModel('hardcoder/hardcoder')->getCollection()
            ->addFieldToFilter('s_id', $subject)
            ->addFieldToFilter('g_id', $grade)
           ->addFieldToFilter('sstatus', 1);


 $allowedType = array(
            array(
                "finset" => array($typeP)
            ),

        );

	if ($typeP!='all' && isset($typeP) ){
	 $model->addFieldToFilter('p_id', $allowedType);
        }
      /* if ((empty($typeP) || $typeP=='all')){
            

        }else{
        $model->addFieldToFilter('p_id', $allowedType);
         }*/
	
        $content = $model->getFirstItem()->getContent();
        $content = Mage::helper('adminhtml/js')->decodeGridSerializedInput($content);
        if (count($content) > 0) {
            $products = array();

            foreach ($content as $k => $v) {

                $row = array();
                $row['id'] =$k;
 		if(!$v['position']){
                               $v['position'] = '0';
                           }
                $row['priority'] =$v['position'];
                $products[] = $row;
            }
            
            $sort = array();
            foreach ($products as $k => $v) {

                $sort['hard_code'][$k] = $v['priority'];
            }
            array_multisort($sort['hard_code'], SORT_ASC, $products);
            $allids = array();
            foreach ($products as $n) {
                $allids[] = $n['id'];
            }
           
            $products = Mage::getModel('catalog/product')->getCollection();
            $products->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setStore(Mage::app()->getStore())
                ->addMinimalPrice()
                ->addTaxPercents()
                ->addStoreFilter();
            $products->addAttributeToFilter('entity_id', array('in' => $allids));
            $products->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $allids) . ')'));
            $products->getSelect()->limit(3);
            return $products;
        }
		
		$storeId = Mage::app()->getStore()->getId();
		$nProducts = '';
		$products = Mage::getResourceModel('reports/product_collection')
					->addOrderedQty()
					->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'description', 'grade','subject','award','product_type','available_text'));
		  
		if(!empty($productTypeId))
		{
		 	$products = $products->addAttributeToFilter('product_type',$productTypeId);	
		} 
		
		$products = $products->setStoreId($storeId)
					->addStoreFilter($storeId)
					->addAttributeToFilter('type_id',array('eq','simple'))
					//->setOrder('ordered_qty', 'desc')
					->setPageSize(3); 
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $products->getSelect()->Order('rand()');
		if(sizeof($products)== 0) { 
				$nProducts = Mage::getResourceModel('reports/product_collection')
					->addOrderedQty()
					->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'description', 'grade','subject','award','product_type','available_text'));
			$nProducts = $nProducts->setStoreId($storeId)
						->addStoreFilter($storeId)
						->addAttributeToFilter('type_id',array('eq','simple'))
						//->setOrder('ordered_qty', 'desc')
						->setPageSize(3); 
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($nProducts);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($nProducts);
			$nProducts->getSelect()->Order('rand()');
			return $nProducts; 
		}
		
		return $products;	   
	}
	
	
	
	/******************** Functions for downloadable product in listing page******/

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinksPurchasedSeparately($_product)
    {
        return $_product->getLinksPurchasedSeparately();
    }
    
    
     /**
     * Enter description here...
     *
     * @return array
     */
    public function getLinks($_product)
    {
        return $_product->getTypeInstance(true)
            ->getLinks($_product);
    }
	 
    public function hasLinks($_product)
    {
    	 return $_product->getTypeInstance(true)
            ->hasLinks($_product);
    }
    /**
     * Enter description here...
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link,$_product)
    {
        $price = $link->getPrice();
        $store = $_product->getStore();

        if (0 == $price) {
            return '';
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $taxHelper = Mage::helper('tax');
        $coreHelper = $this->helper('core');
        $_priceInclTax = $taxHelper->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = $taxHelper->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceInclTax, $store);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $coreHelper->currencyByStore($_priceExclTax, $store);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+'.$coreHelper
                    ->currencyByStore($_priceInclTax, $store).' '.$this->__('Incl. Tax').')';
            }
        }
        $priceStr .= '</span>';

        return $priceStr;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price,$_product)
    {
        $store = $_product->getStore();
        return $this->helper('core')->currencyByStore($price, $store, false);
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getJsonConfig($_product)
    {
        $config = array();
        $coreHelper = Mage::helper('core');

        foreach ($this->getLinks($_product) as $link) {
            $config[$link->getId()] = $coreHelper->currency($link->getPrice(), false, false);
        }

        return $coreHelper->jsonEncode($config);
    }

    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('downloadable/download/linkSample', array('link_id' => $link->getId()));
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle($_product)
    {
        if ($_product->getLinksTitle()) {
            return $_product->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

    /**
     * Returns whether link checked by default or not
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return bool
     */
    public function getIsLinkChecked($link,$_product)
    {
        $configValue = $_product->getPreconfiguredValues()->getLinks();
        if (!$configValue || !is_array($configValue)) {
            return false;
        }

        return $configValue && (in_array($link->getId(), $configValue));
    }

    /**
     * Returns value for link's input checkbox - either 'checked' or ''
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getLinkCheckedValue($link,$_product)
    {
        return $this->getIsLinkChecked($link, $_product) ? 'checked' : '';
    }
}
