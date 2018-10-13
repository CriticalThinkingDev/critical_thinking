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
 * @version		0.1.2
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

    public function testUrlAction()
    {
    	$this->_expireAjax();
    	 
    	$response = Array();

    	$link = $this->getRequest()->getParam('url');
    	$type = $this->getRequest()->getParam('type');
    	$helper = false;
    	
    	if (Mage::helper('downloadplus')->existsDownloadplusAWS()) {
    		$helper = Mage::helper('downloadplusaws/download');
    	}
    	
    	if ($helper && $link && $type) {
    		switch ($type) {
    			case Pisc_DownloadplusAWS_Helper_Download::LINK_TYPE_AWSS3:
    				if ($helper) {
    					$helper->setResource($link, $type);
    					$response['redirect'] = $helper->getUrl();
    				}
    				break;
    			case Pisc_DownloadplusAWS_Helper_Download::LINK_TYPE_AWSCF:
    				if ($helper) {
    					$response['redirect'] = Mage::helper('downloadplusaws')->setStore($this->getRequest()->getParam('store'))->getAdminCloudfrontTestUrl();
    					Mage::helper('downloadplus')->saveInSession('cloudfront_test_object', $link);
    					Mage::helper('downloadplus')->saveInSession('form_key', Mage::getSingleton('core/session')->getFormKey());
    				}
    				break;
    			default:
    				$response['redirect'] = $url;
    				break;
    		}
    	} else {
    		$response['redirect'] = $url;
    	}
    	
    	$response = Zend_Json::encode($response);
    	$this->getResponse()->setHeader('Content-type', 'application/json');
    	$this->getResponse()->setBody($response);
    }
    
}