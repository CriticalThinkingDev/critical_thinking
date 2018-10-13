<?php
class Krishinc_Standards_Block_Ajax extends Mage_Core_Block_Template
{
	
	public  function getAllStandardProductData()
	{ 
		$postData = $this->getRequest()->getPost();
		   
		$id = (isset($postData['product_id'])?$postData['product_id']:'');
		$state = $postData['state'];
		
		$model = Mage::getModel('standards/standards')->getCollection()
		   ->addFieldToSelect('*')
		   ->addFieldToFilter('product_id',$id)
		   ->addFieldToFilter('state',$state);

		   return $standard_data = $model->getData(); 
	}
	public function getGradesubject($id)
	{
		$model = Mage::getModel('catalog/product');
		$_product = $model->load($id);
		return $gradeSubject = $model->getProductSubject($_product->getSubject()) . '<br/>Grades: '. $model->getProductGrade($_product->getGrade());
	}
}