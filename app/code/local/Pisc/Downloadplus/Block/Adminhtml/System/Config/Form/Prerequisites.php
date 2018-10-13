<?php
/**
 * Downloadplus Prerequisites Check
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_System_Config_Form_Prerequisites extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected $conflictingExtensions = Array(
	);

	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$id = $element->getHtmlId();
		
		$html = '<tr><td class="label"><label for="'.$id.'">'.$element->getLabel().'</label></td>';
		$html.= '<td class="value">';
		$html.= '<div id="downloadplus_prerequisites">';

		$helper = Mage::helper('downloadplus');
		$config = Mage::getModel('downloadplus/config')->setStore(Mage::app()->getRequest()->getParam('store'));
		$check = Array();
		$notice = Array();
		$fatal = Array();

		/* Check for conflicting extensions */
		foreach ($this->conflictingExtensions as $extension) {
			if ($module = Mage::getConfig()->getNode('modules/'.$extension)) {
				if ($module->is('active')) {
					$check[] = '<li>'.$helper->__('Your store installation uses the following extension that requires modification to work with DownloadPlus: %s', $extension).'</li>';						
				}
			}
		}

		/* Check for Magento Version */
		try {
			if (!Mage::helper('downloadplus/magento')->isCompatible()) {
				$fatal[] = '<li>'.$helper->__('This DownloadPlus License is not maintained for this Magento version, functional limitations will occur. <a href="%s">Please purchase a new license for your current Magento Version at our License Store.</a>', 'https://technology.pillwax.com/software/downloadplus-for-magento.html').'</li>';
				$fatal[] = '<li>'.$helper->__('The installed License is maintained for Magento %s, this Magento installation is detected as %s (Community Edition equivalent).', Mage::helper('downloadplus/magento')->getMaintainedVersion(), Mage::helper('downloadplus/magento')->getMajorVersion()).'</li>';
				
			}
		} catch (Exception $e) {
			$fatal[] = '<li>'.$helper->__('DownloadPlus has detected an incomplete installation. Please install all files contained in the Installation Package File.').'</li>';
		}		
		
		$html.= '<ul class="messages">';
		if (count($fatal)>0 || count($notice)>0 || count($check)>0) {
			if (count($fatal)>0) {
				$html.= '<li class="error-msg"><ul>'.implode($fatal).'</ul></li>';
			}
			if (count($notice)>0) {
				$html.= '<li class="notice-msg"><ul>'.implode($notice).'</ul></li>';
			}
			if (count($check)>0) {
				$html.= '<li class="notice-msg"><ul>'.implode($check).'</ul></li>';
			}
			$html.= '<li class="notice-msg">'.$helper->__('Consult the <a href="https://support.pillwax.com/open-source/doku.php?id=magento:downloadplus" target="_blank">online documentation here</a> for further instructions.').'</li>';
		} else {
			if (count($check)>0) {
				$html.= '<li class="notice-msg"><ul>'.implode($check).'</ul></li>';
			}
			$html.= '<li class="success-msg">';
			$html.= $helper->__('Installation of the extension seems to be ok. Consult the <a href="https://support.pillwax.com/open-source/doku.php?id=magento:downloadplus" target="_blank">online documentation here</a> for configuration instructions and in case of experiencing difficulties.');
			$html.= '</li>';
		}
		$html.= '</ul>';
		
		if ($element->getComment()) {
			$html.= '<p class="nm"><small>'.$element->getComment().'</small></p>';
		}
		
		$html.= '</div>';
		$html.= '</td>';
		$html.= '</tr>';
		return $html;
	}
	
}
