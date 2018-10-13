<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Config Options Download Serialnumber Pool Setting
 *
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @author     PILLWAX Industrial Solutions Consulting
 * @version		0.1.4
 */

class Pisc_Downloadplus_Model_System_Config_Source_Download_Settings_Serialnumber_Product_Import_Pool
{
    protected $_options;

    public function toOptionArray($isMultiselect=false, $product=false)
    {
    	Mage::getModel('downloadplus/config');
    	Mage::getModel('downloadable/product_type');
    	
    	$options = Array(
    		Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('- Add to new Pool above -')),
    		Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT, 'label'=>Mage::helper('downloadplus')->__('- Whole Product -'))
    	);

    	if ($product) {
    		if ($product->getTypeId()==Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
	    		if ($pools = Mage::helper('downloadplus')->getSerialnumberPoolsByProduct($product)) {
	    			foreach($pools as $pool) {
	    				if (!empty($pool) && $pool!=Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT) {
	    					$options[] = Array('value'=>$pool, 'label'=>$pool);
	    				}
	    			}
	    		}
    		}
    	}
    	
    	if ($product->getTypeId()!=Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
    		// '-Add to new Pool above-' only for downloadable products
    		unset($options[0]);
    	}
    	
        return $options;
    }

}