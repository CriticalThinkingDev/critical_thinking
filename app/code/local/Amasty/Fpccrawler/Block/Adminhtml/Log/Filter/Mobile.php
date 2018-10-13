<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
class Amasty_Fpccrawler_Block_Adminhtml_Log_Filter_Mobile extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    public function getCondition()
    {
        $value = $this->getValue();
        if (is_null($value) || $value == 0) {
            return null;
        }

        return array('or' => array('eq' => $value));
    }
}