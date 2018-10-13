<?php
class Krishinc_Advancecustomer_Block_Admin_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
	 /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = array();

        /* @var $config Mage_Eav_Model_Config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
      
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
        	  
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            /****START:: Added by bijal to show customer type at adminend >> order >> account info section****/
             if($attributeCode == 'customer_type') { 
             	 
             	  $attribute->setData('is_system',false); 
             }
             /****END****/
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            /****START:: Added by bijal to show customer type at adminend >> order >> account info section****/
            if($attributeCode == 'customer_type') { 
            	$orderKey   = $attribute->getAttributeCode();
            	
            } else {
            	$orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            }
        	/***END****/
            $orderValue = $this->getOrder()->getData($orderKey); 
            if ($orderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $orderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortOrder  = $attribute->getSortOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }
}