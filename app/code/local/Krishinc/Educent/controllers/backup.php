<?php
class Krishinc_Educent_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

        $store = Mage::app()->getStore();
        $website = Mage::app()->getWebsite();

        $firstName = 'John';
        $lastName = 'Doe';
        $email = 'johndoe@example.com';
        $logFileName = 'my-order-log-file.log';

        $billingAddress = array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $firstName,
            'middlename' => '',
            'lastname' => $lastName,
            'suffix' => '',
            'company' => '',
            'street' => array(
                '0' => 'Your Customer Address 1', // compulsory
                '1' => 'Your Customer Address 2' // optional
            ),
            'city' => 'Culver City',
            'country_id' => 'US', // two letters country code
            'region' => 'California', // can be empty '' if no region
            'region_id' => '12', // can be empty '' if no region_id
            'postcode' => '90232',
            'telephone' => '888-888-8888',
            'fax' => '',
            'customer_type'=>"H",
            'save_in_address_book' => 1
        );

        $shippingAddress = array(
            'customer_address_id' => '',
            'prefix' => '',
            'firstname' => $firstName,
            'middlename' => '',
            'lastname' => $lastName,
            'suffix' => '',
            'company' => '',
            'street' => array(
                '0' => 'Your Customer Address 11', // compulsory
                '1' => 'Your Customer Address 222' // optional
            ),
            'city' => 'Culver City',
            'country_id' => 'US', // two letters country code
            'region' => 'California', // can be empty '' if no region
            'region_id' => '12', // can be empty '' if no region_id
            'postcode' => '90232',
            'telephone' => '888-888-8888',
            'fax' => '',
            'customer_type'=>"H",
            'save_in_address_book' => 1
        );
        $shippingPrice = 0;
        Mage::register('shipping_cost', $shippingPrice);

        $shippingMethod = 'flatrate_flatrate';


        $paymentMethod = 'cashondelivery';

        $productIds = array(1761);
        $quote = Mage::getModel('sales/quote')
            ->setStoreId($store->getId());

// Set currency for the quote
        $quote->setCurrency(Mage::app()->getStore()->getBaseCurrencyCode());

        $customer = Mage::getModel('customer/customer');

        $customer->setWebsiteId($website->getId())
            ->setStore($store)
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setMode("guest")
            ->setEmail($email);
        $quote->assignCustomer($customer);
        $quote->setCustomerEmail($email);
        $quote->setOrderType('EDU');
        foreach($productIds as $productId) {
            $qty =1;
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);

            $quote->addProduct($product, $qty);


        }
        $billingAddressData = $quote->getBillingAddress()->addData($billingAddress);

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

        $quote->collectTotals();
        $quote->save();





        $quote->getPayment()->importData(array('method' => $paymentMethod));


        // Collect totals of the quote
        $quote->collectTotals();

        // Save quote
        $quote->save();


        // Create Order From Quote
        $service = Mage::getModel('sales/service_quote', $quote);
        $data =array('order_type','EDU') ;
        $service->setOrderData($data);
        $service->submitAll();

        $incrementId = $service->getOrder()->getRealOrderId();
        $odr = Mage::getModel('sales/order')->load($service->getOrder()->getId());

        $odr->setOrderType('EDU')->save();
        // p($odr->getData());

        Mage::getSingleton('checkout/session')
            ->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->clearHelperData();



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
        //extract(json_decode($response, true));
        pp($response);
        p('123');

        $this->loadLayout();
        $this->renderLayout();
    }
}