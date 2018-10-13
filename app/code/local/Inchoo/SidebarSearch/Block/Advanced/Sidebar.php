<?php
class Inchoo_SidebarSearch_Block_Advanced_Sidebar extends Mage_CatalogSearch_Block_Advanced_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sidebarsearch/advanced/sidebar.phtml');
    }
    
	public function _prepareLayout()
	{
		// don't change crumbs!
	}
	
    public function getAttributeSelectElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);
        $name = $attribute->getAttributeCode();
        // 2 - avoid yes/no selects to be multiselects
        /*
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
        */
        	$attributeName = $this->getAttributeLabel($attribute);
        	if($attribute->getAttributeCode() == 'product_type'){
                   //$attributeName = 'Product';
                   $attributeName = 'Books, eBooks, & App';
                }
 if($attribute->getAttributeCode() == 'product_type'){
array_unshift($options, array('value'=>'all', 'label'=>Mage::helper('catalogsearch')->__(''.$attributeName).'s'));
            }else{
array_unshift($options, array('value'=>'all', 'label'=>Mage::helper('catalogsearch')->__('All '.$attributeName).'s'));
            }

            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('catalogsearch')->__(''.$this->getAttributeLabel($attribute))));
       // }
       
	if(Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
 
			if($attribute->getAttributeCode() == 'product_type'){

				unset($options[0]);
 

			}
		}
        return $this->_getSelectBlock()
            ->setName($name.'[]')
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams($extra)
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
			->setClass('multiselect')
            ->getHtml();
    }

 public function getAttributeSelectElementNavMobile($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);
        $name = $attribute->getAttributeCode();
        // 2 - avoid yes/no selects to be multiselects
        /*
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
        */
        	$attributeName = $this->getAttributeLabel($attribute);
        	if($attribute->getAttributeCode() == 'product_type'){
                   //$attributeName = 'Product';
                   $attributeName = 'Books, eBooks, & App';
                }
 if($attribute->getAttributeCode() == 'product_type'){
array_unshift($options, array('value'=>'all', 'label'=>Mage::helper('catalogsearch')->__(''.$attributeName).'s'));
            }else{
array_unshift($options, array('value'=>'all', 'label'=>Mage::helper('catalogsearch')->__('All '.$attributeName).'s'));
            }

            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('catalogsearch')->__(''.$this->getAttributeLabel($attribute))));
       // }
       
	//if(Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
 
			if($attribute->getAttributeCode() == 'product_type'){

				unset($options[0]);
 

			}
	//	}
        return $this->_getSelectBlock()
            ->setName($name.'[]')
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams($extra)
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
			->setClass('multiselect')
            ->getHtml();
    }
    
    /**
     * ***Added for refine filter
     *
     * @return unknown
     */
    public function isRefineAvailable()
    {
    	if((Mage::app()->getRequest()->getModuleName()=='catalogsearch') && (Mage::app()->getRequest()->getControllerName()=='advanced') &&(Mage::app()->getRequest()->getActionName()=='result'))
    	{
    		return true;
    	/*} elseif((Mage::app()->getRequest()->getModuleName()=='catalogsearch') && (Mage::app()->getRequest()->getControllerName()=='result') &&(Mage::app()->getRequest()->getActionName()=='index'))
    	{
    		return true;*/
    	
    	}else {
    		return false;
    	}
    }
    
     /**
     * ***Added for refine filter to get url for 'is_sale','core_curriculum','new','awd','all'
     *
     * @return unknown
     */
    public function getBaseRefineUrl($code)
    { 
    	$params = $this->getRequest()->getParams();
    	if((Mage::app()->getRequest()->getModuleName()=='catalogsearch') && (Mage::app()->getRequest()->getControllerName()=='advanced') &&(Mage::app()->getRequest()->getActionName()=='result'))
    	{
    		$strArr = array('is_sale','core_curriculum','new','awd','all');
    		foreach ($strArr as $val) {
    			if(array_key_exists($val,$params))
    			{
    				unset($params[$val]);
    			}
    		}
    		if(array_key_exists('p',$params)) {	unset($params['p']); }
    		if(array_key_exists('limit',$params)) {	unset($params['limit']); }
    		if($code != 'all')
    		{
    			$params[$code] = 1;
    		}
    		 
    		$resultUrl = Mage::getUrl('catalogsearch/advanced/result');
    		 
    		if($total = sizeof($params))
    		{
    			 
    			$resultUrl.='?';
    			$i=1;
	    		foreach ($params as $key => $val)
	    		{	 
	    			if(is_array($val)){
	    				$resultUrl	.=$key .'[]='.$val[0];
	    			} else {
	    				$resultUrl	.=$key .'='.$val;
	    			}
	    			 if($i++ < $total)
	    			 {
	    			 	$resultUrl .='&';
	    			 }
	    		}
    		}
    		return $resultUrl;
 
    	} elseif((Mage::app()->getRequest()->getModuleName()=='catalogsearch') && (Mage::app()->getRequest()->getControllerName()=='result') &&(Mage::app()->getRequest()->getActionName()=='index'))
    	{
    		return $baseUrl = Mage::getUrl('catalogsearch/advanced/result').'?name='.$this->getRequest()->getParam('q').'&'.$code.'=1';
    		 
    	} else{
    		return false;
    	}
    }
	
}
