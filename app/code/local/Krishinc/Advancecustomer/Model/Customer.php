<?php
   class Krishinc_Advancecustomer_Model_Customer extends Mage_Customer_Model_Customer {
   	
   	
   	  const XML_PATH_REGISTER_ADMIN_EMAIL_TEMPLATE = 'customer/create_account/admin_email_template';
	  const XML_PATH_CONFIRM_ADMIN_EMAIL_TEMPLATE       = 'customer/create_account/admin_email_confirmation_template';
      const XML_PATH_REMIND_ADMIN_EMAIL_TEMPLATE =  'customer/password/admin_remind_email_template';     
	  /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
    	if (Mage::app()->getStore()->isAdmin()) {
	      
	        $types = array(
	            'registered'   => self::XML_PATH_REGISTER_ADMIN_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
	            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
	            'confirmation' => self::XML_PATH_CONFIRM_ADMIN_EMAIL_TEMPLATE,   // email with confirmation link 
	        );
    	} else {
    		  $types = array(
	            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
	            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
	            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,   // email with confirmation link
	        );
    	} 
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('customer' => $this, 'back_url' => $backUrl), $storeId);

        return $this;
    }
     /**
     * Send email with new customer password
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordReminderEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }
          if (Mage::app()->getStore()->isAdmin()) {   
            $this->_sendEmailTemplate(self::XML_PATH_REMIND_ADMIN_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this), $storeId);      
          }else {
            $this->_sendEmailTemplate(self::XML_PATH_REMIND_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('customer' => $this), $storeId);      
          }

        return $this;
    }
   }
   