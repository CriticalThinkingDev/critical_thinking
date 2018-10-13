<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.1.5
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2013 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Block_Adminhtml_Productattributes_Init_Brands
    extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('listrak/remarketing/productattributes/form/initbrands.phtml');
    }

    public function _prepareLayout()
    {
        $this->setChild(
            'form',
            $this->getLayout()->createBlock('remarketing/adminhtml_productattributes_init_brands_form')
        );
        return parent::_prepareLayout();
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    public function getFormElementsHtml()
    {
        return $this->getChildHtml('form-elements');
    }
}

