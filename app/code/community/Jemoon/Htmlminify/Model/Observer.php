<?php

/**
 *  Jemoon_Htmlminify_Model_Observer
 *
 * @category   Jemoon
 * @package    Jemoon_Htmlminify
 * @author     Marcin Jędrzejewski <marcin.jedrzejewski85@gmail.com>
 * @copyright  Copyright (c) 2012 Marcin Jędrzejewski
 * @license    http://framework.zend.com/license   BSD License
 * @version    $id$
 * @since      0.1.0
 */
class Jemoon_Htmlminify_Model_Observer {

    public function Htmlminify($observer) {
               $controller = Mage::app()->getRequest()->getControllerName();
		$action = Mage::app()->getRequest()->getActionName();
		$router = Mage::app()->getRequest()->getRouteName();
		$route = $router.'/'.$controller.'/'.$action;
if($route=='catalogsearch/advanced/result' || $route=='catalog/product/view' || $route=='catalogsearch/result/index' || $route=='cms/index/index'){
        if (Mage::getStoreConfig('dev/html/minify_html_output')) {
            // Fetches the current event
            $event = $observer->getEvent();
            $controller = $event->getControllerAction();
            $allHtml = $controller->getResponse()->getBody();

            // Trim each line
            $allHtml = preg_replace('/^\\s+|\\s+$/m', '', $allHtml);

            // Remove HTML comments
            $allHtml = preg_replace_callback(
                    '/<!--([\\s\\S]*?)-->/', array($this, '_commentCB'), $allHtml);

            // Remove ws around block/undisplayed elements
            $allHtml = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
                    . '|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
                    . '|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
                    . '|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
                    . '|ul)\\b[^>]*>)/i', '$1', $allHtml);

            // Remove ws outside of all elements
            $allHtml = preg_replace_callback(
                    '/>([^<]+)</', array($this, '_outsideTagCB'), $allHtml);
            $controller->getResponse()->setBody($allHtml);
        }
}
    }

    protected function _outsideTagCB($m) {
        return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
    }

    protected function _commentCB($m) {
        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0] : '';
    }

}
