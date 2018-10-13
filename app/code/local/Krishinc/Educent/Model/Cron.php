<?php
class Krishinc_Educent_Model_Cron{
    public function import(){
        Mage::log('Start',null,'educents.log');
        Mage::app()->setCurrentStore(1);

        // Mage::log(Mage::app()->getStore()->getId(),null,'educents.log');
        $username = 'Z2CzZTAQZkMZqX5lYp7RvdbVNi4Z3I92';
        $url = 'http://api.educents.com/api/v1/shipments?status=processing';
        $headers = array(
            'Content-Type: application/json;',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_USERPWD, "$username:");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $response = curl_exec($ch);

        $response = Mage::helper('core')->jsonDecode($response);

        $orderCount = $response['order_count'];
        if($orderCount){
            $store = Mage::app()->getStore();
            $website = Mage::app()->getWebsite();
            Mage::app('admin');
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $orders =$response['orders'];
            $emailResponse = array();

            foreach($orders as $order){
                $eorderId = $order['order_id'];
                $educent = Mage::getModel('educent/educent');
                $educent->load($eorderId ,'order_id');
                if($educent->getId()){

                    continue;
                }
               $cleanshippingStr = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $order['ship_name'])));
                $shippingNames = explode(" ",$cleanshippingStr);
                $sfirstName = $shippingNames[0];
                $slastName = $shippingNames[1];

                $cleanbillingStr = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $order['billing_name'])));
                $billingNames = explode(" ",$cleanbillingStr);
                $bfirstName = $billingNames[0];
                $blastName = $billingNames[1];
                //    p($shippingNames);

                $regionId = Mage::getModel('directory/region')->loadByName($order['state'],$order['country'])->getId();
                if(!$regionId){
                    Mage::log('No region found',null,'educents.log');
                    Mage::log($order,null,'educents.log');
                    $regionId = 12;
                }

                $email = '';
                $billingAddress = array(
                    'customer_address_id' => '',
                    'prefix' => '',
                    'firstname' => $bfirstName,
                    'middlename' => '',
                    'lastname' => $blastName,
                    'suffix' => '',
                    'company' => '',
                    'street' => array(
                        '0' => $order['billing_address_line_1'], // compulsory
                        '1' => $order['billing_address_line_2'] // optional
                    ),
                    'city' => $order['billing_city'],
                    'country_id' => $order['billing_country'], // two letters country code
                    'region' => $order['billing_state'], // can be empty '' if no region
                    'region_id' => $regionId, // can be empty '' if no region_id
                    'postcode' => $order['billing_ZIP'],
                    'telephone' => '888-888-8888',
                    //'fax' => '',
                    'customer_type'=>"H",
                    'save_in_address_book' => 1
                );


                $shippingAddress = array(
                    'customer_address_id' => '',
                    'prefix' => '',
                    'firstname' => $sfirstName,
                    'middlename' => '',
                    'lastname' => $slastName,
                    'suffix' => '',
                    'company' => '',
                    'street' => array(
                        '0' => $order['address_line_1'], // compulsory
                        '1' => $order['address_line_2'] // optional
                    ),
                    'city' => $order['city'],
                    'country_id' => $order['country'], // two letters country code
                    'region' => $order['state'], // can be empty '' if no region
                    'region_id' => $regionId, // can be empty '' if no region_id
                    'postcode' => $order['ZIP'],
                    'telephone' => '888-888-8888',
                    // 'fax' => '',
                    'customer_type'=>"H",
                    'save_in_address_book' => 1
                );

                $shippingMethod = 'premiumrate_Standard_Shipping_(7-14_business_days)';
                $paymentMethod = 'purchaseorder';
                try {
                    $quote = Mage::getModel('sales/quote')
                        ->setStoreId($store->getId());


                    $quote->setCurrency(Mage::app()->getStore()->getBaseCurrencyCode());

                    $customer = Mage::getModel('customer/customer');

                    $customer->setWebsiteId($website->getId())
                        ->setStore($store)
                        ->setFirstname($bfirstName)
                        ->setLastname($blastName)
                        ->setMode("guest")
                        ->setEmail($email);
                    $quote->assignCustomer($customer);
                    $quote->setCustomerEmail($email);
                    $eduitems = array();
                    foreach($order['order_items'] as $item) {
                        //p($item);

                        $qty =$item['qty_ordered'];
                        $eduitem['sku'] = $item['sku'];
                        $eduitem['seller_sku'] = $item['seller_sku'];
                        $eduitem['qty'] = $qty;
                        $eduitems[] = $eduitem;
                        $proId = Mage::getModel("catalog/product")->getIdBySku($item['seller_sku']);
                        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($proId);
                        $quote->addProduct($product, $qty);


                    }
                    $eduitems = serialize($eduitems);
                    $quote->getBillingAddress()->addData($billingAddress);
                    $quote->getShippingAddress()->collectTotals();
                    //   p(get_class($quote->getShippingAddress()));
                    $shippingAddressData = $quote->getShippingAddress()->addData($shippingAddress);
                    $quote->getShippingAddress()->setShippingMethod($shippingMethod);
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                    $quote->getShippingAddress()->collectShippingRates();


                    $address = $quote->getShippingAddress();
                    $price=0;

                    $address->setShippingAmount($price);
                    $address->setBaseShippingAmount($price);
                    // Find if our shipping has been included.
                    $rates = $address->collectShippingRates()
                        ->getGroupedAllShippingRates();
                    foreach ($rates as $carrier) {
                        foreach ($carrier as $rate) {
                            $rate->setPrice($price);
                            $rate->save();

                        }
                    }
                    $address->setCollectShippingRates(false);

                    $address->save();
		$quote->setCouponCode('EDUAPI');
 			 $quote->collectTotals();
                    $quote->save();
		$eorderId = 'EDU'.$eorderId;
                    $quote->getPayment()->importData(array('method' => $paymentMethod,'po_number' => $eorderId));


                    // Collect totals of the quote
                    $quote->collectTotals()->save();

                    // Save quote
                  


                    // Create Order From Quote
                    $service = Mage::getModel('sales/service_quote', $quote);
                    $service->submitAll();
                    $incrementId = $service->getOrder()->getRealOrderId();
                    $odr = Mage::getModel('sales/order')->load($service->getOrder()->getId());
$odr->setData('source_code', 'EDUAPI');
                    $odr->setOrderType('EDU')->save();
                    Mage::getSingleton('checkout/session')
                        ->setLastQuoteId($quote->getId())
                        ->setLastSuccessQuoteId($quote->getId())
                        ->clearHelperData();



                    $array = array();
                    $array['order_id'] = $order['order_id'];
                    $array['real_order_id'] = $service->getOrder()->getId();
                    $array['items'] = $eduitems;
                    $emailResponseitem = array('id'=>$incrementId);
                    $emailResponse[] = $emailResponseitem;
                    $educent->setData($array)->save();
                    Mage::log('Success',null,'educents.log');
                } catch (Exception $e) {
                    Mage::log('Catch Error',null,'educents.log');
                    Mage::log($e->getMessage(),null,'educents.log');
                }

            }


            if(count($emailResponse)){
                $html = '';
                foreach($emailResponse as $m){

                    
                    $html .= '<p>Imported Order Id '.$m['id'].'</p>';
                  

                }

                $mailTo = "niled@criticalthinking.com";
                $bcc = "paras.sakaria@krishtechnolabs.com";
                $this->sendStasticsMail($html,$mailTo,"Educents Track Cron Run",$bcc);
            }



        }else{
            Mage::log('No Orders',null,'educents.log');
        }
        Mage::log('End',null,'educents.log');
    }

    public function createshipment(){
        Mage::log('start',null,'educents-track.log');
        $educent = Mage::getModel('educent/educent')->getCollection()->addFieldToFilter('track',0);
        if(count($educent->getData())){
            $username = 'Z2CzZTAQZkMZqX5lYp7RvdbVNi4Z3I92';
            $url = 'http://api.educents.com/api/v1/shipments';
            $headers = array(
                'Content-Type: application/json;',
            );
            $emailResponse = array();
            foreach($educent as $item){
                $order = Mage::getModel('sales/order')->load($item->getRealOrderId());
               $lastOrderId = $order->getIncrementId();
                $orderitems = $item->getItems();
                $orderitems = unserialize($orderitems);

                $shipment = $order->getTracksCollection()->getFirstItem();
                if(count($shipment) && $shipment->getTrackNumber()){
                    $postArrayNew =  array();
                    $orderId = $item->getOrderId();
                    $strackId = $shipment->getTrackNumber();
                    if($shipment->getCarrierCode()=='tracker1'){
                    $sTitle = 'usps';
                    }
		  if($shipment->getCarrierCode()=='tracker2'){
                    $sTitle = 'ups';
                    }
if($shipment->getCarrierCode()=='tracker3'){
                    $sTitle = 'fedex';
                    }

                    $postArrayNew['order_id'] = "$orderId";
                    $postArrayNew['tracking_num'] = "$strackId";
                    $postArrayNew['carrier'] = "$sTitle";

                    $postArrayNew['order_items'] = $orderitems;



                    $data_string = json_encode($postArrayNew);


                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                    curl_setopt($ch,CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:");
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                    $response = curl_exec($ch);
                    $response = json_decode($response);
$educent1 = Mage::getModel('educent/educent')->load($item->getId());
$educent1->setResponse($response)->save();
                    $emailResponseitem = array('id'=>$lastOrderId,'response'=>$response);
                    $emailResponse[] = $emailResponseitem;
                    Mage::log($response,null,'educents-track.log');
                    if($response=='success'){
                       
                      

$educent1->setTrack(1)->save();
Mage::log($item->getId(),null,'educents-track-save.log');
  Mage::log('scucess',null,'educents-track.log');
                        
                    }else{
 
 
                        Mage::log('error',null,'educents-track.log');
                    }

                }

            }
            if(count($emailResponse)){
$html = '';
                foreach($emailResponse as $m){

                    
                    $html .= '<p>Order Id '.$m['id'].'</p>';
                    $html .= '<p>API Response '.$m['response'].'</p>';

                }

                $mailTo = "niled@criticalthinking.com";
                $bcc = "paras.sakaria@krishtechnolabs.com";
                $this->sendStasticsMail($html,$mailTo,"Educents Track Cron Run",$bcc);
            }


        }



        Mage::log('end',null,'educents-track.log');

    }
    function sendStasticsMail($msg,$to,$sub,$bcc = '')
    {
        $msg = wordwrap($msg,70);
        $headers = 'From: service@criticalthinking.com'. "\r\n";
        $headers .= 'BCC: '.$bcc."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        mail($to,$sub,$msg,$headers);
    }
}
