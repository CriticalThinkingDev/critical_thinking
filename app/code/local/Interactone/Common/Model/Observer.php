<?php

/**
 * @category   Interactone
 * @package    Interactone_Common
 */
class Interactone_Common_Model_Observer
{
    const CRON_MINUTELY = 'I';
    const CRON_HOURLY   = 'H';
    const CRON_DAILY    = 'D';

    /**
     * Return array of cron frequency types
     *
     * @return array
     */
    public function getCronFrequencyTypes()
    {
        return array(
            self::CRON_MINUTELY => Mage::helper('cron')->__('Minute Intervals'),
            self::CRON_HOURLY   => Mage::helper('cron')->__('Hourly'),
            self::CRON_DAILY    => Mage::helper('cron')->__('Daily')
        );
    }

    /**
     * Return array of cron valid munutes
     *
     * @return array
     */
    public function getCronMinutes()
    {
        return array(
            5  => Mage::helper('cron')->__('5 minutes'),
            10 => Mage::helper('cron')->__('10 minutes'),
            15 => Mage::helper('cron')->__('15 minutes'),
            20 => Mage::helper('cron')->__('20 minutes'),
            30 => Mage::helper('cron')->__('30 minutes')
        );
    }
}