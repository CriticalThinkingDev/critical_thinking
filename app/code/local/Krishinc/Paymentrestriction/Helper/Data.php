<?php
class Krishinc_Paymentrestriction_Helper_Data extends Mage_Core_Helper_Abstract
{
	const CONFIG_PATH_ALLOWED_ADMIN_PAYMENT_METHODS = 'payment_restrict/basic_settings/frontend_payment';
    public function getAllowedAdminPaymentMethods()
    { 
        $methods = Mage::getStoreConfig(self::CONFIG_PATH_ALLOWED_ADMIN_PAYMENT_METHODS); 
        $methods = explode(',', $methods);
        return $methods;
    }
}