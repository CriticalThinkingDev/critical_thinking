<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * RSS Feed Controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author		Software Group @ PILLWAX Industrial Solutions Consulting (technology.license@pillwax.com)
 * @version		0.1.2
 */

class Pisc_Downloadplus_RssController extends Mage_Core_Controller_Front_Action
{

	protected function _initFeed()
	{
		$params = new Varien_Object;
    	if ($param = $this->getRequest()->getParam('product')) {
    		$params->setData('product_sku', $param);
    	}
    	if ($param = $this->getRequest()->getParam('category')) {
    		$params->setData('category_url_key', $param);
    	}
    	if ($param = $this->getRequest()->getParam('cid')) {
    		$params->setData('category_id', $param);
    	}
    	if ($param = $this->getRequest()->getParam('store')) {
    		$params->setData('store_id', $param);
    	} else {
    		$params->setData('store_id', Mage::app()->getStore()->getId());
    	}
    	Mage::register('downloadplus_rss_feed', $params);
    	return $this;
	}

    public function indexAction()
    {
    	$config = Mage::getModel('downloadplus/config');

        if ($config->isDownloadableRssFeed()) {
        	$this->updatesAction();
        	/*
            $this->loadLayout();
    	    $this->renderLayout();
    	    */
        } else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('defaultNoRoute');
        }
    }

    public function nofeedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');
    	$this->loadLayout(false);
       	$this->renderLayout();
    }

    public function updatesAction()
    {
    	$config = Mage::getModel('downloadplus/config');
    	$this->_initFeed();

        if ($config->isDownloadableRssFeed()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
    	    $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('nofeed','index','rss');
            return;
        }
    }

    public function additionalAction()
    {
    	$config = Mage::getModel('downloadplus/config');
    	$this->_initFeed();
    
    	if ($config->isDownloadableRssFeed()) {
    		$this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    		$this->loadLayout(false);
    		$this->renderLayout();
    	} else {
    		$this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
    		$this->getResponse()->setHeader('Status','404 File not found');
    		$this->_forward('nofeed','index','rss');
    		return;
    	}
    }
    
}