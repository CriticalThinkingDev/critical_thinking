<?php

class Krishinc_Softwaredemos_Adminhtml_SoftwaredemosController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initAction() {
	 
		$this->loadLayout()
			->_setActiveMenu('softwaredemos/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Softwaredemos Manager'), Mage::helper('adminhtml')->__('Softwaredemos Manager')); 
		return $this;
	}   

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	
	public function newAction() {
		$this->_forward('edit');
	}

    /**
     * View form action
     */
    public function editAction()
    { 
        $this->_registryObject();
 		$id     = $this->getRequest()->getParam('id');
		
		$model  = Mage::getModel('softwaredemos/softwaredemos')->load($id); 

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
        	$path = $model->getImage();
			
			if(!empty($path)) {
				$model->setImage(Mage::getBaseUrl('media')."softwaredemos/".$path);			
			}

			Mage::register('softwaredemos_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('softwaredemos/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Software Demos Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Softwaredemos'), Mage::helper('adminhtml')->__('Item Softwaredemos'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

//			$this->_addContent($this->getLayout()->createBlock('softwaredemos/adminhtml_form_edit'))
//				->_addLeft($this->getLayout()->createBlock('softwaredemos/adminhtml_form_edit_tabs'));
 
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softwaredemos')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function gridAction()
    { 
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('softwaredemos/adminhtml_form_edit_tab_product')
             	 ->setProductsRelated($this->getRequest()->getPost('softwaredemos_products', null))  
                 ->toHtml()
        );
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function saveAction()
    {  
        if ($data = $this->getRequest()->getPost()) {
		
			$name = $data['softname'];
			$description = $data['description'];
			$products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('products'));
			$subjectid= $data['subject_id']; 
		    $status =$data['status_form'];
		    /* Changes by pankil - to remove the conflict of product grid search status and software demo form status,
		     * 			   by changing name from status to status_form in form, and unset before setData.
		    */
		    unset($data['status_form']);
		    $grades = implode(',',$data['grades']);
		    $data['grades'] = $grades;
		    
			//upload Icon:
			if(isset($_FILES['icon_img']['name']) && $_FILES['icon_img']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('icon_img');
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
				   // create folder
					 if(!file_exists("./media/softwaredemos"))     mkdir("./media/softwaredemos",0777); 
					$path = Mage::getBaseDir('media') . DS .'softwaredemos' . DS ;
					$uploader->save($path, $_FILES['icon_img']['name'] );
				} catch (Exception $e) {       }
	           //this way the name is saved in DB
	  			$data['icon_img'] = $_FILES['icon_img']['name'];
				
				$img_icon = "softwaredemos/".$data['icon_img'];	
			}
			else
			{ 
				if(($this->getRequest()->getParam('id')) && isset($data['icon_img']['value'])) {
						$file ='';
						$img_icon = $data['icon_img']['value']; 
					}
			}
			 
			//upload Thumbline:
			if(isset($_FILES['thumbline_img']['name']) && $_FILES['thumbline_img']['name'] != '') {
				try {	
				
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('thumbline_img');
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
				   // create folder
					 if(!file_exists("./media/softwaredemos"))     mkdir("./media/softwaredemos",0777); 
					$path = Mage::getBaseDir('media') . DS .'softwaredemos' . DS ;
					$uploader->save($path, $_FILES['thumbline_img']['name'] );
				} catch (Exception $e) {       }
	           //this way the name is saved in DB
	  			$data['thumbline_img'] = $_FILES['thumbline_img']['name'];
				$img_thumbline = "softwaredemos/".$data['thumbline_img'];	
			}else
			{
				if(($this->getRequest()->getParam('id')) && isset($data['thumbline_img']['value'])) {
						$img_thumbline = $data['thumbline_img']['value'];
					}
			}
	
			//upload Base Image:
			if(isset($_FILES['large_img']['name']) && $_FILES['large_img']['name'] != '') {
				try {
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('large_img');
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
				   // create folder
					 if(!file_exists("./media/softwaredemos"))     mkdir("./media/softwaredemos",0777); 
					$path = Mage::getBaseDir('media') . DS .'softwaredemos' . DS ;
					$uploader->save($path, $_FILES['large_img']['name'] );
				} catch (Exception $e) {       }
	           //this way the name is saved in DB
	  			$data['large_img'] = $_FILES['large_img']['name'];
				 $img_baseimage = "softwaredemos/".$data['large_img'];	
			}else
			{
				if(($this->getRequest()->getParam('id')) && isset($data['large_img']['value'])) {
						$img_baseimage = $data['large_img']['value'];
					}	
			} 
	  
		 	$model = Mage::getModel('softwaredemos/softwaredemos');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'))
				->setSoftName($name)
				->setSubjectId($subjectid)
				->setIconImg($img_icon)
				->setThumblineImg($img_thumbline)
				->setLargeImg($img_baseimage)	
				->setDescription($description)
				->setStatus($status);
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				$model->save();
				
					
				if($model->getId()) {
					$productModel = Mage::getModel('softwaredemos/softwaredemos_product');
					$catProductModel = Mage::getModel('catalog/product');
					$productsToDelete = $productModel->getCollection()->addFieldToFilter('softwaredemos_id', $model->getId());  
					if($productsToDelete->count() > 0)
					{ 
						foreach ($productsToDelete as $productToDel)
						{
							$catProductModel->load($productToDel->getProductId()); 
							$catProductModel->setIsSoftwareDemos(0)
										->save(); 
							$productToDel->delete();  
							 
						}
					}   
				
					if(!empty($products)) {
						
						$data = array();
						foreach ($products as $key => $val)
						{
							$data['softwaredemos_id'] = $model->getId();
							$data['product_id'] = $key;
							$data['position'] = $val['position'];
							$productModel->setData($data)->save();  	
							$catProductModel->load($key);
							$catProductModel->setIsSoftwareDemos(1)
											->save();
						}    
					} 
				} 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('softwaredemos')->__('Software Demos Product was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softwaredemos')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
        
    }

    /*
     * DELETE Record
     */
    public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('softwaredemos/softwaredemos');
				 
				
					/***START:: DELETE Records from softwaredemos_product table***/
					$productModel = Mage::getModel('softwaredemos/softwaredemos_product');
					$productsToDelete = $productModel->getCollection()->addFieldToFilter('softwaredemos_id', $this->getRequest()->getParam('id'));  
					$catProductModel = Mage::getModel('catalog/product');
					  
					if($productsToDelete->count() > 0)
					{  $prod = '';
						foreach ($productsToDelete as $productToDel)
						{
							$prod = $productToDel->getData();
							$catProductModel->load($prod['product_id']); 
							$catProductModel->setIsSoftwareDemos(0) 
										->save(); 
							$productToDel->delete();   
						}
					} 
					/**END**/  
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
        $softwaredemosIds = $this->getRequest()->getParam('softwaredemos');
        if(!is_array($softwaredemosIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
            	$productModel = Mage::getModel('softwaredemos/softwaredemos_product');
                foreach ($softwaredemosIds as $softwaredemosId) {
                    $softwaredemos = Mage::getModel('softwaredemos/softwaredemos')->load($softwaredemosId);
                  
                    /***START:: DELETE Records from softwaredemos_product table***/
                    $productsToDelete = $productModel->getCollection()->addFieldToFilter('softwaredemos_id', $softwaredemosId);  
                    $catProductModel = Mage::getModel('catalog/product');
                   
					if($productsToDelete->count() > 0)
					{  
						foreach ($productsToDelete as $productToDel)
						{
							$prod = $productToDel->getData();
							$catProductModel->load($prod['product_id']); 
							$catProductModel->setIsSoftwareDemos(0)
										->save();   
							$productToDel->delete();    
						}
					}   
					/**END**/
					  $softwaredemos->delete(); 
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($softwaredemosIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    
    /**
	 * Update status
	 */
	
    public function massStatusAction()
    {
        $softwaredemosIds = $this->getRequest()->getParam('softwaredemos');
        if(!is_array($softwaredemosIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($softwaredemosIds as $softwaredemosId) {
                    $softwaredemos = Mage::getSingleton('softwaredemos/softwaredemos')
                        ->load($softwaredemosId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($softwaredemosIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
   
    /**
	 * Export CSV
	 */
	
    public function exportCsvAction()
    {
        $fileName   = 'softwaredemos.csv';
        $content    = $this->getLayout()->createBlock('softwaredemos/adminhtml_softwaredemos_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

     
    /**
	 * Export XML
	 */
	
    public function exportXmlAction()
    {
        $fileName   = 'softwaredemos.xml';
        $content    = $this->getLayout()->createBlock('softwaredemos/adminhtml_softwaredemos_grid')
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
    
    
    /**
     * registry form object
     */
    protected function _registryObject()
    { 
    	if($id  = $this->getRequest()->getParam('id')) {
			$model  = Mage::getModel('softwaredemos/softwaredemos')->load($id); 
	    	Mage::register('softwaredemos', $model);  
    	}
    }

}