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

class AW_Marketsuite_Block_Adminhtml_Filterconf_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('marketsuitefilters_data');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalogrule')->__('General Information')));

        if ($model['filter_id']) {
            $fieldset->addField('filter_id', 'hidden', array(
                'name' => 'filter_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('catalogrule')->__('Rule Name'),
            'title' => Mage::helper('catalogrule')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Status'),
            'title'     => Mage::helper('catalogrule')->__('Status'),
            'name'      => 'is_active',
            'options'    => array(
                '1' => Mage::helper('catalogrule')->__('Active'),
                '0' => Mage::helper('catalogrule')->__('Inactive'),
            ),
        ));

        $fieldset->addField('save_as_flag', 'hidden', array(
                'name'      => '_save_as_flag',
                'value'     => 0
            ));

        $form->setValues($model);

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
