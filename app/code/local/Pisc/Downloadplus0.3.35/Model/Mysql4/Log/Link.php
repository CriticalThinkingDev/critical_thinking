<?php
/**
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license
 */

/**
 * Downloadable Product  Link Log resource model
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author
 */
class Pisc_Downloadplus_Model_Mysql4_Log_Link extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        parent::_construct();
    	$this->_init('downloadplus/log', 'log_id');
    }

}
