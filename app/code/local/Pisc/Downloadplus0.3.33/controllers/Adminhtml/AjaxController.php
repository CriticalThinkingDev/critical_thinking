<?php
/**
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2009 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com)
 */

/**
 * Downloadplus Adminhtml Ajax Controller
 *
 * @author     Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.1
 */

require_once Mage::getModuleDir('controllers', 'Mage_Core').DS.'AjaxController.php';

class Pisc_Downloadplus_Adminhtml_AjaxController extends Mage_Core_AjaxController
{

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    protected function _expireAjax()
    {
    	$session = Mage::getSingleton('core/session');
    	$form_key = $this->getRequest()->getParam('form_key', false);
    	if (!$form_key || $session->getFormKey()!=$form_key) {
            $this->_ajaxRedirectResponse();
            exit;
    	}
        Mage::getSingleton('core/translate_inline')->setIsAjaxRequest(true);
    }

	/*
	 * Sample Ajax function
	 */
/*
	public function sampleAction()
	{
        $this->_expireAjax();

	    $response = '';

	    $this->getResponse()->setHeader('Content-type', 'text/html; charset=utf-8');
        $this->getResponse()->setBody($response);
	}
*/

}