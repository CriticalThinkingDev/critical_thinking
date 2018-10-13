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

class AW_Marketsuite_Block_Adminhtml_Filterconf_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'marketsuite';
        $this->_controller = 'adminhtml_filterconf';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('marketsuite')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('marketsuite')->__('Delete Rule'));
        $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('customer')->__('Save And Continue Edit'),
                'onclick'   => 'editForm.submit(\''.$this->_getSaveAndContinueUrl().'\')',
                'class' => 'save'
            ), 10);
        $this->_addButton('save_as', array(
                'label'     => Mage::helper('customer')->__('Save As'),
                'onclick'   => 'saveAs()',
                'class' => 'save'
            ), 10);

        $filter = Mage::registry('marketsuitefilters_data');
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            'back'      => 'edit',
        ));
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('marketsuitefilters_data');
        if ($rule->getFilterId()) {
            return Mage::helper('marketsuite')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('marketsuite')->__('New Rule');
        }
    }
}
