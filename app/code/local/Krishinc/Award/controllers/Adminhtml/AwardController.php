<?php
    class Krishinc_Award_Adminhtml_AwardController extends Mage_Adminhtml_Controller_Action
    {
        protected function _initAction()
        {
            $this->loadLayout()
                ->_setActiveMenu('award/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Award Manager'), Mage::helper('adminhtml')->__('Award Manager'));
            return $this;
        }   
        
        public function indexAction() {
            $this->_initAction();       
            $this->_addContent($this->getLayout()->createBlock('award/adminhtml_award'));
            $this->renderLayout();
        }
        
        public function newAction() {
            $this->_forward('edit');
        }
        
        public function editAction()
        {
			
            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter('award')->getFirstItem();
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setPositionOrder('asc')
                    ->setAttributeFilter($attributeInfo->getAttributeId())
                    ->setStoreFilter(Mage::app()->getStore()->getId());
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('award/award')->load($id, 'award_option_id');

            if ($model->getAwardOptionId()) {
                   
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                }

                $path = $model->getImage();
			
				if(!empty($path)) {
					$model->setImage(Mage::getBaseUrl('media')."award/".$path);			
				}
                Mage::register('award_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('award/items');

                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Award Names Manager'), Mage::helper('adminhtml')->__('Award Names Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Award Name'), Mage::helper('adminhtml')->__('Award Name'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('award/adminhtml_award_edit'))
                        ->_addLeft($this->getLayout()->createBlock('award/adminhtml_award_edit_tabs'));

                $this->renderLayout();
            } else {
             
                $data = $collection->getItemByColumnValue("option_id", $id);
                
                if ($data) {
                    $data->getData();
                    $model->setName($data['value']);
                }
                $path = $model->getImage();
			
				if(!empty($path)) {
					$model->setImage(Mage::getBaseUrl('media')."award/".$path);			
				}

                Mage::register('award_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('award/items');

                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Award Name Manager'), Mage::helper('adminhtml')->__('Award Names Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Award Name'), Mage::helper('adminhtml')->__('Award Name'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('award/adminhtml_award_edit'))
                        ->_addLeft($this->getLayout()->createBlock('award/adminhtml_award_edit_tabs'));

                $this->renderLayout();
            }
        }
       

       


    public function saveAction() {
    	
        if ($data = $this->getRequest()->getPost()) {

            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter('award')->getFirstItem();
            
            /**START: Create option array to add Or Update attribute option**/
            $option = array();
            $option['attribute_id'] = $attributeInfo->getAttributeId();
            if($this->getRequest()->getParam('id'))
            {
            	$option['value'][$this->getRequest()->getParam('id')][Mage::app()->getStore()->getId()] = $data['name'];
	            $option['value'][$this->getRequest()->getParam('id')][1] = $data['name'];
            	
            }else {
	            $option['value'][0][Mage::app()->getStore()->getId()] = $data['name'];
	            $option['value'][0][1] = $data['name'];
            }
            /*******END:  Create option array to add Or Update attribute option**/
            
            $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setPositionOrder('asc')
                    ->setAttributeFilter($attributeInfo->getAttributeId())
                    ->setStoreFilter(Mage::app()->getStore()->getId());
            $exist = $collection->getItemByColumnValue("option_id", $this->getRequest()->getParam('id'));
      		
            
            $awardData = Mage::getModel('award/award')->load($this->getRequest()->getParam('id'));
         
            if($data['image']['delete'] == '1')
            {
            	if($imageToDelete = $awardData->getData('image'))
            	{
            		 $filepath = Mage::getBaseDir('media')."\award\\".$imageToDelete;  
                     unlink($filepath);
                     $data['image'] = '';
            	}            	
            }  
           
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('image');
						
						// Any extention would work
		           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(true);
						
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir
						$path = Mage::getBaseDir('media') . DS . 'award' . DS ;
						$newfilename ='award_'.$_FILES['image']['name'] ;
						$result = $uploader->save($path, $newfilename);
						//this way the name is saved in DB
		  				$data['image'] = $result['file'];
					} catch (Exception $e) {
				        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			            Mage::getSingleton('adminhtml/session')->setFormData($data);
			            $this->_redirect('*/*/edit/id/', array('id' => $this->getRequest()->getParam('id')));
			            return;
			        }
		        
			        
				} else {
					if(($this->getRequest()->getParam('id')) && isset($data['image']['value'])) {
						$file ='';
						$file = $data['image']['value'];
						if($file){
							$file_name = explode('award/',$file);
							 
							if(sizeof($file_name>1)) {
								$data['image'] = $file_name[1];							
							}
						}
					
					}
				}
            
			 /**To add and update option value of attribute award**/
             $setup = new Mage_Eav_Model_Entity_Setup('core_setup'); 
             $setup->addAttributeOption($option); 
             /***END**/
             
            $model = Mage::getModel('award/award');
            if (!$exist) { //check if not exists  attribute option 
               $newOption = $newcollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
				                            ->setPositionOrder('asc')
				                            ->setAttributeFilter($attributeInfo->getAttributeId())
				                            ->setStoreFilter(Mage::app()->getStore()->getId())
				                            ->getItemByColumnValue("value", $data['name']);  
                                
                $model->setData($data)
                      ->setAwardOptionId($newOption['option_id'])
                      ->setAwardId($newOption['option_id']);
            } else { 
               $model = $model->load($this->getRequest()->getParam('id'));
          
               $model->setImage($data['image'])
               		 ->setDescription($data['description'])
                     ->setAwardOptionId($this->getRequest()->getParam('id'))
                     ->setName($data['name'])
                     ->setAwardUrl($data['award_url'])
                     ->setAwardId($this->getRequest()->getParam('id'));  
            }

            try {
          
                $model->setAwarddate($data['awarddate']);
                $model->setIsCompanyaward($data['is_companyaward']);
                $model->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('award')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('award_option_id' => $model->getAwardOptionId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) { 
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('award_option_id' => $this->getRequest()->getParam('award_option_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('award')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
        
    }
       
     public function deleteAction()
     {  
          if ($this->getRequest()->getParam('id') > 0) {
                try {
                	
                	/**START: Create option array to Delete attribute option**/
                	
		            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		                            ->setCodeFilter('award')->getFirstItem();   
		            
		            $options = array();
		            $options['attribute_id'] = $attributeInfo->getAttributeId();
		            $allOptions = $attributeInfo->getSource()->getAllOptions();
		            $allValues = array_values($allOptions);
		            foreach ($allValues as $opt)
		            {
		         		$options['value'][$opt['value']] [Mage::app()->getStore()->getId()] = $opt['label'];
			            $options['value'][$opt['value']][1] = $opt['label']; 
		            }
		            
		            if($this->getRequest()->getParam('id'))
		            {
			            $options['delete'][$this->getRequest()->getParam('id')] [Mage::app()->getStore()->getId()] = 1;
			            $options['delete'][$this->getRequest()->getParam('id')][1] = 1; 
		            } 
		             
		             $setup = new Mage_Eav_Model_Entity_Setup('core_setup'); 
		             $setup->addAttributeOption($options);  
		             /***END***/
		             $model = Mage::getModel('award/award')->load($this->getRequest()->getParam('id'), 'award_option_id');
                    
                    $image = $model->getImage();
                    $filepath = Mage::getBaseDir('media')."\award\\".$image;  
                    unlink($filepath);
                    if ($model->getAwardOptionId()) {
                        $model->delete();
                    }  
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Item could not be deleted'));
                    $this->_redirect('*/*/');
                }
         }
        $this->_redirect('*/*/');
        }
        public function massDeleteAction() {
            $awardIds = $this->getRequest()->getParam('award');
            
            if (!is_array($awardIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            } else {
                try {
                	
                		/**START: Create option array to add Or Update attribute option**/
                        	
			            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			                            ->setCodeFilter('award')->getFirstItem();   
			            
			            $options = array();
			            $options['attribute_id'] = $attributeInfo->getAttributeId();
			            $allOptions = $attributeInfo->getSource()->getAllOptions();
			            $allValues = array_values($allOptions);
			            foreach ($allValues as $opt) 
			            {
			         		$options['value'][$opt['value']] [Mage::app()->getStore()->getId()] = $opt['label'];
				            $options['value'][$opt['value']][1] = $opt['label']; 
			            }
			           
                	
                    foreach ($awardIds as $awardId) {
                    	
            	    
			            $options['delete'][$awardId] [Mage::app()->getStore()->getId()] = 1;
			            $options['delete'][$awardId][1] = 1;  
			            
                        $model = Mage::getModel('award/award')->load($awardId, 'award_option_id');
                        $image = $model->getImage();
                        $filepath = Mage::getBaseDir('media')."\award\\".$image; 
                        unlink($filepath);
                        if ($model->getAwardOptionId()) {
                            $model->delete();
                        }
                    }
                   /**To add and update option value of attribute award**/
		             $setup = new Mage_Eav_Model_Entity_Setup('core_setup'); 
		             $setup->addAttributeOption($options);  
			       /***END**/
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__(
                                    'Total of %d record(s) were successfully deleted', count($awardIds)
                            )
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index');
        }
         public function exportCsvAction() {
            $fileName = 'award.csv';
            $content = $this->getLayout()->createBlock('award/adminhtml_award_grid')
                    ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
        }

        public function exportXmlAction() {
            $fileName = 'award.xml';
            $content = $this->getLayout()->createBlock('award/adminhtml_award_grid')
                    ->getXml();

            $this->_sendUploadResponse($fileName, $content);
        }

        protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK', '');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
        }
        /**
         * Product grid for AJAX request.
         * Sort and filter result for example.
         */
        public function gridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                   $this->getLayout()->createBlock('importedit/adminhtml_award_grid')->toHtml()
            );
        }
       
    }