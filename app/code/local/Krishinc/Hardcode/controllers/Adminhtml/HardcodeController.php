<?php

class Krishinc_Hardcode_Adminhtml_HardcodeController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('hardcode/items')
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
		$model  = Mage::getModel('hardcode/hardcode')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('hardcode_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('hardcode/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('hardcode/adminhtml_hardcode_edit'))
				->_addLeft($this->getLayout()->createBlock('hardcode/adminhtml_hardcode_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hardcode')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {

			$i_d = $this->getRequest()->getParam('id');
          if(!$i_d){
			  if(!$data['s_id'] && !$data['g_id']){
				  Mage::getSingleton('adminhtml/session')->addError('Please select Subject or grade Combination.');
				  Mage::getSingleton('adminhtml/session')->setFormData($data);
				  $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				  return;
			  }
		  }

			$hardCollection = Mage::getModel('hardcode/hardcode')->getCollection()
				->addFieldToFilter('s_id', $data['s_id'])
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

           
			$model = Mage::getModel('hardcode/hardcode');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hardcode')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hardcode')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('hardcode/hardcode');
				 
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
        $hardcodeIds = $this->getRequest()->getParam('hardcode');
        if(!is_array($hardcodeIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($hardcodeIds as $hardcodeId) {
                    $hardcode = Mage::getModel('hardcode/hardcode')->load($hardcodeId);
                    $hardcode->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($hardcodeIds)
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
        $hardcodeIds = $this->getRequest()->getParam('hardcode');

        if(!is_array($hardcodeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($hardcodeIds as $hardcodeId) {
                    $hardcode = Mage::getSingleton('hardcode/hardcode')
                        ->load($hardcodeId)
                        ->setSstatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($hardcodeIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'hardcode.csv';
        $content    = $this->getLayout()->createBlock('hardcode/adminhtml_hardcode_grid')
            ->getCsv();


        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'hardcode.xml';
        $content    = $this->getLayout()->createBlock('hardcode/adminhtml_hardcode_grid')
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