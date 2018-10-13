<?php
/**
 * Open Commerce LLC Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Commerce LLC Commercial Extension License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.tinybrick.com/license/commercial-extension
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tinybrick.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this package to newer
 * versions in the future. 
 *
 * @category   TinyBrick
 * @package    TinyBrick_GiftCard
 * @copyright  Copyright (c) 2010 TinyBrick Inc. LLC
 * @license    http://www.tinybrick.com/license/commercial-extension
 */
class TinyBrick_GiftCard_Adminhtml_GiftcardController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('giftcard/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Card Manager'), Mage::helper('adminhtml')->__('Gift Card Manager'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('giftcard/giftcard')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('giftcard_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('giftcard/entry');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Card Manager'), Mage::helper('adminhtml')->__('Gift Card Manager'));
//			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_edit'))
				->_addLeft($this->getLayout()->createBlock('giftcard/adminhtml_giftcard_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('giftcard entry does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_redirect('*/*/edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//get original balance
			$oldBal = Mage::getModel('giftcard/giftcard')->load($this->getRequest()->getParam('id'))->getBal();
	  			
			$model = Mage::getModel('giftcard/giftcard');
				
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));			

			if(!$model->getId()) {
				// new card being created
				$model->setNumber(Mage::helper('giftcard')->generateCardNumber());
				//print_r($data);
				//exit;
				$model->setOrderId(0);
				$model->setOrderAmount($data['bal']);
				$model->setShipped(0);
			} else {
				if($model->getBal() != $oldBal) {
					$diff = $model->getBal() - $oldBal;
					$trans = Mage::getModel('giftcard/payment');
					$trans
						->setId(null)
						->setGiftcardId($model->getId())
						->setQuoteId(0)
						->setOrderId(0)
						->setAmount($diff)
						->setCreatedAt(now())
						->setIsAdmin(1);
					$trans->save();
				}
                                // This will update the giftcard number to whatever you change it too. 
                                $gcnumber = $model->getgcnumber();  
                                $model->setNumber($gcnumber);

			}
			
			
			try {
				//Added the UpdatedAt and CreatedAt fields for this table
				if ($model->getCreatedAt() == NULL && $model->getUpdatedAt() == NULL) {
					$model->setCreatedAt(now())->setUpdatedAt(now());
				} else {
					$model->setUpdatedAt(now());
				}	
				
				if($model->save()) {
					if($model->getType() == 2 && $data['send_email']) {
						//Set email and msg if just now coming in through paramas in Admin Panel
						if($model->getGiftcardEmail() == ""){
							try{
								$model->setGiftcardEmail($this->getRequest()->getParam('to_email'));
							}catch(exception $e){}
						}
						if($model->getGiftcardMsg() == ""){
							try{
								$model->setGiftcardMsg($this->getRequest()->getParam('to_msg'));
							}catch(exception $e){}
						}
						//send virtual card email
						$sent = Mage::getModel('core/email_template')
							->sendTransactional(
								Mage::getStoreConfig('sales/giftcard/trans_id'),
								array('email' => Mage::getStoreConfig('sales/giftcard/send_email'), 'name' => Mage::getStoreConfig('sales/giftcard/send_email_name')), 
								$model->getGiftcardEmail(), 
								$model->getGiftcardEmail(), 
									array('number' => $model->getNumber(), 'amount'=>Mage::helper('core')->formatPrice($model->getBal()), 'msg' => $model->getGiftcardMsg(), 'sender_name' => 'Admin') //changes done by bijal
						);
						if($sent) {
							$model->setShipped(1);
						} else {
							$model->setShipped(0);
						}
						$model->save();
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftcard')->__('Gift Card Entry was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftcard')->__('Unable to find giftcard entry to save'));
        $this->_redirect('*/*/');
	}
 
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('giftcard/giftcard');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('giftcard entry was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $recipesIds = $this->getRequest()->getParam('giftcard');
        if(!is_array($recipesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select giftcard entry(s)'));
        } else {
            try {
                foreach ($recipesIds as $recipesId) {
                    $recipes = Mage::getModel('giftcard/giftcard')->load($recipesId);
                    $recipes->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($recipesIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

  
    public function exportCsvAction()
    {
        $fileName   = 'giftcard.csv';
        $content    = $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'giftcard.xml';
        $content    = $this->getLayout()->createBlock('giftcard/adminhtml_giftcard_grid')
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