<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Marketsuite_Adminhtml_FilterconfController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('marketsuite/managerules');
    }
    
    public function indexAction() {

        if (version_compare(Mage::getVersion(),'1.4.0.0', '>='))
            $this->_title($this->__('MSS'))->_title($this->__('Manage Rules'));

        $this->loadLayout()
                ->_setActiveMenu('marketsuite')
                ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        if (version_compare(Mage::getVersion(),'1.4.0.0', '>='))
            $this->_title($this->__('MSS'))->_title($this->__('Manage Rules'));

        $id = $this->getRequest()->getParam('id');

        /* Model must extend from Mage_Rule_Model_Rule */
        $model = Mage::getModel('marketsuite/filter');

        if ($id) {
            $model->load($id);
            if (! $model->getFilterId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketsuite')->__('This filter no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('marketsuitefilters_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('marketsuite');

        $block = $this->getLayout()->createBlock('marketsuite/adminhtml_filterconf_edit')
            ->setData('action', $this->getUrl('*/marketsuite_filterconf/save'));

        /*Load needed js to head of layout*/
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('marketsuite/adminhtml_filterconf_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
                    
            $redirectBack   = $this->getRequest()->getParam('back', false);
            try {
                $model = Mage::getModel('marketsuite/filter');
                /* Disabled for Magento 1.3.1.1 */
                if (Mage::getVersion() != '1.3.1.1')
                {
                    if ($id = $this->getRequest()->getParam('filter_id')) {
                        $model->load($id);
                        if ($id != $model->getId()) {
                            Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
                        }
                    }
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $model->loadPost($data);

                if ($this->getRequest()->getParam('_save_as_flag'))
                    $model->setId(null);
                
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalogrule')->__('Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                
                if ($redirectBack)
                {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $model->getId(),
                        '_current'=>true
                    ));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('marketsuite/filter');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketsuite')->__('Rule was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketsuite')->__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('marketsuite/filter'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}