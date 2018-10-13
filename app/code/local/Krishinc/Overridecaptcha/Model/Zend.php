<?php

class Krishinc_Overridecaptcha_Model_Zend extends Mage_Captcha_Model_Zend 
{
	 /**
     * Whether captcha is required to be inserted to this form
     *
     * @param null|string $login
     * @return bool
     */
    public function isRequired($login = null)
    { 
       /// if ($this->_isUserAuth() || !$this->_isEnabled() || !in_array($this->_formId, $this->_getTargetForms())) {
        if (!$this->_isEnabled() || !in_array($this->_formId, $this->_getTargetForms())) {
            return false;
        }

        return ($this->_isShowAlways() || $this->_isOverLimitAttempts($login)
            || $this->getSession()->getData($this->_getFormIdKey('show_captcha'))
        );
    }
}