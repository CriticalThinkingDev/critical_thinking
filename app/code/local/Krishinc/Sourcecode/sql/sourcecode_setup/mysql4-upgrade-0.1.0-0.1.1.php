<?php
 
Mage::getSingleton('core/resource')->getConnection('core_read')->addColumn(Mage::getSingleton('core/resource')->getTableName('sales/order'), 'source_code', 'TEXT NULL');
 ?>