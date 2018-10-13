<?php

class Krishinc_Fieldtester_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getFieldtesterPostUrl()
	{		 
        return $this->_getUrl('fieldtester/index/createpost', array('_secure'=>true));	
	}

	public function getAllBestDescribes()
	{
		return array(
			'Parent'	=>'Parent',
			'Home Educator' => 'Home Educator',
			'Classroom Educator' => 'Classroom Educator'
		);
	}	
	 
}