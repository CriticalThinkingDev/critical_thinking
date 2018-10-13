<?php 
class Krishinc_Reviews_Model_Resource_Review_Collection extends Mage_Review_Model_Mysql4_Review_Collection
{
/*****************
     * Added by bijal for location field
     ***/
    /**
     * init select
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(array('detail1' => $this->_reviewDetailTable),
                'main_table.review_id = detail1.review_id',
                array('location' )); 
        return $this;
    }
    
    /**
     * Set date order
     *
     * @param string $dir
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function setDateOrder($dir = 'DESC')
    { 
        $this->setOrder('main_table.created_at', $dir);
        
        return $this;
    }

}