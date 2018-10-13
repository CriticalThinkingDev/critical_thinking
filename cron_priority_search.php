<?php 
error_reporting(E_ALL | E_STRICT);
define('MAGENTO_ROOT', getcwd());
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
try {
	 
	$orderIds = Mage::getModel('overridepo/overridepo')->salesOrderUpdateSearchPriority();
} catch (Exception $e) {
    Mage::printException($e);
}
?>
Critical Thinking - Cron for Search priority execute successfully.