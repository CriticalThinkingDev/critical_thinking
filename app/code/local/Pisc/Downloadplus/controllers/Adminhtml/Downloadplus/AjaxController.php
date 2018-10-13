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

class Pisc_Downloadplus_Adminhtml_Downloadplus_AjaxController extends Mage_Core_AjaxController
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

    	$link = $this->getRequest()->getParam('url', null);
    	$type = $this->getRequest()->getParam('type', null);
    	$linkId = $this->getRequest()->getParam('link_id', null);
    	$sampleId = $this->getRequest()->getParam('sample_id', null);
    	$helper = false;

    	$response['redirect'] = $link;

    	if (Mage::helper('downloadplus')->existsDownloadplusAWS()) {
    		$helper = Mage::helper('downloadplusaws/download');
    		if ($helper && $link && $type) {
    			switch ($type) {
    				case Pisc_DownloadplusAWS_Helper_Download::LINK_TYPE_AWSS3:
   						$helper->setResource($link, $type);
   						$response['redirect'] = $helper->getUrl();
    					break;
    				case Pisc_DownloadplusAWS_Helper_Download::LINK_TYPE_AWSCF:
   						$response['redirect'] = Mage::helper('downloadplusaws')->setStore($this->getRequest()->getParam('store'))->getAdminCloudfrontTestUrl();
   						Mage::helper('downloadplus')->saveInSession('cloudfront_test_object', $link);
   						Mage::helper('downloadplus')->saveInSession('form_key', Mage::getSingleton('core/session')->getFormKey());
   						Mage::helper('downloadplus')->saveInSession('cloudfront_test_object_link_id', $linkId);
   						Mage::helper('downloadplus')->saveInSession('cloudfront_test_object_sample_id', $sampleId);
    					break;
    			}
    		}
    	}

    	if (Mage::helper('downloadplus')->existsDownloadplusEditionguard()) {
    		$helper = Mage::helper('downloadpluseditionguard/download');
    		if ($helper && $link && $type) {
    			switch ($type) {
    				case Pisc_DownloadplusEditionguard_Helper_Download::LINK_TYPE_EDITIONGUARD:
   						$helper->setResource($link, $type);
   						$response['redirect'] = $helper->getUrl();
    					break;
    			}
    		}
    	}

    	$response = Zend_Json::encode($response);
    	$this->getResponse()->setHeader('Content-type', 'application/json');
    	$this->getResponse()->setBody($response);
    }

    /**
     * Upload file controller action
     */
    public function uploadAction()
    {
    	$type = $this->getRequest()->getParam('type');
    	$tmpPath = null;
    	switch ($type) {
    		case 'link_images':
    			$tmpPath = Pisc_DownloadplusMagazine_Model_Issue_Link::getBaseThumbnailTmpPath();
    			break;
    	}
		if ($tmpPath) {
	    	$result = array();
	    	$config = Mage::getModel('downloadplus/config');
	    	try {
	    		$uploader = new Varien_File_Uploader($type);
	    		$uploader->setAllowRenameFiles(true);
	    		$uploader->setFilesDispersion(false);
	    		$uploader->setFilenamesCaseSensitivity($config->getDownloadableDeliveryFilenameMixedcase());
	    		$result = $uploader->save($tmpPath);
	    		$result['cookie'] = array(
	    				'name'     => session_name(),
	    				'value'    => $this->_getSession()->getSessionId(),
	    				'lifetime' => $this->_getSession()->getCookieLifetime(),
	    				'path'     => $this->_getSession()->getCookiePath(),
	    				'domain'   => $this->_getSession()->getCookieDomain()
	    		);
	    	} catch (Exception $e) {
	    		$result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
	    	}
		} else {
			$result = array('error'=>Mage::helper('downloadplus')->__('Unsupported upload type.'), 'errorcode'=>'UPLOAD FAILED');
		}

    	$this->getResponse()->setBody(Zend_Json::encode($result));
    }

}