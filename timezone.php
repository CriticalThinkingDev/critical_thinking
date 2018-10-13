<?php

require_once('app/Mage.php'); //Path to Magento

umask(0);

Mage::app();


echo "magento time ->";
echo Mage::getModel('core/date')->date('Y-m-d H:i:s');


echo '</br>';
echo "server_timezone"; echo '</br>';
echo date_default_timezone_get();
echo '</br>';

echo "server time ->";

$t = time();

echo(date("Y-m-d H:i:s",$t));
