<?php

class MageExt_FixRound_Model_Store extends Mage_Core_Model_Store
{
     public function roundPrice($price)
    {
		//return $price;
        return round($price, 4);
    }

    public function formatPrice($price, $includeContainer = true)
    {
        if ($this->getCurrentCurrency()) {
            return $this->getCurrentCurrency()->format(round($price,2), array(), $includeContainer);
        }
        return $price;
    }
}
