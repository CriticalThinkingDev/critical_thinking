<?php
class Krishinc_Autosendtrack_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

 $tracks = Mage::getModel('sales/order_shipment_track')->getCollection()->addAttributeToFilter('order_id',49591);

 foreach($tracks as $track){
           $shipment = $track->getShipment();
            $shipment->sendEmail();
               

        }

         exit('123');    



        $dateObj = Mage::getSingleton('core/date');
        $toDate = $dateObj->gmtDate('Y-m-d H:i:s');

        $weekday = date("l");
        if($weekday=='Monday'){
            $fromDate = date('Y-m-d H:i:s', strtotime('-4 day', strtotime($toDate)));
        }else{
           $fromDate = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($toDate)));
        }
        $tracks = Mage::getModel('sales/order_shipment_track')->getCollection()->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate,date=>true));

        foreach($tracks as $track){
            $shipment = $track->getShipment();
            //$shipment->sendEmail();
             Mage::log($shipment->getIncrementId(),null,'autosendtrack.log');

        }
        //$shipment = Mage::getModel('sales/order_shipment')->load('24894');
    }

    public function indexbackAction()
    {



        $dateObj = Mage::getSingleton('core/date');
        $toDate = $dateObj->gmtDate('Y-m-d H:i:s');

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
                //$shipment->sendEmail();
                Mage::log($shipment->getIncrementId(),null,'autosendtrack.log');
            }
        }
        //$shipment = Mage::getModel('sales/order_shipment')->load('24894');
    }
 public function updatepriorityAction(){
       Mage::getModel('autosendtrack/autosendtrack')->updatePriority();

    }

    public function backupAction(){

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('priority')
            ->addAttributeToFilter('status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addFieldToFilter('attribute_set_id', 12)
            ->addFieldToFilter('type_id', 'grouped');

        foreach ($collection as $product) {

            $priority = $product->getPriority();

            Mage::log('Product Id  : '.$product->getId(), null,'grouped_old_priority_backup.log');
            Mage::log('Priority : '.$product->getPriority(), null,'grouped_old_priority_backup.log');
            Mage::log('--------------', null,'grouped_old_priority_backup.log');



        }

    }
 public function backupseriesAction(){

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('priority')
            ->addAttributeToFilter('status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addFieldToFilter('attribute_set_id', 11)
            ->addFieldToFilter('type_id', 'grouped');

        foreach ($collection as $product) {

            $priority = $product->getPriority();

            Mage::log('Product Id  : '.$product->getId(), null,'series_old_priority_backup.log');
            Mage::log('Priority : '.$product->getPriority(), null,'series_old_priority_backup.log');
            Mage::log('--------------', null,'series_old_priority_backup.log');



        }

    }
}
