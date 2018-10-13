<?php

require_once 'Mage/Catalog/controllers/ProductController.php';  
class Krishinc_Pdfcatalog_ProductController extends Mage_Catalog_ProductController
{
	public function pdfviewAction()
	{   
		
		 if (!$this->_initProduct()) {
		 	
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
            return;
        }
        
        $this->loadLayout();
        $this->renderLayout();
         
	}
	
	public function licenseAction()
	{
        $this->loadLayout();
        $this->renderLayout();
	}
}