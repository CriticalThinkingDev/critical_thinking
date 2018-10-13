<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_GoogleTagManager_Block_Order extends Yireo_GoogleTagManager_Block_Default
{
    public function getItemsAsJson()
    {   
        $data = array();
        foreach($this->getOrder()->getAllVisibleItems() as $item) {
            $data[] = array(
                'sku' => $item->getProduct()->getSku(), 
                'name' => $item->getProduct()->getName(),
                'price' => (double)number_format($item->getBasePrice(),2,'.',''),
                'quantity' =>(int)$item->getQtyOrdered()
            );
        }        
        return json_encode($data);
    }
    
}
