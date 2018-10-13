<?php
/**
 * Downloadplus Link Title History Model
 *
 * @author     PILLWAX Industrial Solutions Consulting
 * @category   Pisc
 * @package    Pisc_DownloadplusBonus
 * @copyright  Copyright (c) 2014 PILLWAX Industrial Solutions Consulting (http://technology.pillwax.com/software)
 * @license    Commercial Unlimited License (http://technology.pillwax.com/software/license)
 * @version    0.1.0
 */

class Pisc_Downloadplus_Model_Link_Title_History extends Mage_Core_Model_Abstract
{

    protected $_eventPrefix = 'downloadplus_link_title_history';

    protected function _construct()
    {
        $this->_init('downloadplus/link_title_history', 'title_id');
    }

    public function getCurrentTitle($link = null, $default = null)
    {
        $title = $default;
        if ($link instanceof Mage_Downloadable_Model_Link || $link instanceof Pisc_Downloadplus_Model_Link) {
            if ($link instanceof Pisc_Downloadplus_Model_Link) {
                $title = $link->getLinkTitle();
            }
            $collection = $this->getCollection();
            $collection->addFieldToFilter('link_id', Array('eq'=>$link->getId()));
            $collection->addFieldToFilter('store_id', Array('eq'=>$this->getDataSetDefault('store_id',0)));
            $collection->setOrder('updated_at', 'DESC');
            if ($collection->getSize()>0) {
                if ($recent = $collection->getFirstItem()) {
                    $title = $recent->getTitle();
                }
            }
        }
        if ($link instanceof Mage_Downloadable_Model_Link_Purchased_Item) {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('link_id', Array('eq'=>$link->getLinkId()));
            $collection->addFieldToFilter('store_id', Array('eq'=>$this->getDataSetDefault('store_id',0)));
            $collection->setOrder('updated_at', 'DESC');
            if ($collection->getSize()>0) {
                if ($recent = $collection->getFirstItem()) {
                    $title = $recent->getTitle();
                }
            }
            
        }
        
        return $title;
    }
    
}