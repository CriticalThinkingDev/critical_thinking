<?php

class Krishinc_Softwaredemos_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function get_file_size($file, $type)
	{
			switch($type){  
			case "KB":  
				$filesize = filesize($file) * .0009765625; // bytes to KB  
			break;  
			case "MB":  
				$filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB  
			break;  
			case "GB":  
				$filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB  
			break;  
		}  
		if($filesize <= 0){  
			return $filesize = 'unknown file size';}  
		else{return round($filesize, 2).' '.$type;}  
	}

	public function isDownloadableDemo($product) {
		$constant_helper = Mage::helper('grouped/constants'); 
		if($product->getIsSoftwareDemos() && $product->getSoftwareDemoFlag() == $constant_helper::SOFTWARE_DEMO_FLAG_DOWNLOAD) {
			if($product->getWindowDownload() != "" || $product->getMacDownload() != "") {
				return true;
			}
		}
		return false;
	}
	
	public function isPlayDemo($product) {
		$constant_helper = Mage::helper('grouped/constants'); 
		if($product->getIsSoftwareDemos() && $product->getSoftwareDemoFlag() == $constant_helper::SOFTWARE_DEMO_FLAG_ONLINE) {
			if($playdemoUrl = $product->getPlaydemoUrl()) {
				return $playdemoUrl;
			}
		}
		return false;
	}
}