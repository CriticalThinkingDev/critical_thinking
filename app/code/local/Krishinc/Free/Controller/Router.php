<?php

class Krishinc_Free_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
	
	public function match(Zend_Controller_Request_Http $request)
	{ 
			$path = explode('/', trim($request->getPathInfo(), '/')); 
		    // If path doesn't match your module requirements
		    if (count($path) > 2 && $path[0] != 'free' && $path[0] != 'download'&& $path[0] != 'donations-download') {
		        return false; 
		    }
		   
		    // Define initial values for controller initialization
		    $module = $path[0];
		    $realModule = 'Krishinc_Free';
		    $controller = 'index';
		    $action = 'action';
		    $controllerClassName = $this->_validateControllerClassName(
		        $realModule, 
		        $controller
		    );            
		    // If controller was not found
		    if (!$controllerClassName) {
		        return false; 
		    }            
		    // Instantiate controller class
		    $controllerInstance = Mage::getControllerInstance(
		        $controllerClassName, 
		        $request, 
		        $this->getFront()->getResponse()
		    );
		    // If action is not found
		    if (!$controllerInstance->hasAction($action)) { 
		        return false; // 
		    }            
		    // Set request data
		    $request->setModuleName($module);
		    $request->setControllerName($controller);
		    $request->setActionName($action);
		    $request->setControllerModule($realModule);            
		    // Set your custom request parameter
		    $request->setParam('url_path', $path[1]);
		    // dispatch action
		    $request->setDispatched(true);
		    $controllerInstance->dispatch($action);
		    // Indicate that our route was dispatched
		    return true;
		}
}