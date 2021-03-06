<?php
/**
 * GoogleTagManager plugin for Magento 
 *
 * @package     Yireo_GoogleTagManager
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_GoogleTagManager_Block_Quote extends Yireo_GoogleTagManager_Block_Default
{
    public function getItemsAsJson()
    {   
        $data = array();
        foreach($this->getQuote()->getAllItems() as $item) {
            $data[] = array(
                'sku' => $item->getProduct()->getSku(),
                'name' => htmlentities($item->getProduct()->getName().'"dtest"'),
                'price' => $item->getProduct()->getPrice(),
                'quantity' => $item->getQty(),
            );
        }
      
        return json_encode($data);
    }
}
