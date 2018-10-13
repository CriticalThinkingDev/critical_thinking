<?php
/**
 * Unirgy_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_StoreLocator
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Unirgy_StoreLocator_LocationController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{ 
		$this->loadLayout();
	 
        $this->renderLayout();
	}
    public function mapAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function searchAction()
    {
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("markers");
        $parnode = $dom->appendChild($node);
        $req = $this->getRequest();
        try {
            $num = (int)Mage::getStoreConfig('ustorelocator/general/num_results');

            list($collection, $units) = Mage::helper('ustorelocator/protected')->getCollection($req);

            $privateFields = Mage::getConfig()->getNode('global/ustorelocator/private_fields');
            $i = 0;
            foreach ($collection as $loc){
                if($i == $num) {
                    break; // reached set limit
                }
                $node = $dom->createElement("marker");
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute("units", $units);
                $newnode->setAttribute("marker_label", ++$i);
                foreach ($loc->getData() as $k=>$v) {
                    if (!$privateFields->$k) {
                        if($k == 'icon' && !empty($v)) {
                            $v = ltrim($v, '/');
                            if($icon_info = @getimagesize(Mage::getBaseDir('media') . DS . $v)) {
                                $newnode->setAttribute('icon_width', $icon_info[0]);
                                $newnode->setAttribute('icon_height', $icon_info[1]);
                            }
                            $v = Mage::getBaseUrl('media') . $v;
                        } elseif ( $k == 'is_featured') {
                            $v = (boolean) $v;
                        }
                        $newnode->setAttribute($k, $v);
                    }
                }
            }
        } catch (Exception $e) {
            $node = $dom->createElement('error');
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute('message', $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-Type', 'text/xml', true)->setBody($dom->saveXml());
    }
    
    public function stateAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $region_id = $this->getRequest()->getParam('region_id');
        $region = $this->getRequest()->getParam('region');
        $state = '';
        if ($countrycode != '') {
        	
            $statearray = Mage::getModel('ustorelocator/location')->getCollection()
						->addFieldToSelect('region_id')->setOrder('region_id','asc')
						->addFieldToFilter('country',$countrycode); 
						
			$statearray->getSelect()->distinct(true); 
             
            if($statearray->count()>1):
            	$state = '';
	            $state .= "<select name='region_id' id='region_id' class='required-entry browser-default' onchange=\"getRegionLocations(this,'region_id','".$countrycode."');\">";
	        	$state .= "<option value=''>All States/Provinces</option>"; 
	            foreach ($statearray as $_state) {
	                $state .= "<option value='" . $_state->getData('region_id') . "'>" .$_state->getData('region_id') . "</option>";
	            }
	            $state .="</select>"; 
	        else:
	        	$statearray = Mage::getModel('ustorelocator/location')->getCollection()
						->addFieldToSelect('region')->setOrder('region','asc')
						->addFieldToFilter('country',$countrycode); 
				 $statearray->getSelect()->distinct(true);	 	 
				 if($statearray->count()>1):
	            	$state = '';
		            $state .= "<select name='region_id' id='region_id' class='required-entry' >";
		        	$state .= "<option value=''>Select State</option>";
		            foreach ($statearray as $_state) {
		                $state .= "<option value='" . $_state->getData('region') . "'>" .$_state->getData('region') . "</option>";
		            }
		            $state .="</select>"; 
	            else:
		            $state ='';
		       endif;  
	       endif;  
        }
         $this->getResponse()->setBody($state);
         
    } 

      public function alllocationsAction() { 
    	 
        $this->loadLayout(); 
        $this->renderLayout();
    }
  /*  public function update()
    {
    	$locations = Mage::getModel('ustorelocator/location')->getCollection();
    	 
    	foreach ($locations as $location) {
		   	 $address = $location->getAddress();
		   	 $addressdis = $location->getAddressDisplay(); 
			 if($address!='' && $address!='#')   $prepAddr = str_replace(' ','+',$address);
			 
			else if($addressdis!='' && $addressdis!='#')   $prepAddr = str_replace(' ','+',$addressdis);
			 
			$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
			 
			$output= json_decode($geocode);
			 
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;
			if($location->getLatitude()==0){
				$location->setLatitude($lat);
				
			}
			if($location->getLongitude()==0)
			{ 
				$location->setLongitude($long);
			}
			$location->save(); 
			$abc .= '  -- '. $location->getId();
  		}
  		return $abc;
    }*/
}
