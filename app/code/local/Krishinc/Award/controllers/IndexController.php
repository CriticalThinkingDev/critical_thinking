<?php
class Krishinc_Award_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Awards & Honors - More Than 100 National Award Winners!');                                                 
        }
   
		$this->renderLayout();
    }

 public function callbackAction(){
        
       
        header("realtimeapi: {$_POST['realtimeapi_code']}");
	
	header("Content-Type: multipart/form-data");

        $entries = '';
	$entries = $_POST['entries'];
       $array = json_decode($entries);
        foreach ($array as $m){
   	  Mage::log($m->email, null,'woobox.log');
          $email = $m->email;
          if($email){
        	 Mage::helper('listrak')->subscribeToListrackWoobox($email);
     	  }


        }
       
       
    }

    public function callbackwooboxAction(){
     $email = 'wooboxparas@krishtechnolabs.com';
     if($email){
         Mage::helper('listrak')->subscribeToListrackWoobox($email);
     }

    }
}
