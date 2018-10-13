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

class AW_Marketsuite_Block_Adminhtml_Filterconf_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('filter_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('catalogrule')->__('Market Segmentation Suite'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('catalogrule')->__('Rule Information'),
            'title'     => Mage::helper('catalogrule')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock('marketsuite/adminhtml_filterconf_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('catalogrule')->__('Conditions'),
            'title'     => Mage::helper('catalogrule')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('marketsuite/adminhtml_filterconf_edit_tab_conditions')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
