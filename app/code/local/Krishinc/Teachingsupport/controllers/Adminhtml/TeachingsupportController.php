<?php

class Krishinc_Teachingsupport_Adminhtml_TeachingsupportController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('teachingsupport/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Teaching Support Manager'), Mage::helper('adminhtml')->__('Teaching Support Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('teachingsupport/teachingsupport')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			
			if (!empty($data)) {
				$model->setData($data);
			}
			 
			if(strstr($model->getData('sku'),','))
			{
				$skus = explode(',',$model->getSku());
				$model->setSku($skus);
			}
			$path = $model->getPdf();
			if(!empty($path)) {
				$model->setPdf(Mage::getBaseUrl('media')."tspdfs/".$path);			
			}
			$path1 = $model->getPdf1();
			if(!empty($path1)) {
				$model->setPdf1(Mage::getBaseUrl('media')."tspdfs/".$path1);	  		 
			}

			Mage::register('teachingsupport_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('teachingsupport/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Teaching Support Manager'), Mage::helper('adminhtml')->__('Teaching Support Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Teaching Support'), Mage::helper('adminhtml')->__('Teaching Support'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('teachingsupport/adminhtml_teachingsupport_edit'))
				->_addLeft($this->getLayout()->createBlock('teachingsupport/adminhtml_teachingsupport_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('teachingsupport')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			 
		 	$skus = $this->getRequest()->getParam('sku');
            if(is_array($skus)) {
                $data['sku'] = implode(',',$skus);
            }
			if(isset($_FILES['pdf']['name']) && $_FILES['pdf']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('pdf');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('pdf'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS .'tspdfs'. DS ;
					$result = $uploader->save($path, $_FILES['pdf']['name'] );
					//this way the name is saved in DB
	  				$data['pdf'] =  "tspdfs/".$result['file']; 
				 
				} catch (Exception $e) {
	      			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		            Mage::getSingleton('adminhtml/session')->setFormData($data);
		            $this->_redirect('*/*/edit/id/', array('id' => $this->getRequest()->getParam('id')));
		            return;
		        }
	        
		        
			}
			if(isset($_FILES['pdf1']['name']) && $_FILES['pdf1']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('pdf1');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('pdf'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS .'tspdfs'. DS ;
					$result1 = $uploader->save($path, $_FILES['pdf1']['name'] );
					
			        //this way the name is saved in DB
		  			$data['pdf1'] =  "tspdfs/".$result1['file']; 
				} catch (Exception $e) {
		      			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			            Mage::getSingleton('adminhtml/session')->setFormData($data);
			            $this->_redirect('*/*/edit/id/', array('id' => $this->getRequest()->getParam('id')));
			            return;
		        }
	        
			}
	  			
	  			
			$model = Mage::getModel('teachingsupport/teachingsupport');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				 
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('teachingsupport')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('teachingsupport')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('teachingsupport/teachingsupport')->load($this->getRequest()->getParam('id'));
				if($pdf1 = $model->getPdf()){
					$filepath1 = Mage::getBaseDir('media').DS.$pdf1;  
                    unlink($filepath1);
				}
				if($pdf2 = $model->getPdf1()){
					$filepath2 = Mage::getBaseDir('media').DS.$pdf2; 
                    unlink($filepath2);
				}
				 $model->delete();
					 
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
        $teachingsupportIds = $this->getRequest()->getParam('teachingsupport');
        if(!is_array($teachingsupportIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($teachingsupportIds as $teachingsupportId) {
                    $teachingsupport = Mage::getModel('teachingsupport/teachingsupport')->load($teachingsupportId);
                    if($pdf1 = $teachingsupport->getPdf()){
						$filepath1 = Mage::getBaseDir('media').DS.$pdf1; 
                    	unlink($filepath1);
					}
					if($pdf2 = $teachingsupport->getPdf1()){
						$filepath = Mage::getBaseDir('media').DS.$pdf2; 
	                    unlink($filepath);
					} 
                    $teachingsupport->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($teachingsupportIds)
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
        $teachingsupportIds = $this->getRequest()->getParam('teachingsupport');
        if(!is_array($teachingsupportIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($teachingsupportIds as $teachingsupportId) {
                    $teachingsupport = Mage::getSingleton('teachingsupport/teachingsupport')
                        ->load($teachingsupportId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($teachingsupportIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'teachingsupport.csv';
        $content    = $this->getLayout()->createBlock('teachingsupport/adminhtml_teachingsupport_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'teachingsupport.xml';
        $content    = $this->getLayout()->createBlock('teachingsupport/adminhtml_teachingsupport_grid')
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