<?php

class Krishinc_Hardcoder_Adminhtml_HardcoderController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('hardcoder/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function associatedproductsAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('customer.grid')
			->setCustomers($this->getRequest()->getPost('customers', null));

		$this->renderLayout();
	}
	public function associatedproductsgridAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('customer.grid')
			->setCustomers($this->getRequest()->getPost('customers', null));


		$this->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('hardcoder/hardcoder')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('hardcoder_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('hardcoder/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('hardcoder/adminhtml_hardcoder_edit'))
				->_addLeft($this->getLayout()->createBlock('hardcoder/adminhtml_hardcoder_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hardcoder')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {

			$i_d = $this->getRequest()->getParam('id');
			$data['p_id'] =  implode(",",$data['p_id']);

          if(!$i_d){
			  if(!$data['s_id'] && !$data['g_id'] && !$data['p_id']){
				  Mage::getSingleton('adminhtml/session')->addError('Please select Subject, Grade or Product Type Combination.');
				  Mage::getSingleton('adminhtml/session')->setFormData($data);
				  $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				  return;
			  }
		  }

			$hardCollection = Mage::getModel('hardcoder/hardcoder')->getCollection()
				->addFieldToFilter('s_id', $data['s_id'])
				->addFieldToFilter('p_id', $data['p_id'])
				->addFieldToFilter('g_id', $data['g_id']);
			$item = $hardCollection->getFirstItem();

			if(!$i_d){
				if($hardCollection->count()){
					Mage::getSingleton('adminhtml/session')->addError('Duplicate Entry. Selected Subject and Grade already created for Id '.$item->getId());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			if($data['s_id']){
				$sattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'subject');
				$data['subject'] = $sattribute->getSource()->getOptionText($data['s_id']);
			}

			if($data['g_id']){
				$gattribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'grade');
				$data['grade'] = $gattribute->getSource()->getOptionText($data['g_id']);
			}


			if($data['links']){
				$data['content'] =$data['links']['customers'];
				$content = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['content']);
				$data['count'] = count($content);
			}

           
			$model = Mage::getModel('hardcoder/hardcoder');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hardcoder')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hardcoder')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('hardcoder/hardcoder');
				 
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
        $hardcoderIds = $this->getRequest()->getParam('hardcoder');
        if(!is_array($hardcoderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($hardcoderIds as $hardcoderId) {
                    $hardcoder = Mage::getModel('hardcoder/hardcoder')->load($hardcoderId);
                    $hardcoder->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($hardcoderIds)
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
        $hardcoderIds = $this->getRequest()->getParam('hardcoder');

        if(!is_array($hardcoderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($hardcoderIds as $hardcoderId) {
                    $hardcoder = Mage::getSingleton('hardcoder/hardcoder')
                        ->load($hardcoderId)
                        ->setSstatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($hardcoderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'hardcoder.csv';
        $content    = $this->getLayout()->createBlock('hardcoder/adminhtml_hardcoder_grid')
            ->getCsv();


        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'hardcoder.xml';
        $content    = $this->getLayout()->createBlock('hardcoder/adminhtml_hardcoder_grid')
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
