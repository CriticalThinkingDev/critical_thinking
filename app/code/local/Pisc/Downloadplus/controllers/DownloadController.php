<?php
/**
 * Download Controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.3.17
 *
 * Code for resumeable downloads based on work of:
 * - Edward Jaramilla: http://php.net/manual/en/function.fread.php
 * - AwesomePHP: http://www.awesomephp.com/?Tutorials*16/Download-file-with-resume,-stream-and-speed-options.html
 */

require_once Mage::getModuleDir('controllers', 'Mage_Downloadable').DS.'DownloadController.php';

class Pisc_Downloadplus_DownloadController extends Mage_Downloadable_DownloadController
{

    protected $_eventPrefix = 'downloadplus_download';

    /*
     * Returns if resumable download is configured
     */
    protected function isDownloadResumeable()
    {
    	return Mage::helper('downloadplus')->isDownloadResumeable();
    }

    /*
     * Saves data in current session
     */
    protected function saveInSession($key, $value)
    {
    	return Mage::helper('downloadplus')->saveInDownloadSession($key, $value);
    }

    /*
     * Gets data from current session
     */
    protected function getFromSession($key)
    {
    	return Mage::helper('downloadplus')->getFromDownloadSession($key);
    }

    /*
     * Clears session storage
     */
    protected function clearSession()
    {
    	Mage::helper('downloadplus')->clearDownloadSession();
    	return $this;
    }

    /*
     * Tracks concurrent downloads, returns TRUE if allowed
     */
    protected function startConcurrentDownload()
    {
    	return Mage::helper('downloadplus/download')->startConcurrentDownload();
    }

    /*
     * Removes a concurrent download from tracking, returns remaining number of concurrent downloads
     */
    protected function closeConcurrentDownload()
    {
    	return Mage::helper('downloadplus/download')->closeConcurrentDownload();
    }

    /**
	 * Process the Download
	 * Overrides core function to introduce event
     */
	protected function _processDownload($resource, $resourceType, $resourceObject=null)
    {
    	Mage::helper('downloadplus/download');

    	if (!$resourceObject) {
    		$resourceObject = Mage::registry('downloadplus_download_resourceobject');
    	}

    	$download = Mage::getModel('downloadplus/event');
    	$download->setData('session', $this->_getCustomerSession());
    	$download->setData('resource_path', $resource);
    	$download->setData('resource_type', $resourceType);
    	$download->setData('resource_object', $resourceObject);
    	$download->setData('override_core_download', false);
    	$download->setData('redirect_url', null);

        Mage::dispatchEvent($this->_eventPrefix.'_process_before', array('download' => $download));

        // Redirect and prevent download
        if ($download->getCancelDownload()) {
        	if ($download->getRedirectUrl()) {
        		$this->clearSession();
        		$this->_redirectUrl($download->getRedirectUrl());
        	}
        	return false;
        }

        // Record download in log
        Mage::getModel('downloadplus/log')->track($resourceObject);

        // Allows to override the Core Download Function
        if ($download->getOverrideCoreDownload()) {
        	if ($download->getRedirectUrl()) {
        		$this->_redirectUrl($download->getRedirectUrl());
        	}
        	return true;
        }

        if ($resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSS3 || $resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_AWSCF) {
        	$this->_processDownloadAWS($download->getResourcePath(), $download->getResourceType(), $download->getResourceObject());
        } elseif ($resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_EDITIONGUARD) {
        	$this->_processDownloadEditionGuard($download->getResourcePath(), $download->getResourceType(), $download->getResourceObject());
        } else  {
        	//session_write_close();
	        if (($resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE
	        		|| $resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILELOCAL
        			|| $resourceType==Pisc_Downloadplus_Helper_Download::LINK_TYPE_BUILDER) && $this->isDownloadResumeable()) {
	        	$this->_processResumeableDownload($download->getResourcePath(), $download->getResourceType());
	        } else {
	        	parent::_processDownload($download->getResourcePath(), $download->getResourceType());
	        }
	        //session_start();
        }
       	$this->closeConcurrentDownload();
    }

    public function processDownload($resource, $resourceType, $resourceObject=null) {
    	return $this->_processDownload($resource, $resourceType, $resourceObject);
    }

    /*
     * Process redirect to Amazon Web Services for download
     */
    protected function _processDownloadAWS($resource, $resourceType, $resourceObject=null)
    {
    	if (!Mage::helper('downloadplus')->existsDownloadplusAWS()) {
    		Mage::helper('downloadplus/adminnotification')->addNotification(null, null, Mage::helper('downloadable')->__('Download requiring DownloadPlus for Amazon Web Services requested, but Add-On is not active.'));
    		$this->_getSession()->addError(
    			Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
    		);
    		return false;
    	}
    	$helper = Mage::helper('downloadplusaws/download');
    	$helper->setResource($resource, $resourceType, $resourceObject);
    	$helper->output();
    }

    /*
     * Process redirect to Editionguard for download
     */
    protected function _processDownloadEditionguard($resource, $resourceType, $resourceObject=null)
    {
    	if (!Mage::helper('downloadplus')->existsDownloadplusEditionguard()) {
    		Mage::helper('downloadplus/adminnotification')->addNotification(null, null, Mage::helper('downloadable')->__('Download requiring DownloadPlus for EditionGuard requested, but Add-On is not active.'));
    		$this->_getSession()->addError(
    				Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
    		);
    		return false;
    	}
    	$helper = Mage::helper('downloadpluseditionguard/download');
    	$helper->setResource($resource, $resourceType, $resourceObject);
    	$helper->output();
    }

    /*
     * Allows resumeable download
     */
    protected function _processResumeableDownload($resource, $resourceType, $resourceObject=null)
    {
        $helper = Mage::helper('downloadplus/download');
        $helper->setResource($resource, $resourceType);

        $fileName = $helper->getFilename();
        $fileSize = $helper->getFilesize();
        $contentType = $helper->getContentType();

        // Check if http_range is sent by browser/download manager
        $range = '';
        $extra_ranges = '';
        $seek = Array();
        $seekEnd = '';
        $seekStart = '';

        if (isset($_SERVER['HTTP_RANGE'])) {
        	list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        	if ($size_unit=='bytes' && strpos($range_orig,',')) {
        		// Multiple ranges could be specified at the same time, but for simplicity only serve the first range
        		list($range, $extra_ranges) = explode(',', $range_orig, 2);
        	} else {
        		$range = $range_orig;
        	}
	        // Get download piece from range (if set)
	        $seek = explode('-', $range, 2);
        }
        // Set start and end based on range (if set), else set defaults. Also check for invalid ranges.
        $seekEnd = (isset($seek[1]) && !empty($seek[1])) ? min(abs(intval($seek[1])),($fileSize- 1)) : ($fileSize - 1);
        if (isset($seek[0]) && !empty($seek[0])) {
        	$seekStart = ($seekEnd < abs(intval($seekStart))) ? 0 : max(abs(intval($seek[0])),0);
        } else {
        	$seekStart = 0;
        }

        // Only send partial content header if downloading a piece of the file (IE workaround)
        if ($seekStart>0 || $seekEnd<($fileSize-1)) {
        	$this->getResponse()
        		->setHttpResponseCode(206)
        		->setHeader('Accept-Ranges', 'bytes', true)
        		->setHeader('Content-Range', 'bytes '.$seekStart.'-'.$seekEnd.'/'.$fileSize, true);
        		//->setHeader('Content-Legth', '', true);
        } else {
	        $this->getResponse()
	        	->setHttpResponseCode(200);
	        if ($fileSize) {
	            $this->getResponse()
	            	->setHeader('Content-Rage', 'bytes 0-'.($fileSize-1).'/'.$fileSize, true)
	                ->setHeader('Content-Length', $fileSize, true);
	        }
        }
        $this->getResponse()
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Transfer-Encoding', 'binary', true)
            ->setHeader('Content-type', $contentType, true);

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

       	$helper->resumeableOutput($seekStart);
    }

    /**
     * Download sample action
     */
    public function sampleAction()
    {
      $sampleId = $this->getRequest()->getParam('sample_id', 0);

      $sample = Mage::getModel('downloadable/sample')->load($sampleId);
      if (! $sample->getId() ) {
      	$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
      	return $this->_redirect('downloadable/download/unavailable/sample/'.$sampleId);
      }

      $licenseRequired = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->isDownloadableSampleLicenseRequired();
      $licenseAccepted = ($this->getRequest()->getParam('downloadlicense_sample_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());
      if ($this->isDownloadResumeable() && $this->getFromSession('sample_id_'.$sampleId)) {
      		$licenseAccepted = true;
      }

      if (!$licenseRequired ||($licenseRequired && $licenseAccepted)) {
      		// Limit concurrent downloads
	      	if (!$this->startConcurrentDownload()) {
	      		$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Maximum number of concurrent downloads exceeded. Please wait until your other downloads are finished."));
	      		return $this->_redirect('downloadable/download/unavailable/sample/'.$sampleId);
	      	}
      		// Prepare for resumeable download
      		if ($this->isDownloadResumeable()) {
      			$this->saveInSession('sample_id_'.$sampleId, true);
      		}
      		// Only start download if License is accepted
      		$this->sampleActionDownload();
      } else {
          // Render a message if form is submitted without acceptance
      	  if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
      	  	// Show Message is Form has been submitted without acceptance
          	$this->_getSession()->addError(Mage::helper('downloadable')->__('To download this file these terms need to be accepted.'));
          }

          // Display the License Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'core_download_sample'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
      }
    }

    /*
     * Download link sample action
     */
    public function linkSampleAction()
    {
    	$linkId = $this->getRequest()->getParam('link_id', 0);

    	$link = Mage::getModel('downloadable/link')->load($linkId);
    	if (! $link->getId() ) {
    		$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
    		return $this->_redirect('downloadable/download/unavailable/linksample/'.$linkId);
    	}

    	$licenseRequired = Mage::getModel('downloadplus/config')->isDownloadableProductSampleLicenseRequired();
    	$licenseAccepted = ($this->getRequest()->getParam('downloadlicense_product_sample_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());
    	if ($this->isDownloadResumeable() && $this->getFromSession('link_sample_id_'.$linkId)) {
      		$licenseAccepted = true;
    	}

    	// Only start download if License is accepted
    	if (!$licenseRequired || ($licenseRequired && $licenseAccepted)) {
      		// Limit concurrent downloads
    		if (!$this->startConcurrentDownload()) {
	    		$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Maximum number of concurrent downloads exceeded. Please wait until your other downloads are finished."));
	    		return $this->_redirect('downloadable/download/unavailable/linksample/'.$linkId);
	    	}

      		// Prepare for resumeable download
      		if ($this->isDownloadResumeable()) {
      			$this->saveInSession('link_sample_id'.$linkId, true);
      		}

      		// Start Product download
      		if ($link->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
      			|| $link->getSampleType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE) {
	    		switch (Mage::getModel('downloadplus/config')->getDownloadableDeliveryProductBehaviour()) {
	      			case 'magento':
	      			default:
	      				Mage::unregister('downloadplus_download_resourceobject');
	      				Mage::register('downloadplus_download_resourceobject', $link);
	      				parent::linkSampleAction();
	      				break;
	      			case 'latest':
	      				$this->linkSampleActionDownloadLatest();
	      				break;
	    		}
      		} else {
      			$this->linkSampleActionDownloadLatest();
      		}
    	} else {
    		// Render a message if form is submitted without acceptance
    		if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
    			// Show Message is Form has been submitted without acceptance
    			$this->_getSession()->addError(Mage::helper('downloadable')->__('To access this sample these terms need to be accepted.'));
    		}

    		// Display the License Page
    		$update = $this->getLayout()->getUpdate();
    		$update->addHandle('default');

    		// Adds action handle 'core_download_linksample'
    		$this->addActionLayoutHandles();

    		$this->loadLayoutUpdates();
    		$this->generateLayoutXml()->generateLayoutBlocks();
    		$this->renderLayout();
    	}
    }

    /**
     * Download link action
     */
    public function linkAction()
    {
      $linkId = $this->getRequest()->getParam('id', 0);
      $archiveId = $this->getRequest()->getParam('archive', false);

      $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')->load($linkId, 'link_hash');
      if (! $linkPurchasedItem->getId() ) {
          $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
          return $this->_redirect('downloadable/download/unavailable/link/'.$linkId);
      }

      $licenseRequired = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->isDownloadableProductLicenseRequired();
      $licenseAccepted = ($this->getRequest()->getParam('downloadlicense_product_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());
      if ($this->isDownloadResumeable() && $this->getFromSession('link_id_'.$linkId)) {
      		$licenseAccepted = true;
      }

      if (!$licenseRequired ||($licenseRequired && $licenseAccepted)) {
      		// Limit concurrent downloads
      		if (!$this->startConcurrentDownload()) {
		      	$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Maximum number of concurrent downloads exceeded. Please wait until your other downloads are finished."));
		      	return $this->_redirect('downloadable/download/unavailable/link/'.$linkId);
		    }

      		// Process expiration
      		$extension = Mage::getModel('downloadplus/link_purchased_item_extension')
      						->createExpirationOnDownload($linkPurchasedItem);
      		if ($extension->isExpired()) {
      			$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Link has expired."));
      			return $this->_redirect('downloadable/download/unavailable/link/'.$linkId);
      		}

      		// Prepare for resumeable download
      		if ($this->isDownloadResumeable()) {
      			$this->saveInSession('link_id_'.$linkId, true);
      		}

      		// Start Product download
      		if ($linkPurchasedItem->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_URL
      			|| $linkPurchasedItem->getLinkType()==Pisc_Downloadplus_Helper_Download::LINK_TYPE_FILE) {
      			switch (Mage::getModel('downloadplus/config')->getDownloadableDeliveryProductBehaviour($linkPurchasedItem)) {
      				case Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_MAGENTO:
      				default:
      					Mage::unregister('downloadplus_download_resourceobject');
      					Mage::register('downloadplus_download_resourceobject', $linkPurchasedItem);
      					parent::linkAction();
      					break;
      				case Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST:
      					$this->linkActionDownloadLatest();
      					break;
      			}
      		} else {
      			$this->linkActionDownloadLatest();
      		}
     } else {
          // Render a message if form is submitted without acceptance
      	  if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
      	  	// Show Message if Form has been submitted without acceptance
            $this->_getSession()->addError(Mage::helper('downloadable')->__('To download this file these terms need to be accepted.'));
          }

          // Display the License Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'core_download_link'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
      }
    }

    /**
     * Download link action
     * Modifies: Retrieve most recent file associated with purchased link
     */
    public function linkActionDownloadLatest()
    {
    	Mage::helper('downloadplus/download');

        $id = $this->getRequest()->getParam('id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')->load($id, 'link_hash');
        if (! $linkPurchasedItem->getId() ) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
            return $this->_redirectUrl('downloadable/download/unavailable/link/'.$id);
        }
        if (!Mage::helper('downloadable')->getIsShareable($linkPurchasedItem)) {
            $customerId = $this->_getCustomerSession()->getCustomerId();
            if (!$customerId) {
                $product = Mage::getModel('catalog/product')->load($linkPurchasedItem->getProductId());
                if ($product->getId()) {
                    $notice = Mage::helper('downloadable')->__(
                        'Please log in to download your product or purchase <a href="%s">%s</a>.',
                        $product->getProductUrl(), $product->getName()
                    );
                } else {
                    $notice = Mage::helper('downloadable')->__('Please log in to download your product.');
                }
                $this->_getSession()->addNotice($notice);
                $this->_getCustomerSession()->authenticate($this);
                $this->_getCustomerSession()->setBeforeAuthUrl(Mage::getUrl('downloadable/customer/products'), array('_secure' => true));
                return ;
            }
            $linkPurchased = Mage::getModel('downloadable/link_purchased')->load($linkPurchasedItem->getPurchasedId());
            if ($linkPurchased->getCustomerId() != $customerId) {
                $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
                return $this->_redirectUrl('downloadable/download/unavailable/link/'.$id);
            }
        }

        $downloadsLeft = ($linkPurchasedItem->getNumberOfDownloadsBought()==0) || (($linkPurchasedItem->getNumberOfDownloadsBought()>0) && ($linkPurchasedItem->getNumberOfDownloadsBought() - $linkPurchasedItem->getNumberOfDownloadsUsed())>0);

        if ($linkPurchasedItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
            && $downloadsLeft) {

           	$resource = Mage::helper('downloadplus/resource')->getResource($linkPurchasedItem);
           	$resourceType = Mage::helper('downloadplus/resource')->getResourceType($linkPurchasedItem);

            if ($archiveId) {
            	// Deliver archived downloadable file
            	$archive = Mage::getModel('downloadplus/download_detail')->load($archiveId);
            	if ($archive->getId()==$archiveId) {
            		$resource = Mage::helper('downloadplus/resource')->getResource($archive);
            		$resourceType = Mage::helper('downloadplus/resource')->getResourceType($archive);
            	}
            } elseif (Mage::getModel('downloadplus/config')->getDownloadableDeliveryProductBehaviour($linkPurchasedItem)==Pisc_Downloadplus_Model_Config::CONFIG_BEHAVIOUR_LATEST) {
            	// Deliver current downloadable file
            	/*
            	$link = Mage::getModel('downloadable/link')->load($linkPurchasedItem->getLinkId());
            	if ($link->getId()==$linkPurchasedItem->getLinkId()) {
            		$resource = Mage::helper('downloadplus/resource')->getResource($link);
            		$resourceType = Mage::helper('downloadplus/resource')->getResourceType($link);
            	}
            	*/
				$history = Mage::getModel('downloadplus/link_history')->setLinkPurchasedItem($linkPurchasedItem);
				$resource = $history->getCurrentResource();
				$resourceType = $history->getCurrentResourceType();
            }
            try {
            	$result = $this->_processDownload($resource, $resourceType, $linkPurchasedItem);
            	if ($result === false) {
            		return;
            	}

                $linkPurchasedItem->setNumberOfDownloadsUsed(
                    $linkPurchasedItem->getNumberOfDownloadsUsed()+1
                );
                if ($linkPurchasedItem->getNumberOfDownloadsBought() != 0
                    && !($linkPurchasedItem->getNumberOfDownloadsBought() - $linkPurchasedItem->getNumberOfDownloadsUsed())) {
                    $linkPurchasedItem->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
                }
                $linkPurchasedItem->save();

                if ($result === true) {
                	return;
                }

                exit(0);
            }
            catch (Exception $e) {
                if (!empty($resource) && !empty($resourceType)) {
                	Mage::helper('downloadplus/adminnotification')
    	            	->addNotification('downloadplus-transmission-failed', $e, Mage::helper('downloadplus/download')->setResource($resource, $resourceType)->getFilename());
                } else {
                    Mage::helper('downloadplus/adminnotification')
                        ->addNotification('downloadplus-file-not-found', $e, $linkPurchasedItem->getLinkTitle());
                }
                $this->_getSession()->addError(
                    Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
                );
            }
        } elseif ($linkPurchasedItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__('Link has expired.'));
        } elseif ($linkPurchasedItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__('Link is not available.'));
        } elseif (!$downloadsLeft) {
            $this->_getSession()->addError(
                Mage::helper('downloadable')->__('Sorry, the download limit for the requested link is already consumed.')
            );
        } else {
        	Mage::helper('downloadplus/adminnotification')->addNotification(null, null, Mage::helper('downloadable')->__('Download request for Downloadable Link failed because of Order Status %s.', $linkPurchasedItem->getStatus()));
            $this->_getSession()->addError(
                Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
            );
        }

        $params = array('link' => $id);
        if ($archiveId) {
        	$params['archive'] = $archiveId;
        }

        return $this->_redirectUrl(Mage::getUrl('downloadable/download/unavailable', $params));
    }

    /**
     * Download link sample action
     */
    public function linkSampleActionDownloadLatest()
    {
    	Mage::helper('downloadplus/download');

        $linkId = $this->getRequest()->getParam('link_id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $link = Mage::getModel('downloadable/link')->load($linkId);
        if ($link->getId()) {

        	$resource = Mage::helper('downloadplus/resource')->getResource($link, true);
        	$resourceType = Mage::helper('downloadplus/resource')->getResourceType($link, true);

            if ($archiveId) {
            	// Deliver archived downloadable file
            	$archive = Mage::getModel('downloadplus/download_detail')->load($archiveId);
            	if ($archive->getId()==$archiveId) {
           			$resource = Mage::helper('downloadplus/resource')->getResource($archive);
           			$resourceType = Mage::helper('downloadplus/resource')->getResourceType($archive);
            	}
            }

            try {
                $result = $this->_processDownload($resource, $resourceType, $link);
                if ($result === true || $result === false) {
                	return;
                }
                exit(0);
            } catch (Mage_Core_Exception $e) {
    			Mage::helper('downloadplus/adminnotification')
    				->addNotification('downloadplus-transmission-failed', $e, Mage::helper('downloadplus/download')->setResource($resource, $resourceType)->getFilename());
                $this->_getSession()->addError(
                	Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact store owner.')
                );
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download sample action
     */
    public function sampleActionDownload()
    {
    	Mage::helper('downloadplus/download');

        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $sample = Mage::getModel('downloadable/sample')->load($sampleId);
        if ($sample->getId()) {

        	$resource = Mage::helper('downloadplus/resource')->getSampleResource($sample);
        	$resourceType = Mage::helper('downloadplus/resource')->getSampleResourceType($sample);

            try {
                $result = $this->_processDownload($resource, $resourceType, $sample);
                if ($result === true) {
                	return;
                }
                exit(0);
            } catch (Mage_Core_Exception $e) {
    			Mage::helper('downloadplus/adminnotification')
    				->addNotification('downloadplus-transmission-failed', $e, Mage::helper('downloadplus/download')->setResource($resource, $resourceType)->getFilename());
            	$this->_getSession()->addError(
                	Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact store owner.')
                );
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download customer link action
     */
    public function customerAction()
    {
      $linkId = $this->getRequest()->getParam('id', 0);
      $archiveId = $this->getRequest()->getParam('archive', 0);

      $linkCustomerItem = Mage::getModel('downloadplus/link_customer_item')->load($linkId, 'link_hash');
      if (! $linkCustomerItem->getId() ) {
          $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
          return $this->_redirect('downloadable/download/unavailable/customer/'.$linkId);
      }

      $licenseRequired = Mage::getModel('downloadplus/config')->isDownloadableCustomerDownloadLicenseRequired();
      $licenseAccepted = ($this->getRequest()->getParam('downloadlicense_product_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());

      if (!$licenseRequired ||($licenseRequired && $licenseAccepted)) {
      		// Record download in log
      		if (Mage::getModel('downloadplus/config')->isDownloadableTrackProduct()) {
      			/*
		        $linkCustomerItem = Mage::getModel('downloadplus/link_customer_item')->load($linkId, 'link_hash');
		        if ( $linkCustomerItem->getId() ) {
	      			$log = Mage::getModel('downloadplus/log_link');
	      			$log->trackPurchasedLink($linkCustomerItem);
		        }
		        */
      		}
      		// Start Product download
      		$this->customerActionDownload();
      } else {
          // Render a message if form is submitted without acceptance
      	  if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
      	  	// Show Message if Form has been submitted without acceptance
            $this->_getSession()->addError(Mage::helper('downloadable')->__('To download this file these terms need to be accepted.'));
          }

          // Display the License Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'core_download_link'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
      }
    }

    /**
     * Download Customer Link Item action
     */
    public function customerActionDownload()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $archiveId = $this->getRequest()->getParam('archive', false);

        $linkCustomerItem = Mage::getModel('downloadplus/link_customer_item')->load($id, 'link_hash');
        if (! $linkCustomerItem->getId() ) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
            return $this->_redirect('downloadable/download/unavailable/customer/'.$id);
        }
        if (!Mage::helper('downloadable')->getIsShareable($linkCustomerItem)) {
            $customerId = $this->_getCustomerSession()->getCustomerId();
            if (!$customerId) {
                $product = Mage::getModel('catalog/product')->load($linkCustomerItem->getProductId());
                if ($product->getId()) {
                    $notice = Mage::helper('downloadable')->__(
                        'Please log in to download your product or purchase <a href="%s">%s</a>.',
                        $product->getProductUrl(), $product->getName()
                    );
                } else {
                    $notice = Mage::helper('downloadable')->__('Please log in to download your product.');
                }
                $this->_getSession()->addNotice($notice);
                $this->_getCustomerSession()->authenticate($this);
                $this->_getCustomerSession()->setBeforeAuthUrl(Mage::getUrl('downloadable/customer/products/'), array('_secure' => true));
                return ;
            }
            if ($linkCustomerItem->getPurchasedId()) {
	            $linkPurchased = Mage::getModel('downloadable/link_purchased')->load($linkCustomerItem->getPurchasedId());
	            if ($linkPurchased->getCustomerId() != $customerId) {
	                $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
	                return $this->_redirect('downloadable/download/unavailable/customer/'.$id);
	            }
            } else {
            	if ($linkCustomerItem->getOrderItemId()) {
            		$orderItem = Mage::getModel('sales/order_item')->load($linkCustomerItem->getOrderItemId());
            		$order = $orderItem->getOrder();
            		if ($order->getCustomerId() != $customerId) {
		                $this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
		                return $this->_redirect('downloadable/download/unavailable/customer/'.$id);
            		}
            	}

            }
        }

        $downloadsLeft = ($linkCustomerItem->getNumberOfDownloadsBought()==0) || (($linkCustomerItem->getNumberOfDownloadsBought()>0) && ($linkCustomerItem->getNumberOfDownloadsBought() - $linkCustomerItem->getNumberOfDownloadsUsed())>0);

        if ($linkCustomerItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
            && $downloadsLeft) {

            $resource = '';
            $resourceType = '';

            if ($linkCustomerItem->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $linkCustomerItem->getLinkUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($linkCustomerItem->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('downloadable/file')->getFilePath(
	                    Pisc_Downloadplus_Model_Customer_Download::getBasePath(), $linkCustomerItem->getLinkFile()
	                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $result = $this->_processDownload($resource, $resourceType, $linkCustomerItem);
                if ($result === false) {
                	return;
                }

                $linkCustomerItem->setNumberOfDownloadsUsed(
                    $linkCustomerItem->getNumberOfDownloadsUsed()+1
                );
                if ($linkCustomerItem->getNumberOfDownloadsBought() != 0
                    && !($linkCustomerItem->getNumberOfDownloadsBought() - $linkCustomerItem->getNumberOfDownloadsUsed())) {
                    $linkCustomerItem->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
                }
                $linkCustomerItem->save();
                if ($result === true) {
                	return;
                }
                exit(0);
            }
            catch (Exception $e) {
    			Mage::helper('downloadplus/adminnotification')
    				->addNotification('downloadplus-transmission-failed', $e, Mage::helper('downloadplus/download')->setResource($resource, $resourceType)->getFilename());
            	$this->_getSession()->addError(
                    Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
                );
            }
        } elseif ($linkCustomerItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__('Link has expired.'));
        } elseif ($linkCustomerItem->getStatus() == Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING) {
            $this->_getSession()->addNotice(Mage::helper('downloadable')->__('Link is not available.'));
        } elseif (!$downloadsLeft) {
            $this->_getSession()->addError(
                Mage::helper('downloadable')->__('Sorry, the download limit for the requested link is already consumed.')
            );
        } else {
        	Mage::helper('downloadplus/adminnotification')->addNotification(null, null, Mage::helper('downloadable')->__('Download request for additional Customer Download failed because of Order Status %s.', $linkCustomerItem->getStatus()));
            $this->_getSession()->addError(
                Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
            );
        }
        return $this->_redirect('downloadable/download/unavailable/customer/'.$id);
    }

    /**
     * Download customer link action
     */
    public function productAction()
    {
      $linkId = $this->getRequest()->getParam('id', 0);
      $archiveId = $this->getRequest()->getParam('archive', 0);

      $linkProductItem = Mage::getModel('downloadplus/link_product_item')->load($linkId);
      if (! $linkProductItem->getId() ) {
      	$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
      	return $this->_redirect('downloadable/download/unavailable/additional/'.$linkId);
	  }

	  if ($linkProductItem->getAttribute('downloadable_additional_clogin')=='1' && !Mage::helper('customer')->isLoggedIn()) {
		$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested download requires to be a registered customer."));
		Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('downloadable/download/product', array('id'=>$linkId)));
  		return $this->_redirect('downloadable/download/register/additional/'.$linkId);
	  }

      $licenseRequired = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->isDownloadableProductDownloadLicenseRequired();
      $licenseAccepted = ($this->getRequest()->getParam('downloadlicense_product_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());

      if (!$licenseRequired ||($licenseRequired && $licenseAccepted)) {
      		// Start Product download
      		$this->productActionDownload();
      } else {
          // Render a message if form is submitted without acceptance
      	  if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
      	  	// Show Message if Form has been submitted without acceptance
            $this->_getSession()->addError(Mage::helper('downloadable')->__('To download this file these terms need to be accepted.'));
          }

          // Display the License Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'core_download_link'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
      }
    }

    /**
     * Download Product Link Item action
     */
    public function productActionDownload()
    {
    	Mage::helper('downloadplus/download');

    	$id = $this->getRequest()->getParam('id', 0);
    	$archiveId = $this->getRequest()->getParam('archive', false);

    	$linkProductItem = Mage::getModel('downloadplus/link_product_item')->load($id);
    	if (! $linkProductItem->getId() ) {
    		$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
    		return $this->_redirect('downloadable/download/unavailable/product/'.$id);
    	}

    	$resource = Mage::helper('downloadplus/resource')->getResource($linkProductItem);
    	$resourceType = Mage::helper('downloadplus/resource')->getResourceType($linkProductItem);

    	try {
    		$result = $this->_processDownload($resource, $resourceType, $linkProductItem);
            if ($result === true) {
            	return;
            }
    		exit(0);
    	}
    	catch (Exception $e) {
    		Mage::helper('downloadplus/adminnotification')
    			->addNotification('downloadplus-transmission-failed', $e, Mage::helper('downloadplus/download')->setResource($resource, $resourceType)->getFilename());
    		$this->_getSession()->addError(
    			Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.')
    		);
    	}
    	return $this->_redirect('downloadable/download/unavailable/product/'.$id);
    }

    /**
     * Download serialnumber action
     */
    public function serialnumberAction()
    {
      $serialId = $this->getRequest()->getParam('id', 0);
      $serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($serialId, 'serial_hash');
      if (!$serial->getId()) {
      	$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
      	return $this->_redirect('downloadable/download/unavailable/serialnumber/'.$serialId);
      }

      $licenseRequired = Mage::getModel('downloadplus/config')->setStore(Mage::helper('downloadplus')->getStore())->isDownloadableSerialnumberDownloadLicenseRequired();
      $licenseAccepted = ($this->getRequest()->getParam('downloadlicense_serialnumber_accepted', 0)==1) && ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey());

      if (!$licenseRequired ||($licenseRequired && $licenseAccepted)) {
      		$this->serialnumberActionDownload();
      } else {
          // Render a message if form is submitted without acceptance
      	  if ($this->getRequest()->getParam('formKey', false)===Mage::getSingleton('core/session')->getFormKey()) {
      	  	// Show Message if Form has been submitted without acceptance
            $this->_getSession()->addError(Mage::helper('downloadable')->__('To download this file these terms need to be accepted.'));
          }

          // Display the License Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'core_download_link'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
      }
    }

    /**
     * Send serialnumber as file
     */
    public function serialnumberActionDownload()
    {
      $serialId = $this->getRequest()->getParam('id', 0);

      $customerId = $this->_getCustomerSession()->getCustomerId();
      if (!$customerId) {
      	$notice = Mage::helper('downloadplus')->__('Please log in to download your serialnumber.');
      	$this->_getSession()->addNotice($notice);
      	$this->_getCustomerSession()->authenticate($this);
      	$this->_getCustomerSession()->setBeforeAuthUrl(Mage::getUrl('downloadable/customer/products/'), array('_secure' => true));
      	return ;
      }

      $config = Mage::getModel('downloadplus/config');
      $serial = Mage::getModel('downloadplus/link_purchased_item_serialnumber')->load($serialId, 'serial_hash');
      $customer = $serial->getCustomer();
      if ($config->isDownloadSerialnumbers() && $customer->getId() && $customer->getId()==$customerId) {
      	try {
	      	$helper = Mage::helper('downloadable/file');

	      	$fileName = $serial->getDownloadFilename();
	      	$contentType = $helper->getFileType($fileName);

	        $this->getResponse()
	            ->setHttpResponseCode(200)
	            ->setHeader('Pragma', 'public', true)
	            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
	            ->setHeader('Content-type', $contentType, true);

	        if ($fileSize = strlen($serial->getSerialNumber())) {
	            $this->getResponse()
	                ->setHeader('Content-Length', $fileSize);
	        }
	        $this->getResponse()
	        	->setHeader('Content-Disposition', 'attachment; filename='.$fileName);

	        $this->getResponse()
	            ->clearBody();
	        $this->getResponse()
	            ->sendHeaders();

	        print $serial->getSerialNumber();
	        exit(0);
      	}
      	catch (Mage_Core_Exception $e) {
   			Mage::helper('downloadplus/adminnotification')
   				->addNotification('downloadplus-transmission-failed', $e, $fileName);
      		$this->_getSession()
      			->addError(Mage::helper('downloadable')->__('Sorry, there was an error getting requested content. Please contact store owner.'));
        }
      } else {
      	$this->_getSession()->addNotice(Mage::helper('downloadable')->__("Requested link doesn't exist."));
      }

      return $this->_redirect('downloadable/download/unavailable/serialnumber/'.$serialId);
    }

    /*
     * Download Unavailable Action
     */
    public function unavailableAction()
    {
    	// Display the Unavailable Page
    	$update = $this->getLayout()->getUpdate();
    	$update->addHandle('default');

    	// Adds action handle 'core_download_unavailable'
    	$this->addActionLayoutHandles();

    	$this->loadLayoutUpdates();
    	$this->generateLayoutXml()->generateLayoutBlocks();
    	$this->renderLayout();
    }

    /*
     * Register/Login for Download Action
     */
    public function registerAction()
    {
    	// Display the Register/Login Page
    	$update = $this->getLayout()->getUpdate();
    	$update->addHandle('default');

    	// Adds action handle 'core_download_unavailable'
    	$this->addActionLayoutHandles();

    	$this->loadLayoutUpdates();
    	$this->generateLayoutXml()->generateLayoutBlocks();
    	$this->renderLayout();
    }

    /*
     * Placeholder for DownloadplusBonus Actions - are connected through own Observer
     */
    public function bonusAction() {}

}
