<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Downloadable Products List for Customer
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.8
 */

class Pisc_Downloadplus_Block_Customer_Products_List_Default extends Pisc_Downloadplus_Block_Customer_Products_List
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadplus/customer/products/list/default.phtml');
    }

    public function updateCollection()
    {
        $this->setItems(null);
        return $this;
    }
}
