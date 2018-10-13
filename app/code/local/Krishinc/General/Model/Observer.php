<?php
class Krishinc_General_Model_Observer
{
    public function reindexCatalogSearch()
    {
        $process = Mage::getModel('index/indexer')->getProcessByCode('catalogsearch_fulltext');
        try{
            $process->reindexAll();
        }catch(Exception $e){
            Mage::logException($e);
        }
    }
}