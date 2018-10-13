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
 * @version		0.1.5
 */

class Pisc_Downloadplus_Model_System_Config_Source_Download_Settings_Serialnumber_Pool
{
    protected $_options;

    public function toOptionArray($isMultiselect=false, $product=false)
    {
    	Mage::getModel('downloadplus/config');

    	$options = Array(
    		Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_NONE, 'label'=> Mage::helper('downloadplus')->__('- Do not assign Serialnumbers -')),
    		Array('value'=>Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT, 'label'=> Mage::helper('downloadplus')->__('- Whole Product -'))
    	);

    	if ($globalPools = Mage::helper('downloadplus')->getSerialnumberPoolsGlobal()) {
    		foreach($globalPools as $pool) {
    			if (!empty($pool)) {
    				$options[] = Array(
    				    'value'=>$pool,
    				    'label'=>Mage::helper('downloadplus')->__('%s (Global)', str_replace(Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL, '', $pool))
    				);
    			}
    		}
    	}

    	if ($product) {
    		if ($pools = Mage::helper('downloadplus')->getSerialnumberPoolsByProduct($product)) {
    			foreach($pools as $pool) {
    				if (!empty($pool) && $pool!=Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_PRODUCT && !in_array($pool, $globalPools)) {
    				    if (strpos($pool, Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL)===0) {
    				        $options[] = Array(
    				            'value'=>$pool,
    				            'label'=>Mage::helper('downloadplus')->__('%s (Global)', str_replace(Pisc_Downloadplus_Model_Config::SERIALNUMBER_POOL_GLOBAL, '', $pool))
    				        );
    				    } else {
        					$options[] = Array('value'=>$pool, 'label'=>$pool);
    				    }
    				}
    			}
    		}
    	}

        return $options;
    }
}