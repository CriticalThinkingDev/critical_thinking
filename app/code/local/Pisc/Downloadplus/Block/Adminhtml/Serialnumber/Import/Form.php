<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Serialnumber_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{

	public function __construct()
	{
		parent::__construct();

		$this->setId('downloadplusSerialnumberImportForm');
	}

	public function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'        => 'edit_form',
			'action'    => $this->getData('action'),
			'method'    => 'post'
		));

		$this->setTitle(Mage::helper('downloadplus')->__('Import shared Serialnumbers'));

		$fieldset = $form->addFieldset('import_fieldset', array(
			'legend' => Mage::helper('downloadplus')->__('Import Serialnumbers')
		));

		$fieldset->addField('downloadplus-serialnumbers', 'textarea',
			array(
				'name'  => 'downloadplus[serialnumbers]',
				'label' => Mage::helper('downloadplus')->__('New Serialnumbers'),
				'class' => 'required-entry',
				'value' => '',
		        'required' => true,
		        'after_element_html' => '<p class="note nm"><small>'.$this->__("Paste one-line serialnumbers here and use 'Import Serialnumbers' to import.").'</small></p>'
			)
		);

		$fieldset->addField('downloadplus-serialnumberpool-new', 'text',
			array(
				'name'  => 'downloadplus[serialnumberpool][new]',
				'label' => Mage::helper('downloadplus')->__('Add to new Pool'),
				'class' => '',
				'value' => '',
				'required' => false,
				'after_element_html' => '<p class="note nm"><small>'.$this->__('Enter a Global Pool name here or select existing from below.').'</small></p>'
			)
		);

		$fieldset->addField('downloadplus-serialnumberpool-use', 'select',
			array(
				'name'  => 'downloadplus[serialnumberpool][use]',
				'label' => Mage::helper('downloadplus')->__('Import into Pool'),
				'class' => '',
				'values' => Mage::getModel('downloadplus/system_config_source_download_settings_serialnumber_import_pool')->toOptionArray(),
				'value' => '',
				'required' => false,
			)
		);

		$form->setAction($this->getUrl('adminhtml/downloadplus_serialnumber/importPost'));
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
