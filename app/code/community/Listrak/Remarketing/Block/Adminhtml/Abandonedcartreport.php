<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.0.0
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2011 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Block_Adminhtml_Abandonedcartreport
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_abandonedcartreport';
        $this->_blockGroup = 'remarketing';
        $this->_headerText = Mage::helper('remarketing')->__('Abandoned Carts');
        parent::__construct();
        $this->_removeButton('add');
    }
}