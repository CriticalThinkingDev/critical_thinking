<?php

/**
 * Created by PhpStorm.
 * User: paras
 * Date: 10/12/13
 * Time: 4:47 PM
 */


ini_set('display_errors', 1);
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
umask(0);

Mage::app('default');

/*working with https*/
$client = new Zend_XmlRpc_Client('http://www.criticalthinking.com/api/xmlrpc');

/*not working with https*/
//$client = new SoapClient('http://www.criticalthinking.com/api/soap/?wsdl');
$session = $client->call('login', array('paras123', 'paras123'));

$filters = array(
    'sku' => array('like'=>'11902%')
);
$products = $client->call('call', array($session, 'product.list',));



echo '<pre>';
print_r($products);
echo '</pre>';

?>

