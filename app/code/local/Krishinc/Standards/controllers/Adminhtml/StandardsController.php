<?php
/**
created by : varsha parmar
created on : 26 March 2013

================================================
Back End Author management controller to handle actions
**/
class Krishinc_Standards_Adminhtml_StandardsController extends Mage_Adminhtml_Controller_Action
{
 
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('standards/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Standards Manager'), Mage::helper('adminhtml')->__('Standards Manager'));
        return $this;
    }   
   
    public function indexAction() {
        $this->_initAction();       
        $this->renderLayout();
    }
 
    public function editAction()
    {
		
        $standardsId     = $this->getRequest()->getParam('id');
        $standardsModel  = Mage::getModel('standards/standards')->load($standardsId);
		
        if ($standardsModel->getId() || $standardsId == 0) {
 
            Mage::register('standards_data', $standardsModel);
			
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$standardsModel->setData($data);
			}
						
            $this->loadLayout();
            $this->_setActiveMenu('standards/items');
           
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Standards Manager'), Mage::helper('adminhtml')->__('Standards Manager'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('standards/adminhtml_standards_edit'))
                 ->_addLeft($this->getLayout()->createBlock('standards/adminhtml_standards_edit_tabs'));
               
            $this->renderLayout();
        } else { 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('standard does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
		
        if ( $this->getRequest()->getPost() ) {
		
            $postData = $this->getRequest()->getPost();				
			$description =  $postData['description'];


			$model = Mage::getModel('standards/standards');	
			$sku = $model->getProductSku($postData['product_id']);				
			$model->setData($postData)->setId($this->getRequest()->getParam('id'));
			
			try {	
					$model->setSku($sku);											
					$model->save();
										
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('standard was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
				
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
			
				$options = array();
				$option_id = $this->getRequest()->getParam('id');
                $standardsModel = Mage::getModel('standards/standards')->load($this->getRequest()->getParam('id'), 'standards_id');
				          
				
				if ($standardsModel->getStandardsId()) {			
					$standardsModel->delete();
				}
					 
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('standard was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
		
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('standards/adminhtml_standards_grid')->toHtml()
        );
    }
	
		
	
	public function exportCsvAction()
    {
		/* create csv export setting */
        $fileName   = 'author.csv';
        $content    = $this->getLayout()->createBlock('standards/adminhtml_standards_grid')
            ->getCsv();
 
        $this->_sendUploadResponse($fileName, $content);
    }
 
    public function exportXmlAction()
    {
		/* create xml export setting */
        $fileName   = 'author.xml';
        $content    = $this->getLayout()->createBlock('standards/adminhtml_standards_grid')
            ->getXml();
 
        $this->_sendUploadResponse($fileName, $content);
    
	}
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
		/* export content */
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');

        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);

        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	public function massDeleteAction() {
        $standardIds = $this->getRequest()->getParam('standards');
        if(!is_array($standardIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($standardIds as $sId) {
                    $standards = Mage::getModel('standards/standards')->load($sId);

					if ($standards->getStandardsId()) {			
						$standards->delete();
					}
					
				}
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($standardIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function massStatusAction()
    {
        $standardIds = $this->getRequest()->getParam('standards');
        if(!is_array($standardIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Standard(s)'));
        } else {
            try {
                foreach ($standardIds as $id) {
                    $standard = Mage::getModel('standards/standards')
                        ->load($id)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($standardIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}