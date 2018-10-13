<?php
$installer = $this;
$installer->startSetup();

// Introduced with 0.3.57
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('downloadplus_sample_extension')} (
	id int(11) NOT NULL AUTO_INCREMENT,
	sample_id int(11) NOT NULL,
	image_file varchar(255) DEFAULT NULL,
	attributes text,
	PRIMARY KEY (id),
	UNIQUE KEY sample_id (sample_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Data extension for downloadable samples';
");

$installer->endSetup();
?>