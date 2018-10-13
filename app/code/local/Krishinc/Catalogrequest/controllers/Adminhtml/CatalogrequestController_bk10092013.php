<?php

class Krishinc_Catalogrequest_Adminhtml_CatalogrequestController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalogrequest/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		
		$this->_initAction()
			->renderLayout();
	}
	public function viewAction() {
	    $id     = $this->getRequest()->getParam('id'); 
		$model  = Mage::getModel('catalogrequest/catalogrequest')->load($id);

		if ($model->getId() || $id == 0) {
			$this->_initAction()
			->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrequest')->__('Item does not exist'));
			$this->_redirect('*/*/');  
		}
	  
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('catalogrequest/catalogrequest');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $catalogrequestIds = $this->getRequest()->getParam('catalogrequest');
        if(!is_array($catalogrequestIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($catalogrequestIds as $catalogrequestId) {
                    $catalogrequest = Mage::getModel('catalogrequest/catalogrequest')->load($catalogrequestId);
                    $catalogrequest->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($catalogrequestIds)
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
        $catalogrequestIds = $this->getRequest()->getParam('catalogrequest');
        if(!is_array($catalogrequestIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($catalogrequestIds as $catalogrequestId) {
                    $catalogrequest = Mage::getSingleton('catalogrequest/catalogrequest')
                        ->load($catalogrequestId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($catalogrequestIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
     {
         $fileName   = 'catalogrequest.csv';
         $content    = $this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_customgrid')
             ->getCsv();

         $this->_sendUploadResponse($fileName, $content);
     }

     public function exportXmlAction()
     {
         $fileName   = 'catalogrequest.xml';
         $content    = $this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_customgrid')
             ->getXml();

         $this->_sendUploadResponse($fileName, $content);
     }



    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
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
}