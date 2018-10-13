<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.43
if ($installer->getConnection()->tableColumnExists($this->getTable('downloadplus_download_detail'), 'store_id')) {
	$installer->run("
		ALTER TABLE {$this->getTable('downloadplus_download_detail')}
			MODIFY `store_id` smallint(6) NOT NULL DEFAULT '0'
		;
		");
}

if (!$installer->getConnection()->tableColumnExists($this->getTable('downloadplus_download_detail'), 'hidden')) {
	$installer->run("
			ALTER TABLE {$this->getTable('downloadplus_download_detail')}
				ADD `hidden` tinyint(4) NOT NULL DEFAULT '0' AFTER `active` 
			;
			");
}

// Sanitize the Download Details table - double entries may have occured
if ($items = $installer->getConnection()->fetchAll("SELECT COUNT(file) as count, file FROM {$this->getTable('downloadplus_download_detail')} GROUP BY file")) {
	foreach ($items as $item) {
		if ($item['count']>1) {
			if ($duplicates = $installer->getConnection()->fetchAll("SELECT detail_id FROM {$this->getTable('downloadplus_download_detail')} WHERE file='".$item['file']."' ORDER BY detail_id ASC")) {
				$last = array_pop($duplicates);
				foreach ($duplicates as $duplicate) {
					if ($duplicate['detail_id']!=$last['detail_id']) {
						$installer->run("DELETE FROM {$this->getTable('downloadplus_download_detail')} WHERE detail_id='".$duplicate['detail_id']."';");
					}
				}
			}
		}
	}
}


$installer->endSetup();
?>