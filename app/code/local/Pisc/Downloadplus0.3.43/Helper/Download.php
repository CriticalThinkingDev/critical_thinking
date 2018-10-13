<?php
/**
 * Downloadplus download helper
 * @category   	Pisc
 * @package    	Pisc_Downloadplus
 * @copyright  	Copyright (c) 2011 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License
 * @version		0.1.2
 */

class Pisc_Downloadplus_Helper_Download extends Mage_Downloadable_Helper_Download
{

	const LINK_TYPE_AWSS3 = 'aws-s3';
	const LINK_TYPE_AWSCF = 'aws-cf';
	const LINK_TYPE_FILELOCAL = 'file-local';
	
    /*
     * Tracks concurrent downloads, returns TRUE if allowed
     */
    public function startConcurrentDownload()
    {
    	$result = false;
        $config = Mage::getModel('downloadplus/config');
    	$limit = $config->getDownloadableDeliveryConcurrentDownloads();
    	if (is_null($limit)) {
    		$result = true;
    	} else {
    		$active = Mage::helper('downloadplus')->getFromSession('active_concurrent_downloads');
    		if ($active===false) { $active=0; }
    		if (is_numeric($active) && $active<$limit) {
    			$active++;
    			Mage::helper('downloadplus')->saveInSession('active_concurrent_downloads', $active);
    			$result = true;
    		}
    	}
    	return $result;
    }

    /*
     * Removes a concurrent download from tracking, returns remaining number of concurrent downloads
     */
    public function closeConcurrentDownload()
    {
    	$result = false;
        $config = Mage::getModel('downloadplus/config');
    	$limit = $config->getDownloadableDeliveryConcurrentDownloads();
    	if (!is_null($limit)) {
    		$active = Mage::helper('downloadplus')->getFromSession('active_concurrent_downloads');
	    	if (is_numeric($active) && $active>0) {
	    		$active = $active-1;
	    		Mage::helper('downloadplus')->saveInSession('active_concurrent_downloads', $active);
	    		$result = $active;
	       	}
    	}
       	return $result;
    }

    public function resumeableOutput($seekStart=null, $maxSpeed=null)
    {
    	// URL's are handled as per default
    	if (is_null($seekStart) || $this->_linkType == self::LINK_TYPE_URL) {
            return $this->output();
    	}

    	// Set default download speed
    	$config = Mage::getModel('downloadplus/config');
    	if (is_null($maxSpeed)) { $maxSpeed = $config->getDownloadableDeliveryResumeableSpeed(); }

    	// Seek to start of download part
    	$handle = fopen($this->_resourceFile,"rb");
    	if ($handle) {
    		try {
		    	@fseek($handle, $seekStart);
		    	// Start buffered download
		    	while(!feof($handle) and !connection_aborted()) {
		    		//	Reset time limit for big files
		    		@set_time_limit(0);
		    		if (!connection_aborted()) {
			    		if ($maxSpeed) {
			    			$start = microtime(true);
			    			print(fread($handle, ceil(1024*$maxSpeed)));
			    			@flush();
			    			@ob_flush();
			    			$end = microtime(true);
			    			$wait = ceil((1+$start-$end)*1000*1000);
			    			if ($wait>0) { usleep($wait); }
			    		} else {
			    			print(fread($handle, 1024));
			    			@flush();
			    			@ob_flush();
			    		}
		    		}
		    	}
		    	fclose($handle);
    		}
    		catch (Exception $e) {
    			Mage::helper('downloadplus/adminnotification')->addNotification('downloadplus-transmission-failed', $e, $this->_resourceFile);
    		}
    		// Stop download processing when browser connection is aborted
    		if (connection_aborted()) {
    			exit(0);
    		}
    	} else {
		    Mage::helper('downloadplus/adminnotification')->addNotification('downloadplus-file-not-found', null, $this->_resourceFile);
    		Mage::throwException(Mage::helper('downloadplus')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
    	}

    }

}
