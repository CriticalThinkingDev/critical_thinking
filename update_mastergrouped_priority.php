<?php
    error_reporting(E_ALL | E_STRICT);
    define('MAGENTO_ROOT', getcwd());
    
    $mageFilename = MAGENTO_ROOT . '/app/Mage.php';
    require_once $mageFilename;
    
    Mage::setIsDeveloperMode(true);
    ini_set('display_errors', 1);
    umask(0);
    Mage::app();
    
    $products = Mage::getModel('catalog/product')
        ->getCollection()
        ->addAttributeToSelect('is_master_group_product')
        ->addAttributeToSelect('sku')
        ->addAttributeToSelect('priority')
        ->addAttributeToFilter('type_id', array('eq' => 'grouped'))
        ->addAttributeToFilter('attribute_set_id',array('eq' => 12));
    
    echo "<pre>";
//    print_r($products->getData());exit;
?> 

<?php 
    foreach ($products as $product) {
        
        if($product->getIsMasterGroupProduct()) {
            $final_priority = 0;
            //if($product->getId() == 1189) {
                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                // Do something with $associatedProducts
                echo "Master Grouped Product - " .$product->getName() ." SKU: ".$product->getSku()." ; Old Priority ". $product->getPriority();
                echo "<br />";
                echo "  Associated Product ";
                echo "<br />";
                foreach($associatedProducts as $ass_pro) {
                    $priority = ($ass_pro->getPriority() != "" && $ass_pro->getPriority() > 0) ? $ass_pro->getPriority() : 0;
                    echo "      ". "  SKU: " .$ass_pro->getSku()."  Proiority: <b>". $priority . "</b>";
                    echo "<br />";
                    $final_priority += $priority;
                }
                echo "Master Grouped Priority : ".$final_priority;
                echo "<br />";
                echo "<br />";
                echo "<br />";
                try {
                    
                    $catalog_product = Mage::getModel('catalog/product')->load($product->getEntityId());
                    $catalog_product->setData('priority', $final_priority)->getResource()->saveAttribute($catalog_product, 'priority');
                    
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            //}
        }
    }
    $process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_flat');
    $process->reindexAll();
    exit;
?>


