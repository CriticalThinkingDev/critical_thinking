<?php
class Krishinc_Pressrelease_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {   
		$this->loadLayout();     
		$this->renderLayout();
    }
    
     public function viewAction()
    { 
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/pressrelease?id=15 
    	 *  or
    	 * http://site.com/pressrelease/id/15 	
    	 */
    	 
		$pressrelease_id = $this->getRequest()->getParam('id');

  		if($pressrelease_id != null && $pressrelease_id != '')	{
			$pressrelease = Mage::getModel('pressrelease/pressrelease')->load($pressrelease_id)->getData();
		} else {
			$pressrelease = null;
		}	
		 
			
		$this->loadLayout();     
		$this->renderLayout();
    }
}