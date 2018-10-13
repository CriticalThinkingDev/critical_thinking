<?php
/**
 * Downloadable Admin Customer Downloadable File controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.2
 */

class Pisc_Downloadplus_Adminhtml_Product_FileController extends Mage_Adminhtml_Controller_Action
{

    /*
     * Check admin permissions for this controller
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/product');
    }

    protected function getSession()
    {
    	return Mage::getSingleton('core/session');
    }

    /*
     * Returns the current Product
     */
    protected function getProduct()
    {
    	$result = null;

    	$product_id = $this->getRequest()->getParam('product_id');
    	if ($product_id) {
    		$result = Mage::getModel('catalog/product')->load($product_id);
    	}
    	if (!$result) {
    		$result = Mage::registry('current_product');
    	}
		return $result;
    }

    /**
     * Upload file controller action
     */
    public function uploadAction()
    {
        $type = $this->getRequest()->getParam('type');
        $tmpPath = '';
        if ($type == 'links') {
            $tmpPath = Pisc_Downloadplus_Model_Product_Download::getBaseTmpPath($this->getProduct());
        }
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

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Download process
     *
     * @param string $resource
     * @param string $resourceType
     */
    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('downloadable/download');

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }

	/*
     * Download link action
     */
    public function linkAction()
    {
        $linkId = $this->getRequest()->getParam('id', 0);
        $link = Mage::getModel('downloadplus/link_product_item')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getLinkUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('downloadable/file')->getFilePath(
                    Pisc_Downloadplus_Model_Product_Download::getBasePath(), $link->getLinkFile()
                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (Mage_Core_Exception $e) {
                $this->getSession()->addError(Mage::helper('downloadable')->__('Sorry, there was an error getting requested content'));
                $this->getResponse()->setRedirect($this->_getRefererUrl());
            }
        }
    }

}
