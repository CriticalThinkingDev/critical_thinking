<?php

class Krishinc_Autosendtrack_Model_Autosendtrack extends Mage_Core_Model_Abstract
{
   public function sendAutotrack(){
       Mage::log('cron_start_executed',null,'autosendtrack.log');

       $dateObj = Mage::getSingleton('core/date');
       $toDate = $dateObj->gmtDate('Y-m-d H:i:s');
       Mage::log($toDate,null,'autosendtrack.log');

       $weekday = date("l");
       if($weekday=='Monday'){
           $fromDate = date('Y-m-d H:i:s', strtotime('-3 day', strtotime($toDate)));
       }else{
           $fromDate = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($toDate)));
       }
       $tracks = Mage::getModel('sales/order_shipment_track')->getCollection()->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate,date=>true));

       foreach($tracks as $track){
           $shipment = $track->getShipment();
           $shipment->sendEmail();
           Mage::log($shipment->getIncrementId(),null,'autosendtrack.log');

       }
       Mage::log('cron_end_executed',null,'autosendtrack.log');
       Mage::log($dateObj->gmtDate('Y-m-d H:i:s'),null,'autosendtrack.log');
   }


    public function sendAutotrackbackup(){
        Mage::log('cron_start_executed',null,'autosendtrack.log');

        $dateObj = Mage::getSingleton('core/date');
        $toDate = $dateObj->gmtDate('Y-m-d H:i:s');
        Mage::log($toDate,null,'autosendtrack.log');

        $weekday = date("l");
        if($weekday=='Monday'){
            $fromDate = date('Y-m-d H:i:s', strtotime('-4 day', strtotime($toDate)));
        }else{
            $fromDate = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($toDate)));
        }
        $shipments = Mage::getModel('sales/order_shipment')->getCollection()->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate,date=>true));
        foreach($shipments as $shipment){
            $tracks = $shipment->getAllTracks();
            if($tracks){
                ///$shipment->sendEmail();
            }
        }
        Mage::log('cron_end_executed',null,'autosendtrack.log');
        Mage::log($dateObj->gmtDate('Y-m-d H:i:s'),null,'autosendtrack.log');
    }

 public function updatePriority(){
        Mage::log('start', null,'cron_grouped_priority.log');
        $dateObj = Mage::getSingleton('core/date');
        $toDate = $dateObj->gmtDate('Y-m-d H:i:s');
        Mage::log($toDate,null,'cron_grouped_priority.log');
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('priority')
            ->addAttributeToSelect('override')
            ->addAttributeToFilter('status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addFieldToFilter('attribute_set_id', 12)
            ->addFieldToFilter('override', 0)
            ->addFieldToFilter('type_id', 'grouped');

        foreach ($collection as $product) {

            $priority = $product->getPriority();

            Mage::log($product->getId(), null,'grouped_old_priority.log');
            Mage::log($product->getPriority(), null,'grouped_old_priority.log');
            Mage::log('--------------', null,'grouped_old_priority.log');
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            if(count($associatedProducts) > 0){

                $priorityArray = array();
                foreach($associatedProducts as $ass_pro) {
                    $priorityArray[] = $ass_pro->getPriority();
                }
                $highestPriority = max($priorityArray);
                if($priority < $highestPriority){
                    $product->setPriority($highestPriority)->save();
                    Mage::log($product->getId(), null,'grouped_new_priority.log');
                    Mage::log($highestPriority, null,'grouped_new_priority.log');
                     Mage::log('--------------', null,'grouped_new_priority.log');
                }



            }


        }
        Mage::log('end', null,'cron_grouped_priority.log');

    }

public function updateSeries(){

        Mage::log('start', null,'cron_series_priority.log');
        $dateObj = Mage::getSingleton('core/date');
        $toDate = $dateObj->gmtDate('Y-m-d H:i:s');
        Mage::log($toDate,null,'cron_series_priority.log');
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('priority')
            ->addAttributeToSelect('override')
            ->addAttributeToFilter('status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addFieldToFilter('attribute_set_id', 11)
            ->addFieldToFilter('override', 0)
            ->addFieldToFilter('type_id', 'grouped');
        $collection->setPageSize(20);
        $pages = $collection->getLastPageNumber();
        $j =0;
        for($i=1; $i<=$pages; $i++) {
            $collection->setCurPage($i);
            $collection->load();
            foreach ($collection as $product) {
                $j++;

                $priority = $product->getPriority();

                Mage::log($product->getId(), null, 'series_old_priority.log');
                Mage::log($product->getPriority(), null, 'series_old_priority.log');
                Mage::log('--------------', null, 'grouped_old_priority.log');
                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

                if (count($associatedProducts) > 0) {

                    $priorityArray = array();
                    foreach ($associatedProducts as $ass_pro) {
                        if ($ass_pro->getTypeId() == 'grouped') {
                            continue;
                        }
                        $priorityArray[$ass_pro->getId()] = $ass_pro->getPriority();
                    }

                    $highestPriority = array_sum($priorityArray);

                    if ($priority < $highestPriority) {
                        $product->setPriority($highestPriority)->save();
                        Mage::log($product->getId(), null, 'series_new_priority.log');
                        Mage::log($highestPriority, null, 'series_new_priority.log');
                        Mage::log('--------------', null, 'series_new_priority.log');
                    }


                }


            }
            $collection->clear();

        }

        Mage::log('end', null,'cron_series_priority.log');

    }
}
