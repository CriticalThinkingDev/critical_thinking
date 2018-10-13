<?php

class Krishinc_Advancecustomer_Model_Source_Marketarray extends  Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
     /**
     * Get list of all available countries
     *
     * @return mixed
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            
            if(!Mage::app()->getStore()->isAdmin())
            {
                $this->_options = array( 
                    array(
                        'value' => 'P',
                        'label' => 'Parent',
                    ),
                    array(
                        'value' => 'H',
                        'label' => Mage::helper('advancecustomer')->__('Homeschooling Parent'),
                    ),
                    array(
                        'value' => 'E',
                        'label' => Mage::helper('advancecustomer')->__('Classroom Teacher or Institution'),
                    ), 
                    array(
                        'value' => 'O',
                        'label' => Mage::helper('advancecustomer')->__('Other'), 
                    )
                );    
            }else {
                $this->_options = array( 
                    array(
                        'value' => 'P',
                        'label' => 'Parent',
                    ),
                    array(
                        'value' => 'H',
                        'label' => Mage::helper('advancecustomer')->__('Homeschooling Parent'),
                    ),
                    array(
                        'value' => 'E',
                        'label' => Mage::helper('advancecustomer')->__('Classroom Teacher or Institution'),
                    ),
                    array(
                        'value' => 'D',
                        'label' => Mage::helper('advancecustomer')->__('Reseller'),
                    ),
                    array(
                        'value' => 'O',
                        'label' => Mage::helper('advancecustomer')->__('Other'), 
                    )
                );
            }
        } 
        
        return $this->_options;
    }
    public function getKeyValueOptions()
    {
        $allOptions = $this->getAllOptions();
        $newArray = array();
     
        foreach ($allOptions as $arr)
        {
            if(!$newArray[$arr['value']]) {
                $newArray[$arr['value']] =$arr['label'];
            }
        } 
        return $newArray;
    }
}