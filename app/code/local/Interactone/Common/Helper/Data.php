<?php

class Interactone_Common_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEBUG = 'interactone/settings/debug';

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isModuleInstalled($moduleName = null)
    {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();

        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        return isset($modules[$moduleName]);
    }

    /**
     * @param string $moduleName
     * @return string
     */
    public function getModuleVersion($moduleName = null)
    {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();

        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        return isset($modules[$moduleName]) ? (string) $modules[$moduleName]->version : null;
    }

    /**
     * @return bool
     */
    public function isDebugEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_DEBUG);
    }

    /**
     * @param string $message
     * @param string|null $file
     */
    public function writeDebug($message, $file = null)
    {
        if ($this->isDebugEnabled()) {
            if (empty($file)) {
                $file = strtolower($this->_getModuleName()) . '.log';
            }
            Mage::log($message, Zend_Log::DEBUG, $file, true);
        }
    }

    /**
     * @param Exception|string $message
     * @param string|null $file
     */
    public function writeError($message, $file = null)
    {
        if (empty($file)) {
            $file = strtolower($this->_getModuleName()) . '.log';
        }
        if ($message instanceOf Exception) {
            $message = $message->getMessage();
        }
        Mage::log($message, Zend_Log::ERR, $file, true);
    }
}