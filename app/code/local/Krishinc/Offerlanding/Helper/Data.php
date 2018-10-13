<?php

class Krishinc_Offerlanding_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getOfferlandingPostUrl()
	{		 
        return $this->_getUrl('offerlanding/index/createpost', array('_secure'=>true));
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