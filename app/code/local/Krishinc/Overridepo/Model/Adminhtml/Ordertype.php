<?php 
class Krishinc_Overridepo_Model_Adminhtml_Ordertype extends Varien_Object
{
    const ORDERTYPE_EMAIL   = 'EMAIL';
    const ORDERTYPE_FAX     = 'FAX';
    const ORDERTYPE_MAIL    = 'MAIL';
	const ORDERTYPE_PHONE   = 'PHONE';
	const ORDERTYPE_WEB   = 'WEB';
	const ORDERTYPE_COMP   = 'COMP';
	const ORDERTYPE_COMPD   = 'COMPD';
	const ORDERTYPE_DLR   = 'DLR';
	const ORDERTYPE_CONO   = 'CONO';
	const ORDERTYPE_CONR   = 'CONR';
	const ORDERTYPE_CONS   = 'CONS';
const ORDERTYPE_EDU   = 'EDU';

//EMAIL, FAX, MAIL, PHONE, WEB, COMP, COMPD, DLR, CONO, CONR, CONS
 /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::ORDERTYPE_EMAIL=> Mage::helper('overridepo')->__('EMAIL'),
            self::ORDERTYPE_FAX => Mage::helper('overridepo')->__('FAX'),
            self::ORDERTYPE_MAIL  => Mage::helper('overridepo')->__('MAIL'),
            self::ORDERTYPE_PHONE       => Mage::helper('overridepo')->__('PHONE'),
            self::ORDERTYPE_WEB       => Mage::helper('overridepo')->__('WEB'),
            self::ORDERTYPE_COMP       => Mage::helper('overridepo')->__('COMP'),
            self::ORDERTYPE_COMPD       => Mage::helper('overridepo')->__('COMPD'),
            self::ORDERTYPE_DLR       => Mage::helper('overridepo')->__('DLR'),
            self::ORDERTYPE_CONO       => Mage::helper('overridepo')->__('CONO'),
            self::ORDERTYPE_CONR       => Mage::helper('overridepo')->__('CONR'),
            self::ORDERTYPE_CONS       => Mage::helper('overridepo')->__('CONS'),
 self::ORDERTYPE_EDU       => Mage::helper('overridepo')->__('EDUCEN'),
        );
    }
 	/**
     * Retireve all options
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=> Mage::helper('overridepo')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }
}
