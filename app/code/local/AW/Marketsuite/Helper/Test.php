<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    1.2.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Marketsuite_Helper_Test extends Mage_Core_Helper_Abstract
{
    private static $scriptStartTime;
    private static $scriptMemoryBegin;

    public static function showTestInfo()
    {
        if (self::$scriptStartTime && self::$scriptMemoryBegin)
        {
            echo '<table border="1" aligh="center">';
            echo '<tr>';
            echo '<td>Time:</td>';
            echo '<td width="30px" style="text-align:center; color:red">'.(time() - self::$scriptStartTime).'</td>';
            echo '<td>Memory:</td>';
            echo '<td width="100px" style="text-align:center; color:red">'.round((memory_get_usage() - self::$scriptMemoryBegin)/(1024*1024), 2).'Mb</td>';
            echo '</tr>';
            echo '</table>';
        }
    }
    
    public static function initTest()
    {
        self::$scriptStartTime = time();
        self::$scriptMemoryBegin = memory_get_usage();
    }

    public function showTestSystem($params)
    {
        $customer_id = isset($params['cust'])?$params['cust']:1;
        $order_id = isset($params['ord'])?$params['ord']:1;
        $quote_id = isset($params['cart'])?$params['cart']:1;

        echo '<form action="submit">';
        echo 'Customer Id <input name="cust" value = "'.$params['cust'].'"/>';
        echo 'Order Id <input name="ord" value = "'.$params['ord'].'"/>';
        echo 'Cart Id <input name="cart" value = "'.$params['cart'].'"/>';
        echo '<input type="submit">';
        echo '</form>';

        

        $object = Mage::getModel('customer/customer')->load($customer_id);
        $object1 = Mage::getModel('sales/order')->load($order_id);
        $object2 = Mage::getModel('sales/quote')
                        ->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                        ->load($quote_id);

        echo "Testing ... </br>";

        echo "<table border='1'>";
        echo "<tr><td></td><td>";
        echo "customer ".$customer_id."</td><td>";
        echo "order ".$order_id."</td><td>";
        echo "cart ".$quote_id."</td><td>";
        echo "</td></tr>";
        $rules = Mage::getModel('marketsuite/filter')->getCollection();

        foreach ($rules as $rule)
        {
            echo "<tr><td>".$rule->getId()." ".$rule->getName()."</td>";
            try
            {
                $vivod = "";
                if (Mage::getModel('marketsuite/filter')->checkRule($object, $rule->getId()))
                    $vivod .= "<td>true</td>";
                else
                    $vivod .= "<td>false</td>";

                if (Mage::getModel('marketsuite/filter')->checkRule($object1, $rule->getId()))
                    $vivod .= "<td>true</td>";
                else
                    $vivod .= "<td>false</td>";

                if (Mage::getModel('marketsuite/filter')->checkRule($object2, $rule->getId()))
                    $vivod .= "<td>true</td>";
                else
                    $vivod .= "<td>false</td>";
            }
            catch (Exception $ex)
            {
                $vivod .= "<td>Bug! ".$ex->getMessage()."</td>";
            }
            echo $vivod;
        }
        echo "</table>";
        die;
    }
}