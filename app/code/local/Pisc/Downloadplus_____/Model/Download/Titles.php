<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadplus Download Titles model
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.2.3
 */
class Pisc_Downloadplus_Model_Download_Titles extends Mage_Core_Model_Abstract
{
	
	protected $_eventPrefix = 'downloadplus_download_titles';

    /**
     * Constructor
     *
     */
    protected function _construct()
    {
    	$this->_init('downloadplus/download_titles');
    }

}