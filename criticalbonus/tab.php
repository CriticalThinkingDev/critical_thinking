<?php
// create the Facebook Graph SDK object
require_once('facebook.php');
$facebook = new Facebook(array(
			       'appId'=>'134581186676413', // replace with your value
			       'secret'=>'395c2e47952a1927334d5d8414794880' // replace with your value
			       ));
$signedRequest = $facebook->getSignedRequest();

// Inspect the signed request
if($signedRequest['page']['liked'] == 1){
  require("fan.php");
 } else {
  require("nonfan.php");
 }
?>