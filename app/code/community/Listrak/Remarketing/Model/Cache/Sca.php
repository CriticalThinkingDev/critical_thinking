<?php
/**
 * Listrak Remarketing Magento Extension Ver. 1.1.9
 *
 * PHP version 5
 *
 * @category  Listrak
 * @package   Listrak_Remarketing
 * @author    Listrak Magento Team <magento@listrak.com>
 * @copyright 2014 Listrak Inc
 * @license   http://s1.listrakbi.com/licenses/magento.txt License For Customer Use of Listrak Software
 * @link      http://www.listrak.com
 */

class Listrak_Remarketing_Model_Cache_Sca extends Enterprise_PageCache_Model_Container_Abstract
{
    private $_cartCookieValue = null;
    private $_saveBlock = false;

    protected function _getCacheId()
    {
        if (!$this->_cartCookieValue)
            $this->_cartCookieValue = $this->_getCookieValue('mltkc');

        if (!$this->_cartCookieValue)
            $this->_cartCookieValue = 'AJAX';

        return md5('REMARKETING_SCA_' . $this->_cartCookieValue);
    }

    protected function _renderBlock()
    {
        $block = $this->_placeholder->getAttribute('block');
        $block = new $block;
        $block->setFullPageRendering(true);

        if ($block->canRender()) {
            $block->setTemplate($this->_placeholder->getAttribute('template'));

            if (Mage::helper('remarketing')->ajaxTracking()) {
                $this->_cartCookieValue = 'AJAX';
                $this->_saveBlock = true;
            }

            return $block->toHtml();
        }
        else {
            $this->_saveBlock = true;
            if (!$this->_cartCookieValue)
                $this->_cartCookieValue = Mage::helper('remarketing')->initCartCookie();

            return "";
        }
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        return $this->_saveBlock ? parent::_saveCache($data, $id, $tags, $lifetime) : false;
    }
}
