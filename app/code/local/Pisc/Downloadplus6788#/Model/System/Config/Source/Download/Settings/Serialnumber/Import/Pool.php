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
 * @version		0.1.1
 */

class Pisc_Downloadplus_Model_System_Config_Source_Download_Settings_Serialnumber_Import_Pool
{
    protected $_options;

    public function toOptionArray($isMultiselect=false, $product=false)
    {
    	Mage::getModel('downloadplus/config');
    	
    	$options = Array(
    		Array('value'=>'', 'label'=> Mage::helper('downloadplus')->__('- Add to new Pool above -'))
    	);

    	if ($pools = Mage::helper('downloadplus')->getSerialnumberPoolsGlobal()) {
    		foreach($pools as $pool) {
    			if (!empty($pool)) {
    				$options[] = Array('value'=>$pool, 'label'=>Mage::helper('downloadplus')->__('%s (Global)', $pool));
    			}
    		}
    	}
    	
    	if ($product) {
    		if ($pools = Mage::helper('downloadplus')->getSerialnumberPoolsByProduct($product)) {
    			foreach($pools as $pool) {
    				if (!empty($pool)) {
    					$options[] = Array('value'=>$pool, 'label'=>$pool);
    				}
    			}
    		}
    	}
    	
        return $options;
    }
}