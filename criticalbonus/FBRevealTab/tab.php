<?php
// create the Facebook Graph SDK object
require_once('facebook.php');
$facebook = new Facebook(array(
			       'appId'=>'xxxxxxxxxx', // replace with your value
			       'secret'=>'xxxxxxxxx' // replace with your value
			       ));
$signedRequest = $facebook->getSignedRequest();

// Inspect the signed request
if($signedRequest['page']['liked'] == 1){
  require("fan.php");
 } else {
  require("nonfan.php");
 }
?>