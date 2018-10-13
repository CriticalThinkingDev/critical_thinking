<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AdvancedReviews
 * @version    2.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_AdvancedReviews_Block_Adminhtml_Proscons extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected $_currentRef;

    protected function _getRef()
    {
        if ( $this->_currentRef )
        {
            return $this->_currentRef;
        }
        else
        {
            return $this->_currentRef = ( Mage::helper('advancedreviews')->isPros() ? 'pros' : 'cons' );
        }
    }    
        
    public function __construct()
    {       
        switch ( Mage::registry( Mage::helper('advancedreviews')->getConstPcRegRef() ) )
        {
            case Mage::helper('advancedreviews')->getConstTypePros() :
                //Pros
                $title = Mage::helper('advancedreviews')->__('Pros');
                $add_button_label = Mage::helper('advancedreviews')->__('Add New Pros');
                break;

            case Mage::helper('advancedreviews')->getConstTypeCons() :
                //Cons
                $title = Mage::helper('advancedreviews')->__('Cons');
                $add_button_label = Mage::helper('advancedreviews')->__('Add New Cons');
                break;

            default:
                //Nothing
                $title = Mage::helper('advancedreviews')->__('Error');
        }

        $this->_controller = 'adminhtml_proscons';
        $this->_blockGroup = 'advancedreviews';
        $this->_headerText = $title;
        $this->_addButtonLabel = $add_button_label;
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array( 'ref' => $this->_getRef() ));
    }
}