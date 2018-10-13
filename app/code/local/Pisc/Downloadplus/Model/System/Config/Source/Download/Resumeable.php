<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2011 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Download Resumeable Setting
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.0
 */

class Pisc_Downloadplus_Model_System_Config_Source_Download_Resumeable
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
    	Mage::getModel('downloadplus/config');
    	$options = Array(
    			Array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_OFF, 'label'=>Mage::helper('downloadplus')->__('Off')),
    			Array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_ON, 'label'=>Mage::helper('downloadplus')->__('On')),
    			//Array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_XSENDFILE, 'label'=>Mage::helper('downloadplus')->__('On (use X-Sendfile for Apache, Lighttpd v1.5, Cherokee)')),
    			//Array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_XLIGHTTPSENDFILE, 'label'=>Mage::helper('downloadplus')->__('On (use X-Sendfile for Lighttpd v1.4)')),
    			//Array('value'=>Pisc_Downloadplus_Model_Config::CONFIG_DOWNLOAD_RESUME_XACCELREDIRECT, 'label'=>Mage::helper('downloadplus')->__('On (use X-Accel for Nginx, Cherokee)')),
    	);

        if(!$isMultiselect){
            array_unshift($options, Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('--Please Select--')));
        }

        return $options;
    }
}
