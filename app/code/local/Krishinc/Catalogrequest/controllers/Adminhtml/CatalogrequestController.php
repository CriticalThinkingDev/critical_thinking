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

/* Copyright 2013 FreeportWeb, Inc. 
   This code was taken from the ChannelBrain Catalog request module for MOM. 
   This code cannot be used wihtout permission.
*/
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('catalogrequest/catalogrequest')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('catalogrequest_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalogrequest/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_edit'))
				->_addLeft($this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrequest')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('catalogrequest/catalogrequest');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalogrequest')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrequest')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
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
/* End ChannelBrain Catalog Request code */

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
  
    public function massExportStatusAction()
    {
        $catalogrequestIds = $this->getRequest()->getParam('catalogrequest');
        if(!is_array($catalogrequestIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($catalogrequestIds as $catalogrequestId) {
                    $catalogrequest = Mage::getSingleton('catalogrequest/catalogrequest')
                        ->load($catalogrequestId)
                        ->setIsExport($this->getRequest()->getParam('is_export'))
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
		 $this->updateExport();
         $this->_sendUploadResponse($fileName, $content);
        
     }
     
	public function updateExport()
	{
	  	$collection = Mage::getModel('catalogrequest/catalogrequest')->getCollection()->addFieldToFilter('is_export','No');
		foreach ($collection as $catalogrequest) {
            $catalogrequest->setIsExport('Yes')
            			   ->setExportAt(now())
                		   ->save();
        }
	}
     public function exportXmlAction()
     {
         $fileName   = 'catalogrequest.xml';
         $content    = $this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_customgrid')
             ->getXml();

         $this->_sendUploadResponse($fileName, $content);
     }

//   public function exportCsv1Action()
//    {
//        $fileName   = 'catalogrequest.csv';
//        /*
//         * $content    = $this->getLayout()->createBlock('catalogrequest/adminhtml_catalogrequest_grid')
//            ->getCsv();
//         * 
//       */
///*
//This code is taken directly from the ChannelBrain Catalogrequest module for MOM.
//This code is copyright ChannelBrain and cannot be used without permission.
//Copyright 2013 FreeportWeb, Inc. 
//*/
//
//	$catalogrequests = Mage::getSingleton('catalogrequest/catalogrequest')->getCollection();
//	$catalogrequests->getSelect()->where('status = ?', 1);
//	//$catalogrequests->addFieldToFilter('status','1');
//	
////	foreach($catalogrequests as $catalogrequest){
////	    $catalogrequest->setStatus(2)->addCountry();
////	}
//	$catalogrequests->save();
//	$data = $catalogrequests->toArray();
//	$output = "";
//	
//	// foreach($data['items'] as $line){
//	//     $row = implode('","', $line);
//	//     $output .= '"' . $row . '"' . "\015\012";
//	// }
//
///*
//
//[catalogrequest_id] => 32
// [fname] => Merry
// [mi] => 
// [lname] => Pri
// [address1] => 6 Tideway Lane
// [address2] => 
// [city] => East Northport
// [state] => NY
// [zip] => 11731
// [country] => US
// [phone] => 631-555-5555
// [email] => mplockleave@comcast.net
// [time_added] => 2012-09-03 14:05:15
// [ip] => 64.223.167.179
// [hostname] => 
// [heardofus] => SL-GOOGLE
// [position] => T
// [grade] => K
// [specialty] => LA
// [status] => 2
// [updates] => F
// [full_country] => United States
//*/
//
//	foreach ($data['items'] as $key=>$value)
//	{
//
//		// reverse the logic for "noemail"
//		if ($value['updates'] == "Y")  // Reverse the logic.
//		{
//			$value['updates'] = "F";
//		}
//		if ($value['updates'] == "N")
//		{
//			$value['updates'] = "T";
//		}
//
//		$state = strtoupper ($value['state']);
//		$value['state'] = $state;
//		// custnum,alnum (n/a)
//		$output .= ',,"' . $value['lastname'];
//		$output .= '","' . $value['firstname'];
//		$output .= '","' . $value['schoolname'];
//		$output .= '","' . $value['mailing_address'];
//		$output .= '","' . $value['appt_unit']; 
//		$output .= '","' . $value['city'];
//		$output .= '","' . $value['state'];
//		$output .= '","' . $value['zipcode'];
//		$output .= '","","' . $value['phone'];
//		$output .= '","","' . $value['position'];		// $ctype['ctype1']
//		$output .= '","' . $value['grade'];			// $ctype['ctype2']
//		$output .= '","' . $value['specialty'];			// $ctype['ctype3']
//		$output .= '","","Y"';
//		$output .= ',"' . $value['carttype'];
//		$output .= '","' . $value['cardnumber'];
//		$output .= '","' . $value['expires'];
//		$output .= '","' . $value['hear_about'];		// $value['sourcekey']
//		$output .= '","' . "GCR";		// $value['ccatalog']
//		$output .= '","' . $value['sales_id'];
//		$output .= '","' . $value['oper_id'];
//		$output .= '","' . $value['referece'];
//		$output .= '","' . $value['shipvia'];
//		$output .= '","' . $value['fulfilled'];
//		$output .= '",' . $value['paid'];
//		$output .= '," ","' . $value['created_date'];
//		$output .= '",' . $value['ordr_num'];
//		$output .= ',,,,,,,,,,,"' . $value['slastname'];
//		$output .= '","' . $value['sfirstname'];
//		$output .= '","' . $value['scompany'];
//		$output .= '","' . $value['saddress1'];
//		$output .= '","' . $value['saddress2'];
//		$output .= '","' . $value['scity'];
//		$output .= '","' . $value['sstate'];
//		$output .= '","' . $value['szipcode'];
//		$output .= '","' . $value['holddate'];
//		$output .= '","' . $value['paymethod'];
//		$output .= '","' . $value['greeting1'];
//		$output .= '","' . $value['greeting2'];
//		$output .= '","' . $value['promocred'];
//		$output .= '","' . $value['useprices'];
//		$output .= '",,,,,,,,,,,"' . $value['useshipamt'];
//		$output .= '","' . $value['shipping'];
//		$output .= '","' . $value['email'];
//		$output .= '","' . $value['country'];
//		$output .= '"';
//		$output .= ',"' . $value['scountry'];
//		$output .= '","' . $value['phone2'];
//		$output .= '","' . $value['sphone'];
//		$output .= '","' . $value['sphone2'];
//		$output .= '","' . $value['semail'];
//		$output .= '","' . $value['ordertype'];
//		$output .= '","' . $value['inpart'];
//		$output .= '","' . $value['title'];
//		$output .= '","' . $value['salu'];
//		$output .= '","' . $value['scountry'];
//		$output .= '","' . $value['ext'];
//		$output .= '","' . $value['ext2'];
//		$output .= '","' . $value['stitle2'];
//		$output .= '","' . $value['ssalu'];
//		$output .= '","' . $value['sshono'];
//		$output .= '","' . $value['sext'];
//		$output .= '","' . $value['sext2'];
//		$output .= '","' . $value['ship_when'];
//		$output .= '","' . $value['greeting3'];
//		$output .= '","' . $value['greeting4'];
//		$output .= '","' . $value['greeting5'];
//		$output .= '","' . $value['greeting6'];
//		$output .= '","' . $value['password'];
//		$output .= '","' . $value['custom01'];
//		$output .= '","' . $value['custom02'];
//		$output .= '","' . $value['custom03'];
//		$output .= '","' . $value['custom04'];
//		$output .= '","' . $value['custom05'];
//		$output .= '","' . $value['rcode'];
//		$output .= '","' . $value['approval'];
//		$output .= '","' . $value['avs'];
//		$output .= '","' . $value['anttrans_id'];
//		$output .= '","' . $value['auth_amt'];
//		$output .= '","' . $value['auth_time'];
//		$output .= '","' . $value['internetid'];
//		$output .= '","' . $value['ordnote1'];
//		$output .= '","' . $value['ordnote2'];
//		$output .= '","' . $value['ordnote3'];
//		$output .= '","' . $value['ordnote4'];
//		$output .= '","' . $value['ordnote5'];
//		$output .= '","' . $value['fulfill1'];
//		$output .= '","' . $value['fulfill2'];
//		$output .= '","' . $value['fulfill3'];
//		$output .= '","' . $value['fulfill4'];
//		$output .= '","' . $value['fullfill5'];
//		$output .= '","' . $value['internet'];
//		$output .= '","' . $value['priority'];
//		$output .= '","' . $value['nomail'];
//		$output .= '","' . $value['norent'];
//		$output .= '","' . $value['updates'];		// $value['noemail']
//		$output .= '","' . $value['ponumber'];
//		$output .= '","' . $value['address3'];
//		$output .= '","' . $value['saddress3'];
//		$output .= '","' . $value['cc_other'];
//		$output .= '","' . $value['promo_code'];
//		$output .= '","' . $value['best_promo'];
//		$output .= '","' . $value['ordermemo'];
//		$output .= '","' . $value['ordermemo2'];
//		$output .= '","' . $value['ordermemo3'];
//		$output .= '","' . $value['multiship'];
//		$output .= '","' . $value['r_code01'];
//		$output .= '","' . $value['r_code02'];
//		$output .= '","' . $value['r_code03'];
//		$output .= '","' . $value['r_code04'];
//		$output .= '","' . $value['r_code05'];
//		$output .= '","' . $value['altit_id01'];
//		$output .= '","' . $value['altit_id02'];
//		$output .= '","' . $value['altit_id03'];
//		$output .= '","' . $value['altit_id04'];
//		$output .= '","' . $value['altit_id05'];
//		$output .= '","' . $value['cardholder'];
//		$output .= '","' . $value['routingnum'];
//		$output .= '","' . $value['accountnum'];
//		$output .= '","' . $value['accountype'];
//		$output .= '","' . $value['bankname'];
//		$output .= '","' . $value['issue_num'];
//		$output .= '"' . "\015\012";
//	  }
//
//	$this->_sendUploadResponse($fileName, $output);
//    }

 /* end ChannelBrain_Catalogrequest */

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