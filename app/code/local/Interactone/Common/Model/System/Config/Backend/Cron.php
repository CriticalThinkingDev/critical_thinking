<?php

abstract class Interactone_Common_Model_System_Config_Backend_Cron
    extends Mage_Core_Model_Config_Data
{
    protected $_cron_string_path;
    protected $_cron_model_path;
    protected $_xml_path_enabled = 'enabled';

    /**
     * Cron settings after save
     *
     * @return Interactone_Common_Model_System_Config_Backend_Cron
     */
    protected function _afterSave()
    {
        $cronExprString = '';

        if ($this->getFieldsetDataValue($this->_xml_path_enabled)) {
            $minutely  = Interactone_Common_Model_Observer::CRON_MINUTELY;
            $hourly    = Interactone_Common_Model_Observer::CRON_HOURLY;
            $daily     = Interactone_Common_Model_Observer::CRON_DAILY;
            $frequency = $this->getFieldsetDataValue('frequency');

            if ($frequency == $minutely) {
                $interval = (int) $this->getFieldsetDataValue('interval');
                $cronExprString = "*/{$interval} * * * *";
            } elseif ($frequency == $hourly) {
                $minutes = (int) $this->getFieldsetDataValue('minutes');
                if ($minutes >= 0 && $minutes <= 59) {
                    $cronExprString = "{$minutes} * * * *";
                } else {
                    Mage::throwException(Mage::helper('interactone_common')->__('Please, specify correct minutes of hour.'));
                }
            } elseif ($frequency == $daily) {
                $time = $this->getFieldsetDataValue('time');
                $timeMinutes = intval($time[1]);
                $timeHours = intval($time[0]);
                $cronExprString = "{$timeMinutes} {$timeHours} * * *";
            }
        }

        try {

            Mage::getModel('core/config_data')
                ->load($this->_cron_string_path, 'path')
                ->setValue($cronExprString)
                ->setPath($this->_cron_string_path)
                ->save();

            Mage::getModel('core/config_data')
                ->load($this->_cron_model_path, 'path')
                ->setValue((string) Mage::getConfig()->getNode($this->_cron_model_path))
                ->setPath($this->_cron_model_path)
                ->save();
            
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save Cron expression'));
        }
    }
}
