<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                    $this->addModelTags($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        $controller = Mage::app()->getRequest()->getControllerName();


        $action = Mage::app()->getRequest()->getActionName();

        $moduleName = Mage::app()->getRequest()->getModuleName();
        if($controller=='advanced' && $action=='result' && $moduleName=='catalogsearch') {
            $query = Mage::app()->getRequest()->getQuery();
            $searchCriteria = Mage::getSingleton('catalogsearch/advanced')->getSearchCriterias();
            $grade = '';
            if(isset($query['grade']['0'])){
                $grade = $query['grade']['0'];
            }

            $subject ='';
            if(isset($query['subject']['0'])){
                $subject = $query['subject']['0'];
            }
            $typeP = '';
            if(isset($query['product_type']['0'])){
                $typeP = $query['product_type']['0'];
            }
            $order = $this->getRequest()->getParam('order');
            if (!$order) {
                $order = $this->getSortBy();
            }

            $dir = $this->getRequest()->getParam('dir');
            if (!$dir) {
                $dir = $this->getDefaultDirection();
            }

           // if ((empty($typeP) || $typeP=='all') && $order == 'priority') {
	   if ($order == 'priority') {


                $model = Mage::getModel('hardcode/hardcode')->getCollection()
                    ->addFieldToFilter('s_id', $subject)
                    ->addFieldToFilter('g_id', $grade)
                    ->addFieldToFilter('sstatus', 1);
                $content = $model->getFirstItem()->getContent();
                $content = Mage::helper('adminhtml/js')->decodeGridSerializedInput($content);

                if (count($content) > 0) {
                    $products = array();

                    foreach ($this->_productCollection as $product) {
                        $row = array();
                        $row['id'] = $product->getId();
                        $row['priority'] = $product->getPriority();

                        $findHard = $content[$product->getId()];
                        if($findHard){
                            if(!$findHard['position']){
                                $findHard['position'] = '0';
                            }
                            $row['hard_code'] = $findHard['position'];
                        }else{
                            $row['hard_code'] =499;
                        }

                        $products[] = $row;
                    }

                    $sort = array();
                    foreach ($products as $k => $v) {
                        $sort['priority'][$k] = $v['priority'];
                        $sort['hard_code'][$k] = $v['hard_code'];
                    }
                    if ($dir == 'desc') {
                        array_multisort($sort['hard_code'], SORT_ASC, $sort['priority'], SORT_DESC, $products);

                    } else {
                        array_multisort($sort['hard_code'], SORT_DESC, $sort['priority'], SORT_ASC, $products);
                    }


                    $allids = array();
                    foreach ($products as $n) {
                        $allids[] = $n['id'];
                    }

                    $collection = Mage::getModel('catalog/product')->getCollection();
                    $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                        ->setStore(Mage::app()->getStore())
                        ->addMinimalPrice()
                        ->addTaxPercents()
                        ->addStoreFilter();
                    $collection->addAttributeToFilter('entity_id', array('in' => $allids));
                    $collection->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $allids) . ')'));


                    /*$collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', array('in' => $allids));
                    $collection->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $allids) . ')'));*/

                    $this->_productCollection = $collection;

                }
            }
        }

        return $this->_productCollection;
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

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

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
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
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

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on product collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getProductCollection())
        );
    }
}
