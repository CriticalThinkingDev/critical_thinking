<?php
define('MAGENTO_ROOT', getcwd());
ini_set('memory_limit', '512M');
$time = microtime(true);
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;

Mage::setIsDeveloperMode(true);
umask(0);
Mage::app('default');
Mage::app()->setCurrentStore(Mage_Core_Model_App::DISTRO_STORE_ID);

$product_table = Mage::getSingleton('core/resource')->getTableName('catalog/product');
$attributeModel = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'isbn_10');
$attrTable = $attributeModel->getBackend()->getTable();

$isbn10Id = $attributeModel->getId();

$isbn13Id = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'isbn_13')->getId();
 
$isbn10wohId = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'isbn_10woh')->getId();
 
$isbn13wohId = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'isbn_13woh')->getId();

 
$collection = Mage::getModel('catalog/product')->getCollection();
$collection->getSelect()->joinLeft(array('isbn10' => $attrTable ), 'e.entity_id = isbn10.entity_id and isbn10.attribute_id = "'.$isbn10Id.'"', array('isbn10' => 'value'));
$collection->getSelect()->joinLeft(array('isbn13' => $attrTable ), 'e.entity_id = isbn13.entity_id and isbn13.attribute_id = "'.$isbn13Id.'"', array('isbn13' => 'value'));
$collection->getSelect()->joinLeft(array('isbn10woh' => $attrTable ), 'e.entity_id = isbn10woh.entity_id and isbn10woh.attribute_id = "'.$isbn10wohId.'"', array('isbn_10woh' => 'value'));
$collection->getSelect()->joinLeft(array('isbn13woh' => $attrTable ), 'e.entity_id = isbn13woh.entity_id and isbn13woh.attribute_id = "'.$isbn13wohId.'"', array('isbn_13woh' => 'value'));
$collection->getSelect()->where("(isbn10.value != '') AND (isbn10woh.value IS NULL or isbn10woh.value = '') OR (isbn13.value != '') AND (isbn13woh.value IS NULL or isbn13woh.value = '')");


$query = '';
$i = 0;
echo count($collection);
foreach($collection as $product) {
    $data = $product->getData(); 
    $product_id = $data['entity_id'];
    $isbn10 = $data['isbn10'];
    $isbn13 = $data['isbn13']; 
     
        try {
            if(trim($isbn10) != ''){
                $newisbn10 = str_replace('-','',trim($isbn10));
                $product->setData('isbn_10woh', $newisbn10);
                $product->getResource()->saveAttribute($product, 'isbn_10woh');
            }
            if(trim($isbn13) != '') {
                $newisbn13 = str_replace('-','',trim($isbn13));
                $product->setData('isbn_13woh', $newisbn13);    
                $product->getResource()->saveAttribute($product, 'isbn_13woh');
            }
            
            
            
        } catch(Exception $e) {
            Mage::log('IsbnUpdate:'.$e,'0777','IsbnUpdate.log');
        }
} 

if(count($collection) > 0) {
    Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product')->reindexEverything();
    Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_attribute')->reindexEverything();
    Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_flat')->reindexEverything();
    Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext')->reindexEverything();
} 

//@mail('bijal@krishinc.com','Cron run successfully','updateIsbn.php - '. date('Y-m-d'));

echo $timeelapsed = " Time Elapsed: ".(microtime(true) - $time)."s <br/>";
echo "Cron run Successfully.";

?>