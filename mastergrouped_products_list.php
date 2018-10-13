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
        ->addAttributeToSelect('priority')
        ->addAttributeToFilter('type_id', array('eq' => 'grouped'))
        ->addAttributeToFilter('attribute_set_id','12');
    
    $final_arr = $duplicate = array();
    echo "<pre>";
    //print_r($products->getData());exit;
    foreach ($products as $product) {
        if($product->getIsMasterGroupProduct()) {
            
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            // Do something with $associatedProducts
            echo "<br />";
            echo "Master Grouped Product -- " .$product->getId();
            echo "<br />";
            echo "Associated Product -> ";
            echo "<br />";
            foreach($associatedProducts as $ass_pro) {
                echo $ass_pro->getId()." -- ". $ass_pro->getSku();
                echo "<br />";
                if(in_array($ass_pro->getSku(),$final_arr)) {
                    $duplicate[] = $product->getId() ."----". $ass_pro->getSku();
                } else {
                    $final_arr[] = $ass_pro->getSku();
                }
                
            }
        }
    }
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "Duplicate Array";
    echo "<br />";
    print_r($duplicate);
    exit;
?>


