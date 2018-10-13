<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 11-10-21
 * Time: 0:09
 */
 
class Unirgy_StoreLocator_Model_Import
    extends Mage_Core_Model_Config_Data
{
    const TITLE = 'title';
    const ADDRESS = 'address';
    /**
     * @var array
     */
    protected $_importStoreIds;
    protected $_importHeaders;
    protected $_importErrors;
    protected $_importedRows;
    protected $_tableColumns;

    /**
     * @var Unirgy_StoreLocator_Model_Mysql4_Location
     */
    protected $_locations_resource;
    public $_importHeadersIdx;
    public $_validation_length;

    protected function _afterSave()
    {
        if (empty($_FILES['groups']['tmp_name']['general']['fields']['upload_locations']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['general']['fields']['upload_locations']['value'];

        $storeIds = array();
        $app = Mage::app();
        $website = $app->getWebsite($this->getScopeId());

        switch($this->getData('scope')) {
            case 'websites':
                $website = $app->getWebsite($this->getData('website_code'));
                $storeIds = $website->getStoreIds();
                break;
            case 'stores' :
                $storeIds = (array) $app->getStore($this->getStoreCode())->getId();
                break;
            default :
                $storeIds = (array) $app->getStore()->getId();
                break;
        }

        $this->_importStoreIds      = $storeIds;
        $this->_importErrors        = array();
        $this->_importedRows        = 0;

        $io = new Varien_Io_File();
        $info = pathinfo($csvFile);
        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');

        // check and skip headers
        $headers = $this->_setHeaders($io->streamReadCsv());
        if ($headers === false || count($headers) < 2) {
            $io->streamClose();
            Mage::throwException(Mage::helper('ustorelocator')->__('Invalid Store Locations File Format. Provide at least "title" and "address".'));
        }

        $resource = $this->_getLocationResource();

        /* @var $hlp Unirgy_StoreLocator_Helper_Data */
        $hlp = Mage::helper('ustorelocator');


        /* @var $adapter Varien_Db_Adapter_Pdo_Mysql */
        $adapter = $resource->getReadConnection();
        $adapter->beginTransaction();
        $overwrite = $this->_shouldOverwrite();
        try {
            $rowNumber  = 1;
            $importData = array();

            while (false !== ($csvLine = $io->streamReadCsv())) {
                $rowNumber ++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if ($rowNumber == 5000) {
                    $this->_saveImportData($importData);
                    $importData = array();
                }
            }

            $io->streamClose();

            if($overwrite){
                $adapter->delete($resource->getMainTable());
            }

            $this->_saveImportData($importData);
            $adapter->commit();
            $hlp->populateEmptyGeoLocations();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::logException($e);
            Mage::throwException($hlp->__('An error occurred while importing locations.'));
        }


        if ($this->_importErrors) {
            $error = $hlp->__('%1$d locations have been imported. See the following list of errors for each locations that has not been imported: %2$s',
                                                  $this->_importedRows, implode(" \n", $this->_importErrors));
            Mage::throwException($error);
        }
    }

    /**
     * @return Unirgy_StoreLocator_Model_Mysql4_Location
     */
    protected function _getLocationResource()
    {
        $resource = $this->_locations_resource;
        if(!$resource) {
            $resource = Mage::getResourceSingleton('ustorelocator/location');
            $this->_locations_resource = $resource;
        }
        return $resource;
    }


    /**
     * Prepare import data
     * There are 2 required fields - title, address
     *
     * There are many optional fields, bellow are defaults used in case of an empty value:
     *
     * latitude => 0, // coordinate of lactation, if omitted, it will be searched from google.
     * longitude => 0, // coordinate of location
     * address_display => 'address', // formatted address to be displayed to users. If omitted, 'address' field will be used
     * notes => '', // notes that will be displayed to users when clicking on store icon
     * website_url => '', // store website or contact email. To be used as url, start it with http
     * phone => '', // store phone number
     * product_types => '', // store tags, what type of products are sold here. semicolon list of tags
     * country => null, // country of the location, not verified
     * stores => null, // string of semicolons separated store codes
     * icon => null, // custom icon file
     * use_label => 1, // should numeric label be displayed over the Icon used
     * is_featured => 0, // is product in featured list
     * zoom => 10 // initial location zoom
     * udropship_vendor => null, // if dropship  is installed, you can relate location to a vendor
     * 
     * @param array $row
     * @param int $rowNumber
     * @return array
     */
    protected function _getImportRow($row, $rowNumber)
    {
        $required = array(self::TITLE, self::ADDRESS);
        $hlp = Mage::helper('ustorelocator');
        $set_addr = false;
        $import_row = array();
        foreach($this->_importHeaders as $key => $field) {
            if(in_array($field, $required) && (!isset($row[$key]) || empty($row[$key]))) {
                $this->_importErrors[] = $hlp->__("Empty required field: '%s' in row: %s", $field, $rowNumber);
                return false;
            }
            $value = isset($row[$key])? $row[$key]: null;
            switch($field) {
                case 'latitude':
                case 'longitude':
                    if(null === $value) {
                        $value = 0;
                    } else {
                        $value = round(Mage::app()->getLocale()->getNumber($value), 9);
                    }
                    break;
                case 'address_display':
                    if(null === $value) {
                        if(isset($import_row[self::ADDRESS])) { // if we have set look up address, use it
                            $set_addr = false;
                            $value = $import_row[self::ADDRESS];
                        } else {
                            $set_addr = $key; // else set flag to update this field
                        }
                    }
                    break;
                case 'address' :
                    if($set_addr !== false) {
                        $import_row[$set_addr] = $value;// if we are here, address is not empty
                    }
                    break;
                case 'notes':
                    if($value === null) {
                        $value = '';
                    }
                    break;
                case 'phone':
                case 'product_types':
                case 'website_url':
                case 'country':
                case 'stores':
                case 'icon':
                    $length = isset($this->_validation_length[$field]) ? $this->_validation_length[$field] : null;
                    if($value === null) {
                        $value = '';
                    } else if($length && strlen($value) > $length) {
                        $this->_importErrors[] = $hlp->__("Value for '%s' is too long. Max allowed length is %s", $field, $length);
                        return false;
                    }
                    break;
                case 'use_label':
                    if($value === null) {
                        $value = 1;
                    } else {
                        $value = ($value == 1) ? 1 : 0;
                    }
                    break;
                case 'is_featured':
                    if($value === null) {
                        $value = 0;
                    } else {
                        $value = ($value == 1) ? 1 : 0;
                    }
                    break;
                case 'zoom':
                    if($value === null) {
                        $value = 10;
                    } else {
                        $value = (int) $value;
                        if( $value < 1) {
                            $value = 1;
                        } else if($value > 25) {
                            $value = 25;
                        }
                    }
                    break;
                case 'udropship_vendor':
                    $ud = Mage::getConfig()->getNode('modules/Unirgy_Dropship');
                    if (!$ud || (string)$ud->active == 'false') {
                        $value = null;
                        break;
                    }
                    if($value !== null) {
                        $vendor = Mage::getModel('udropship/vendor');
                        $value = $vendor->getId();
                    }
                    break;
            }
            $import_row[$key] = $value;
        }
        return $import_row;
    }

    protected function _saveImportData(array $data)
    {
        if(!empty($data)) {
            $columns = $this->_importHeaders;
            $resource = $this->_getLocationResource();
            $resource->getReadConnection()->insertArray($resource->getMainTable(), $columns, $data);
        }
    }

    protected function _setHeaders($headers)
    {
        $resource = $this->_getLocationResource();
        $conn = $resource->getReadConnection();
        $table = $conn->describeTable($resource->getMainTable());
        foreach($table as $column) {
            $col_name = $column['COLUMN_NAME'];
            $key = array_search($col_name, $headers);
            if($key !== false) {
                $this->_importHeaders[$key] = $col_name;
                $this->_importHeadersIdx[$col_name] = $key;
                if(!empty($column['LENGTH'])) {
                    $this->_validation_length[$col_name] = $column['LENGTH'];
                }
            }
        }
        if(!empty($this->_importHeadersIdx) && isset($this->_importHeadersIdx[self::TITLE], $this->_importHeadersIdx[self::ADDRESS])) {
            return $this->_importHeaders;
        }
        return false;
    }

    protected function _shouldOverwrite()
    {
        $ov = Mage::getStoreConfig('ustorelocator/general/upload_overwrite');
        if (isset($_POST['groups']['general']['fields']['upload_overwrite']['value'])) {
            $ov = $_POST['groups']['general']['fields']['upload_overwrite']['value'];
        }
        return $ov;
    }
}
