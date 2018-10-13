<?php

class Krishinc_Grouped_Helper_Constants extends Mage_Core_Helper_Abstract
{
   
    const PRODUCT_TYPE_BOOKS = '128';
    const PRODUCT_TYPE_ANDROID_APP = '388';
    const PRODUCT_TYPE_IOS_APP = '389';
    const PRODUCT_TYPE_WIN_APP = '387';
    const PRODUCT_TYPE_WIN_SOFTWARE = '390';
    const PRODUCT_TYPE_WINMAC_SOFTWARE = '127';
    const PRODUCT_TYPE_EBOOK = '126';
	const PRODUCT_TYPE_ACCESSORIES = '124';
   const PRODUCT_TYPE_Bundle_Ebook = '413';
const PRODUCT_TYPE_Bundle_book = '125';
    const SOFTWARE_DEMO_FLAG_ONLINE = '365';
    const SOFTWARE_DEMO_FLAG_DOWNLOAD = '366';
    
    const CRITICAL_SERIES_ATTR_SET = 11;
    const CRITICAL_GROUPED_ATTR_SET = 12;
    
    public static $non_shipping_countries = array('KR');
    
    public function getNonShippingCountries() {
        return self::$non_shipping_countries;
    }
}
