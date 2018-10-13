<?php
/**
 * DownloadPlus Form Field Block
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Block_Adminhtml_Renderer_Form_Text extends Mage_Adminhtml_Block_Abstract
{
	
	protected function _toHtml()
	{
		$html = $this->getHtmlBefore();
		if ($this->getLabel()) {
			$html.= '<label for="' . $this->getName() . '">' . $this->getLabel() . '</label>';
		}
		
		$html.= '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
				. $this->getClass() . '" title="' . $this->getTitle() . '" ' . $this->getExtraParams()  
				. ' value="'.$this->getValue().'" />';

		$html.= $this->getHtmlAfter();
		
		return $html;
	}
	
}
