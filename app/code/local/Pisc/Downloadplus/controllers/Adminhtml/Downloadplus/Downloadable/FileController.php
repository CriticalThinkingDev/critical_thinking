<?php
/**
 * Downloadplus Admin Downloadable File controller
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_Downloadplus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.4
 */

require_once Mage::getModuleDir('controllers', 'Mage_Downloadable').DS.'Adminhtml'.DS.'Downloadable'.DS.'FileController.php';

class Pisc_Downloadplus_Adminhtml_Downloadplus_Downloadable_FileController extends Mage_Downloadable_Adminhtml_Downloadable_FileController
{

    /**
     * Upload file controller action
     * Modified for case sensitivity configuration setting
     */
    public function uploadAction()
    {
    	$type = $this->getRequest()->getParam('type');
    	if ($type=='samples' || $type=='links' || $type=='link_samples') {
    		return parent::uploadAction();
    	}

        $tmpPath = '';
        if ($type == 'links_image') {
        	$tmpPath = Pisc_Downloadplus_Model_Link_Image::getBaseTmpPath();
        } elseif ($type == 'samples_image') {
        	$tmpPath = Pisc_Downloadplus_Model_Sample_Image::getBaseTmpPath();
        }
        $result = array();
        try {
        	$uploader = new Mage_Core_Model_File_Uploader($type);
        	$uploader->setAllowRenameFiles(true);
        	$uploader->setFilesDispersion(false);
        	$result = $uploader->save($tmpPath);

        	/**
        	 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
        	 */
        	$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
        	$result['path'] = str_replace(DS, "/", $result['path']);

        	if (isset($result['file'])) {
        		$fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
        		Mage::helper('core/file_storage_database')->saveFile($fullPath);
        	}

        	$result['cookie'] = array(
        			'name'     => session_name(),
        			'value'    => $this->_getSession()->getSessionId(),
        			'lifetime' => $this->_getSession()->getCookieLifetime(),
        			'path'     => $this->_getSession()->getCookiePath(),
        			'domain'   => $this->_getSession()->getCookieDomain()
        	);
        } catch (Exception $e) {
            Mage::logException($e);
        	$result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

}
