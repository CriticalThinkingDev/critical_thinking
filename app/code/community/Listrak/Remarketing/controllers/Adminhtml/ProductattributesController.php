<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.0.0
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2011 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Adminhtml_ProductAttributesController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('remarketing');
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();

        try {
            // before we display the data, make sure all the product attribute sets are found in our table
            // that is done because we can't have null primary keys - something that would happen otherwise
            Mage::helper('remarketing/product_attribute_set_map')->ensureDataConsistency();

            // all is consistent - load the information and the UI
            $sets = Mage::getModel('listrak/product_attribute_set_map')
                ->getCollection()
                ->addAttributeSetName()
                ->addAttributeNames()
                ->orderByAttributeSetName();

            Mage::register('productattribute_sets', $sets);

            $this->_addContent($this->getLayout()->createBlock('remarketing/adminhtml_productattributes'));

            $this->renderLayout();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function editAction()
    {
        $this->_initAction();

        try {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('listrak/product_attribute_set_map')
                ->getCollection()
                ->addAttributeSetName()
                ->addMapIdFilter($id)
                ->getFirstItem();

            Mage::register('productattribute_data', $model);

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('remarketing/adminhtml_productattributes_edit'))
                ->_addLeft($this->getLayout()->createBlock('remarketing/adminhtml_productattributes_edit_tabs'));

            $this->renderLayout();
        } catch (Exception $e) {
            Mage::getModel('listrak/log')->addException($e);
            Mage::getSingleton('adminhtml/session')->addError(
                "An unexpected error occurred while attempting to display the form. Please try again."
            );
            $this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();
                $id = $this->getRequest()->getParam('id');

                $model = Mage::getModel('listrak/product_attribute_set_map')
                    ->load($id);

                // now we know if there is a record and if there isn't, it will be inserted
                $categoriesSource = array_key_exists('categories_source', $postData)
                    ? $postData['categories_source'] : null;
                $useConfigCategoriesSource = array_key_exists('use_config_categories_source', $postData)
                    && $postData['use_config_categories_source'] ? 1 : 0;
                $categoryAttributeCode = $postData['categories_category_attribute'] == ''
                    ? null : $postData['categories_category_attribute'];
                $subcategoryAttributeCode = $postData['categories_subcategory_attribute'] == ''
                    ? null : $postData['categories_subcategory_attribute'];
                $model->setBrandAttributeCode($postData['brand_attribute'] == '' ? null : $postData['brand_attribute'])
                    ->setCategoriesSource($categoriesSource)
                    ->setUseConfigCategoriesSource($useConfigCategoriesSource)
                    ->setCategoryAttributeCode($categoryAttributeCode)
                    ->setSubcategoryAttributeCode($subcategoryAttributeCode)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully saved')
                );

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getModel('listrak/log')->addException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    "An unexpected error occurred while attempting to save the settings. Please try again."
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function bulkassignAction()
    {
        try {
            $postData = $this->getRequest()->getPost();
            if (array_key_exists('bulkassign_attribute', $postData) && $postData['bulkassign_attribute'] != '') {
                $attributeCode = $postData['bulkassign_attribute'];

                $sets = Mage::getModel('listrak/product_attribute_set_map')
                    ->getCollection();


                foreach ($sets as $set) {
                    $productAttributeCollectionCount = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->setAttributeSetFilter($set->getAttributeSetId())
                        ->addVisibleFilter()
                        ->setCodeFilter($attributeCode)
                        ->count();
                    if ($set->getBrandAttributeCode() == null
                        && $productAttributeCollectionCount > 0
                    ) {
                        $set->setBrandAttributeCode($attributeCode)
                            ->save();
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getModel('listrak/log')->addException($e);
            Mage::getSingleton('adminhtml/session')->addError(
                "An unexpected error occurred while assigning the brand attribute."
            );
        }

        $this->_redirect('*/*/');
    }
}

