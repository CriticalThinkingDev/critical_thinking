<?php  

class Siteguarding_Antivirus_Block_Adminhtml_Antivirusbackend extends Mage_Adminhtml_Block_Template {
	
	public function getRootPath(){
		
		$rootPath = substr(__DIR__,0,strpos(__DIR__,"/app"));
		return $rootPath;
		
	}
	
	public function getWebsiteUrl(){
		
		$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . "/";
	}
	
	public function getUrlToModule(){
		date_default_timezone_set('Europe/London');
		$file = 'Mg_Scanner.php';
		$url = $this->getWebsiteUrl() . $file . "?session=" . md5($this->getRootPath() . date("Y-m-d"));
		return $url;
	}

}