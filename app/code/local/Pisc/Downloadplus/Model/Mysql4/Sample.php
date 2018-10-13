<?php
/**
 * Downloadplus Sample Resource Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusMagazine
 * @copyright  Copyright (c) 2015 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Mysql4_Sample extends Mage_Downloadable_Model_Mysql4_Sample
{
	
	protected function _construct()
	{
		$this->_init('downloadplus/sample', 'sample_id');
	}
	
	/*
	 * Returns the Sample Title
	 */
	public function getSampleTitle($sampleObject)
	{
		$result = null;
	
		$sql = $this->_getReadAdapter()->select()
				->from($this->getTable('downloadable/sample_title'))
				->where('sample_id = ?', $sampleObject->getSampleId())
				->where('store_id = ?', $sampleObject->getStoreId());
	
		$result = $this->_getReadAdapter()->fetchOne($sql);
	
		return $result;
	}
	
}