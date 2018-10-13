<?php
/**
 * Downloadplus Layout Helper
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2016 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Helper_Layout extends Mage_Core_Helper_Data
{

    protected $_flashUploaderFiles = Array(
                    'lib/flex.js',
                    'lib/FABridge.js',
                    'mage/adminhtml/flexuploader.js'
                );

    public function isFlashUploader()
    {
        return !class_exists('Mage_Uploader_Block_Abstract');
    }

    public function getUploaderLibrary($library)
    {
        if (($this->isFlashUploader() && in_array($library, $this->_flashUploaderFiles))
            || (!$this->isFlashUploader() &&  !in_array($library, $this->_flashUploaderFiles))) {
            return $library;
        }
        return null;
    }

}
