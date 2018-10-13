<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AdvancedReviews
 * @version    2.2.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

/**
 * Overrides AW_Helpdeskultimate_Block_Adminhtml_Tickets_Edit_Tab_Thread for
 * insert parser.phtml to Thread Block.
 * Parser Block will convert "Review Id #id" to link for pleasure approve
 * reviews.
 */
class AW_AdvancedReviews_Block_Adminhtml_Tickets_Thread extends AW_Helpdeskultimate_Block_Adminhtml_Tickets_Edit_Tab_Thread
{
    /**
     * Injects parser HTML into output stream
     * @return String
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $parser = $this->getLayout()
                    ->createBlock('advancedreviews/adminhtml_tickets_thread_parser')
                    ->setTemplate('advancedreviews/adminhtml/tickets/threadparser.phtml')
                    ->toHtml();
        return $html.$parser;
    }
}
