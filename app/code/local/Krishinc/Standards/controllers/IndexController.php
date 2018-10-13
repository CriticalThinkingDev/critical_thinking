<?php
/**
created by : varsha parmar
created on : 26 March 2013

================================================
Front End Author management controller
**/
class Krishinc_Standards_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
            $this->loadLayout();			
			$this->_initLayoutMessages('core/session'); 
			$this->renderLayout();
    }
	
	public function getstateAction()
    {	
           $postData = $this->getRequest()->getPost();
		   
		   $id = $postData['product_id'];
		   
		   $model = Mage::getModel('standards/standards')->getCollection()
		   ->addFieldToSelect('state')
		   ->addFieldToFilter('product_id',$id);
			
		   $model->getSelect()->group(array('state'));
		   $standard_data = $model->getData();
		   
		   $returnHtml = ''; 
		   if(count($standard_data)>0)
		   {
		   $returnHtml .= '<select id="state"  name="state" onChange="callDataAjax();">';
		   $returnHtml .= '<option>'.Mage::helper('standards')->__('Choose a state').'</option>';
			foreach($standard_data  as $data){
				$returnHtml .= '<option value="'.$data['state'].'">';
				$returnHtml .= $data['state'];
				$returnHtml .= '</option>';
			}
			$returnHtml .= '</select>';
		   }
		   else
		   {
			return "";
		   }
		   $this->getResponse()->setBody($returnHtml);
		   
    }
	public function getstandarddataAction()
    {		
	 $this->loadLayout()->renderLayout();
		
    }
	
}