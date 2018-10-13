<?php

class Krishinc_Overridenewsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{ 
	
	/**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribe($postdata)
    {
    	if(isset($postdata['position'])) {$this->setPosition($postdata['position']); }
    	if(isset($postdata['firstname'])) {$this->setFirstname($postdata['firstname']); }
    	if(isset($postdata['lastname'])) {$this->setLastname($postdata['lastname']); }
    	return parent::subscribe($postdata['email']); 
    }
      /**
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
    	$this->setFirstname($customer->getFirstname());
    	$this->setLastname($customer->getLastname());
    	return parent::subscribeCustomer($customer); 
    }

    /*** Function rewrite to disable newsletter email send functionality
     * @return Krishinc_Overridenewsletter_Model_Subscriber|Mage_Newsletter_Model_Subscriber
     */
    public function sendConfirmationSuccessEmail() {
        return $this;
    }

    /*** Function rewrite to disable newsletter email send functionality
     * @return Krishinc_Overridenewsletter_Model_Subscriber|Mage_Newsletter_Model_Subscriber
     */
    public function sendUnsubscriptionEmail() {
        return $this;
    }
}