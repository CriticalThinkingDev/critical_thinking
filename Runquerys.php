<?php

require_once('app/Mage.php'); //Path to Magento
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);

Mage::app();



//$Ids = array(771,772,774,775,776,777,778,779,794,795,796,797,798,799,802,803,804,48,807,808,809,1138,1139,1140,1141,1111,1113,812,813,814,818,819,822,823,834,835,831,832,846,847,848,849,855,854,853,857,858,859, 862,760,761,762,763,764,765,766,1109);
$Ids = array(
      850=>array(1305,1304),
      851=>array(1306,1307),
      852=>array(1309,1308),
      1120=>array(1302,1300,1301),
      838=>array(1280,1284,1283),
      843=>array(1285,1286,1287),
      844=>array(1288,1290,1289),
      845=>array(1293,1292,1291)
    );



foreach($Ids as $id=>$associatedProducts){

    $product = Mage::getModel('catalog/product')->load($id);
    $reviews = Mage::getModel('review/review')
        ->getResourceCollection()
        ->addEntityFilter('product', $id)->getData();


    if(count($reviews)){

        foreach($associatedProducts as $assPro){

            foreach($reviews as $review){
                $isSkip = '';
                $review = Mage::getModel('review/review')->load($review['review_id']);
                $existReviews = Mage::getModel('review/review')
                    ->getResourceCollection()
                    ->addEntityFilter('product', $assPro)->getData();

                if(count($existReviews)){

                    foreach($existReviews as $existReview){


                       //echo '<pre>'; print_r($review->getData()); echo '</pre>';
                        //echo '<pre>'; print_r($existReview); echo '</pre>'; exit;
                        if(trim($existReview['title'])==trim($review->getTitle()) &&
                            trim($existReview['detail'])==trim($review->getDetail()) &&
                        trim($existReview['nickname'])==trim($review->getNickname()) &&
                            trim($existReview['customer_id'])==trim($review->getCustomerId()) &&
                            trim($existReview['location'])==trim($review->getLocation())
                        ){
                            $isSkip = 1;

                        }
                    }

                }else{
                    $isSkip = '';
                }
                if($isSkip==1){
                    continue;
                }



                $data = $review->getData();
                $data['entity_pk_value'] = $assPro;
                unset($data['review_id']);
               //unset($data['detail_id']);
                $_review = Mage::getModel('review/review')->setData($data);
                $_review->setCreatedAt($data['created_at']);
                $_review->save();$_review->setCreatedAt($data['created_at']);$_review->save();
                $_review->aggregate();
            }
        }
    }
}




//echo '<pre>'; print_r($associatedProducts); echo '</pre>';
