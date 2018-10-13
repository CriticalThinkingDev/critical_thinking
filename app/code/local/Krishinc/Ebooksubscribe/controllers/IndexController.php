<?php
class Krishinc_Ebooksubscribe_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

    public function createpostAction()
    {
        if($data = $this->getRequest()->getPost())
        {
           // p($data);
            $email              = trim($this->getRequest()->getPost('email'));
            $data['email'] = $email;
 $sendtoListrak = false;

            if($data['iseuropian']){
                if($data['join_email_list']){
                    $sendtoListrak = true;
                }else{
                    $sendtoListrak = false;
                }

            }else{
                $sendtoListrak = true;
            }

            try {



                    $model = Mage::getModel('offerlanding/offerlanding');
//	    			if(!$model->isEmailExists($data['email']))
//	    			{
                    $model->setData($data)
                        ->setStatus('2');

                    if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                        $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                    } else {
                        $model->setUpdateTime(now());
                    }


                    $model->save();

                    /**START: To subscribe for listrack  **/
                    //Mage::getModel('offerlanding/offerlanding')->subscribeToListrack($model);
if($sendtoListrak){
                    Mage::dispatchEvent('ebook_index_createpost_after', array('requestdata' => $model));
}
                    /****END****/

                    $this->_getSession()->setIsRedirectToDownload($model->getId());
                    $this->_getSession()->unsOlFormData();
                    $this->_getSession()->addSuccess('Thank you for signing up.');

                    $url = Mage::getUrl('').'free-ebook-download';
                    $this->_redirectUrl($url);
                    return;
//						  	} else {
//			    				$this->_getSession()->addError(Mage::helper('captcha')->__('Email Address Already Exists!'));
//			    				$this->_getSession()->setOlFormData($this->getRequest()->getParams());
//			    				$this->_redirect('*/*');
//							  	return;
//			    			}

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setOlFormData($this->getRequest()->getParams())
                    ->addException($e, $e->getMessage());
                $url = Mage::getUrl('').'ebooksubscribe';
                $this->_redirectUrl($url);
                return;
            } catch (Exception $e) {
                $this->_getSession()->setOlFormData($this->getRequest()->getParams())
                    ->addException($e, $this->__('Cannot save your Offer.'));
                $url = Mage::getUrl('').'ebooksubscribe';
                $this->_redirectUrl($url);
                return;
            }

        } else {
            $this->_redirect('*/*');
        }
    }

    public function _getSession()
    {
        return  Mage::getSingleton('core/session');
    }

}
