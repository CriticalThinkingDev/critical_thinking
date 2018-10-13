<?php
/**
 * Configuration pathes storage
 *
 * @category   	Pisc
 * @package    	Pisc_Downloadplus
 * @author		PILLWAX Industrial Solutions Consulting
 * @version		0.1.2 
 */

class Pisc_Downloadplus_Model_Config extends Mage_Core_Model_Config_Data
{
    // Configuration Paths
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_REQUIRED = 'catalog/downloadable_license/product_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT = 'catalog/downloadable_license/product_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_SAMPLE_REQUIRED = 'catalog/downloadable_license/product_sample_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_SAMPLE = 'catalog/downloadable_license/product_sample_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SAMPLE_REQUIRED = 'catalog/downloadable_license/sample_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SAMPLE = 'catalog/downloadable_license/sample_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_CUSTOMER_REQUIRED = 'catalog/downloadable_license/additional_customer_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_CUSTOMER = 'catalog/downloadable_license/additional_customer_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_PRODUCT_REQUIRED = 'catalog/downloadable_license/additional_product_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_PRODUCT = 'catalog/downloadable_license/additional_product_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SERIALNUMBER_REQUIRED = 'catalog/downloadable_license/serialnumber_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SERIALNUMBER = 'catalog/downloadable_license/serialnumber_license';

    const CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_PRODUCTS_REQUIRED = 'catalog/downloadable_tracking/product_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_SAMPLES_REQUIRED = 'catalog/downloadable_tracking/sample_required';
    const CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_RSS_FEED = 'catalog/downloadable_tracking/rss_feed';

    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_PRODUCT_BEHAVIOUR = 'catalog/downloadable_delivery/product_behaviour';
    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_FILENAME_MIXEDCASE = 'catalog/downloadable_delivery/filename_mixedcase';

    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_FORCE_SECURE = 'catalog/downloadable_delivery/force_secure';
    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_EMAIL_IDENTITY = 'catalog/downloadable_delivery/email_identity';
    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_EMAIL_TEMPLATE = 'catalog/downloadable_delivery/email_template';

    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_RESUMEABLE = 'catalog/downloadable_delivery/resumeable';
    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_RESUMEABLE_SPEED = 'catalog/downloadable_delivery/resumeable_speed';
    const CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_CONCURRENT_DOWNLOADS = 'catalog/downloadable_delivery/concurrent_downloads';

    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_ORDERITEMSTATUS_PRODUCTS = 'catalog/downloadable_serialnumbers/order_item_status_products';
    
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_SEND_EMAIL = 'catalog/downloadable_serialnumbers/send_email';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_EMAIL_TEMPLATE = 'catalog/downloadable_serialnumbers/email_template';
    
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_AS_FILE = 'catalog/downloadable_serialnumbers/as_file';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_FILENAME_PATTERN = 'catalog/downloadable_serialnumbers/filename_pattern';

    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_COUNT = 'catalog/downloadable_serialnumbers/notification_count';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_IDENTITY = 'catalog/downloadable_serialnumbers/notification_email_identity';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_TEMPLATE = 'catalog/downloadable_serialnumbers/notification_email_template';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_SEND_TO = 'catalog/downloadable_serialnumbers/notification_email_send_to';

    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_PENDING_EMAIL_TEMPLATE = 'catalog/downloadable_serialnumbers/pending_email_template';
    const CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_PENDING_EMAIL_SEND_TO = 'catalog/downloadable_serialnumbers/pending_email_send_to';

    const CONFIG_XML_PATH_OPTION_PRODUCT_RSS_FEED = 'catalog/downloadable_tracking/option_product_rss_feed';

    const CONFIG_XML_PATH_OPTION_ADMINISTRATOR_NOTIFICATIONS = 'catalog/downloadable_tracking/option_administrator_notifications';
    const CONFIG_XML_PATH_OPTION_ADMINNOTIFICATIONS_TYPE = 'catalog/downloadable_tracking/option_adminnotifications_type';

    const CONFIG_XML_PATH_CATALOG_PRODUCT_DUPLICATE_SAMPLES = 'catalog/downloadable_edit/catalog_product_duplicate_samples';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DUPLICATE_LINKS = 'catalog/downloadable_edit/catalog_product_duplicate_links';

    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_URL = 'catalog/downloadable_edit/default_expiry_type_url';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_URL = 'catalog/downloadable_edit/default_expiry_duration_url';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_FILE = 'catalog/downloadable_edit/default_expiry_type_file';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_FILE = 'catalog/downloadable_edit/default_expiry_duration_file';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_AWSS3 = 'catalog/downloadable_edit/default_expiry_type_awss3';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_AWSS3 = 'catalog/downloadable_edit/default_expiry_duration_awss3';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_AWSCF = 'catalog/downloadable_edit/default_expiry_type_awscf';
    const CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_AWSCF = 'catalog/downloadable_edit/default_expiry_duration_awscf';
    
    
    const CONFIG_NO = 0;
    const CONFIG_YES = 1;
    const CONFIG_TRISTATE = 2;

    const SEVERITY_CRITICAL = 1;
    const SEVERITY_MAJOR    = 2;
    const SEVERITY_MINOR    = 3;
    const SEVERITY_NOTICE   = 4;

    const CONFIG_BEHAVIOUR_LATEST = 'latest';
    const CONFIG_BEHAVIOUR_MAGENTO = 'magento';

    const SERIALNUMBER_NONE = '::none';
    const SERIALNUMBER_POOL_PRODUCT = '::product';
    const SERIALNUMBER_POOL_GLOBAL = 'global::';

    const SERIALNUMBER_EMAIL_SEND_NONE = 'none';
    const SERIALNUMBER_EMAIL_SEND_ALWAYS = 'always';
    const SERIALNUMBER_EMAIL_SEND_FRONTEND = 'frontend';
    
    const CONFIG_DOWNLOAD_RESUME_OFF = 0;
    const CONFIG_DOWNLOAD_RESUME_ON = 1;
    const CONFIG_DOWNLOAD_RESUME_XSENDFILE = 2;
    const CONFIG_DOWNLOAD_RESUME_XLIGHTTPSENDFILE = 3;
    const CONFIG_DOWNLOAD_RESUME_XACCELREDIRECT = 4;

    const LINK_STATUS_UPDATE = 'update';
    const LINK_STATUS_REFRESH = 'refresh';

    protected $_storeId = null;

    
    public function __construct()
    {
    	if ($this->isAdminSession()) {
    		$store = Mage::app()->getRequest()->getParam('store');
    		$this->setStore($store);
    	}
    	return $this;
    }
    
    /*
     * Set the store id for which to retrieve configuration
    */
    public function setStore($store)
    {
    	if ($store instanceof Mage_Core_Model_Store) {
    		$this->_storeId = $store->getId();
    	} else {
    		$this->_storeId = $store;
    	}
    	// Set Admin store to default store
    	if ($this->_storeId==0) { $this->_storeId = null; }
    	return $this;
    }
    
    /*
    * Returns true if in Admin Session
    */
    protected function isAdminSession()
    {
    	$session = Mage::getSingleton('admin/session');
    	if ($session) {
    		return $session->isLoggedIn();
    	}
    	return false;
    }
    
    
    /**
     * Check if Downloadable Product License is required
     */
    public function isDownloadableProductLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
     * Check if Downloadable Product Sample License is required
     */
    public function isDownloadableProductSampleLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_SAMPLE_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
     * Check if Downloadable Sample License is required
     */
    public function isDownloadableSampleLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SAMPLE_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
     * Check if Customer Additional Downloadable License is required
     */
    public function isDownloadableCustomerDownloadLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_CUSTOMER_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
     * Check if Product Additional Downloadable License is required
     */
    public function isDownloadableProductDownloadLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_PRODUCT_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
     * Check if Serialnumber Downloadable License is required
     */
    public function isDownloadableSerialnumberDownloadLicenseRequired()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SERIALNUMBER_REQUIRED, $this->_storeId) == self::CONFIG_YES;
    }

    /**
	 * Gets the Downloadable Products Default License
	 */
	 public function getDownloadableProductDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT, $this->_storeId);
	 }

	/**
	 * Gets the Downloadable Products Sample Default License
	 */
	 public function getDownloadableProductSampleDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_PRODUCT_SAMPLE, $this->_storeId);
	 }

	/**
	 * Gets the Downloadable Samples Default License
	 */
	 public function getDownloadableSampleDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SAMPLE, $this->_storeId);
	 }

	/**
	 * Gets the Customer Additional Download Default License
	 */
	 public function getDownloadableCustomerDownloadDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_CUSTOMER, $this->_storeId);
	 }

	/**
	 * Gets the Product Additional Download Default License
	 */
	 public function getDownloadableProductDownloadDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_ADDITIONAL_PRODUCT, $this->_storeId);
	 }

	/**
	 * Gets the Serialnumber Download Default License
	 */
	 public function getDownloadableSerialnumberDownloadDefaultLicense() {
		return Mage::getStoreConfig(Pisc_Downloadplus_Model_Config::CONFIG_XML_PATH_DOWNLOADABLE_LICENSE_SERIALNUMBER, $this->_storeId);
	 }

	/**
	 * Gets if Logging for Sample Downloads is on
	 */
	 public function isDownloadableTrackSample() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_SAMPLES_REQUIRED, $this->_storeId) == self::CONFIG_YES;
	 }

	/**
	 * Gets if Logging for Sample Downloads is on
	 */
	 public function isDownloadableTrackProduct() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_PRODUCTS_REQUIRED, $this->_storeId) == self::CONFIG_YES;
	 }

	/**
	 * Gets if RSS Feed is on
	 */
	 public function isDownloadableRssFeed() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_TRACKING_RSS_FEED, $this->_storeId) == self::CONFIG_YES;
	 }

	 /*
	  * Returns the configured behaviour for downloading products
	  */
	 public function getDownloadableDeliveryProductBehaviour() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_PRODUCT_BEHAVIOUR, $this->_storeId);
	 }

	 /*
	  * Returns the configured behaviour for filename case
	  */
	 public function getDownloadableDeliveryFilenameMixedcase() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_FILENAME_MIXEDCASE, $this->_storeId);
	 }

	 /*
	  * Returns the configured behaviour for download resume
	  */
	 public function getDownloadableDeliveryResumeable() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_RESUMEABLE, $this->_storeId);
	 	if (empty($result)) { $result = self::CONFIG_DOWNLOAD_RESUME_OFF; }
		return $result;
	 }

	 /*
	  * Returns the configured behaviour for download speed
	  */
	 public function getDownloadableDeliveryResumeableSpeed() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_RESUMEABLE_SPEED, $this->_storeId);
	 	if (empty($result)) { $result = null; }
		return $result;
	 }

	 /*
	  * Returns the configured behaviour for download speed
	  */
	 public function getDownloadableDeliveryConcurrentDownloads() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_CONCURRENT_DOWNLOADS, $this->_storeId);
	 	if (empty($result)) { $result = null; }

	 	// When redirecting to X-Sendfile modules disable concurrent download tracking
	 	if (Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_RESUMEABLE)>self::CONFIG_DOWNLOAD_RESUME_ON) { $result = null; }

		return $result;
	 }

	 /*
	  * Returns if the configured behaviour for downloading products is on "LATEST"
	  */
	 public function isDownloadableDeliveryProductLatest() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_PRODUCT_BEHAVIOUR, $this->_storeId)==self::CONFIG_BEHAVIOUR_LATEST;
	 }

	/**
	 * Gets if Logging for Sample Downloads is on
	 */
	 public function isDownloadForceSecure() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_FORCE_SECURE, $this->_storeId) == self::CONFIG_YES;
	 }

	 /*
	  * Returns the Email Identity for Transactional Emails
	  */
	 public function getDownloadableDeliveryEmailIdentity() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_EMAIL_IDENTITY, $this->_storeId);
	 	if (empty($result)) {
	 		$result = 'general';
	 	}
		return $result;
	 }

	 /*
	  * Returns the Email Template for Transactional Emails
	  */
	 public function getDownloadableDeliveryEmailTemplate() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_DELIVERY_EMAIL_TEMPLATE, $this->_storeId);
	 	if (empty($result)) {
	 		$result = 'catalog_downloadable_delivery_email_template';
	 	}
		return $result;
	 }

	 /*
	  * Returns Option ADMINNOTIFICATIONS TYPE
	  */
	 public function getOptionAdminnotificationsType() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_OPTION_ADMINNOTIFICATIONS_TYPE, $this->_storeId);
	 	if (!$result) {
	 		$result = self::SEVERITY_NOTICE;
	 	}
		return $result;
	 }

	 /*
	  * Returns Option ADMINISTRATOR NOTIFICATIONS
	  */
	 public function getOptionAdministratorNotifications() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_OPTION_ADMINISTRATOR_NOTIFICATIONS, $this->_storeId)==self::CONFIG_YES;
		return $result;
	 }

	/**
	 * Gets the Option to update notifications from the Product RSS Feed
	 */
	 public function getOptionProductRssFeed() {
	 	return Mage::getStoreConfig(self::CONFIG_XML_PATH_OPTION_PRODUCT_RSS_FEED, $this->_storeId);
	 }

	/**
	 * Gets if Serialnumbers are downloadable
	 */
	 public function isDownloadSerialnumbers() {
		return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_AS_FILE, $this->_storeId) == self::CONFIG_YES;
	 }

	/**
	 * Gets the Filename Pattern for downloadable Serialnumbers
	 */
	 public function getDownloadableSerialnumbersFilenamePattern() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_FILENAME_PATTERN, $this->_storeId);
	 	if (empty($result)) {
	 		$result = 'SERIALNUMBER.txt';
	 	}
	 	return $result;
	 }

	 /*
	  * Email notifications on Serialnumbers
	  */
	 public function getDownloadableSerialnumbersSendEmail() {
	 	return Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_SEND_EMAIL, $this->_storeId);
	 }
	 public function isDownloadableSerialnumbersSendEmailAdmin() {
	 	$result = ($this->getDownloadableSerialnumbersSendEmail()==self::SERIALNUMBER_EMAIL_SEND_ALWAYS && $this->isAdminSession());
	 	return $result;
	 }
	 public function isDownloadableSerialnumbersSendEmailFrontend() {
	 	$result = ($this->getDownloadableSerialnumbersSendEmail()==self::SERIALNUMBER_EMAIL_SEND_ALWAYS) ||
	 	($this->getDownloadableSerialnumbersSendEmail()==self::SERIALNUMBER_EMAIL_SEND_FRONTEND && !$this->isAdminSession());
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersEmailTemplate() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_EMAIL_TEMPLATE, $this->_storeId);
	 	if (empty($result)) {
	 		$result = 'catalog_downloadable_serialnumbers_email_template';
	 	}
	 	return $result;
	 }
	 
	 public function getDownloadableSerialnumbersNotificationCount() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_COUNT, $this->_storeId);
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersNotificationEmailIdentity() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_IDENTITY, $this->_storeId);
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersNotificationEmailTemplate() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_TEMPLATE, $this->_storeId);
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersNotificationEmailSendTo() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_NOTIFICATION_EMAIL_SEND_TO, $this->_storeId);
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersPendingEmailTemplate() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_PENDING_EMAIL_TEMPLATE, $this->_storeId);
	 	return $result;
	 }
	 public function getDownloadableSerialnumbersPendingEmailSendTo() {
	 	$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_DOWNLOADABLE_SERIALNUMBERS_PENDING_EMAIL_SEND_TO, $this->_storeId);
	 	return $result;
	 }

	 /*
	  * Backend Options
	  */
	 public function isCatalogProductDuplicateSamples() {
	 	return Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DUPLICATE_SAMPLES, $this->_storeId) == self::CONFIG_YES;
	 }
	 public function isCatalogProductDuplicateLinks() {
	 	return Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DUPLICATE_LINKS, $this->_storeId) == self::CONFIG_YES;
	 }
	 public function getCatalogProductDefaultExpiryType($type) {
	 	$result = null;
	 	switch ($type) {
	 		case 'file':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_FILE, $this->_storeId);
	 			break;
	 		case 'url':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_URL, $this->_storeId);
	 			break;
	 		case 'aws-s3':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_AWSS3, $this->_storeId);
	 			break;
	 		case 'aws-cf':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_TYPE_AWSCF, $this->_storeId);
	 			break;
	 	}
	 	return $result;
	 }
	 public function getCatalogProductDefaultExpiryDuration($type) {
	 	$result = null;
	 	switch ($type) {
	 		case 'file':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_FILE, $this->_storeId);
	 			break;
	 		case 'url':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_URL, $this->_storeId);
	 			break;
	 		case 'aws-s3':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_AWSS3, $this->_storeId);	 			
	 			break;
	 		case 'aws-cf':
	 			$result = Mage::getStoreConfig(self::CONFIG_XML_PATH_CATALOG_PRODUCT_DEFAULT_EXPIRY_DURATION_AWSCF, $this->_storeId);
	 			break;
	 	}
	 	return $result;
	 }
	 
}
