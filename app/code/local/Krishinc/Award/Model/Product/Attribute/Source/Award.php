<?php
/**
 * Catalog product country attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Krishinc_Award_Model_Product_Attribute_Source_Award
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get list of all available countries
     *
     * @return mixed
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options =   array('value'=>0, 'label'=>Mage::helper('catalog')->__('No Awards'));
        }
        $data = Mage::getModel('award/award')->getCollection();
     	if($data) {
     		  $this->_options = array(); 
     	
	     	 foreach ($data as $attribute) { 
	            $this->_options[] = array(
		                'label' => Mage::helper('award')->__($attribute['name']),
		                'value' => $attribute['award_option_id'] 
		            ); 
	     	 }
        } 
        return $this->_options;
    }
}