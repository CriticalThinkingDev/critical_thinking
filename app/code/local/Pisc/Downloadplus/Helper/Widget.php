<?php
/**
 * Downloadplus Widget Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2012 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.1
 */

class Pisc_Downloadplus_Helper_Widget extends Mage_Core_Helper_Abstract
{

	public function getWidget($block)
	{
		$widget = false;
		$path = explode('/', $block);
		$addon = null;
		$result = false;

		if (isset($path[0])) {
				switch ($path[0]) {
					case 'downloadplusaws':
						$addon = 'Pisc_DownloadplusAWS';
						break;
					case 'downloadplusbuilder':
						$addon = 'Pisc_DownloadplusBuilder';
						break;
					case 'downloadplusfile':
						$addon = 'Pisc_DownloadplusFile';
						break;
					case 'downloadplusemail':
						$addon = 'Pisc_DownloadplusEmail';
						break;
					case 'downloadpluscode':
						$addon = 'Pisc_DownloadplusCode';
						break;
					case 'downloadpluseditionguard':
						$addon = 'Pisc_DownloadplusEditionguard';
						break;
					case 'downloadplusmagazine':
						$addon = 'Pisc_DownloadplusMagazine';
						break;
					default:
						$addon = 'Pisc_Downloadplus';
						break;
				}
				if (!is_null($addon)) {
					if ($module = Mage::getConfig()->getNode('modules/'.$addon)) {
						$result = $module->is('active');
					}
				}
			if ($result) {
				try {
					$widget = Mage::app()->getLayout()->createBlock($block);
				} catch (Exception $e) {
					$widget = false;
				}
			}
		}

		return $widget;
	}

}
