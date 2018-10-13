<?php
/**
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @copyright   Copyright (c) 2009 PILLWAX Industrial Solutions Consulting
 * @license		Commercial Unlimited License (https://technology.pillwax.com/license)
 */

/**
 * Archive controller
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author
 * @version		0.1.1
 */

require_once Mage::getModuleDir('controllers', 'Mage_Downloadable').DS.'DownloadController.php';

class Pisc_Downloadplus_ArchiveController extends Mage_Downloadable_DownloadController
{

    /*
     * Show File Archive for Purchased Link
     */
    public function purchasedAction()
    {
          // Display the Archive Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'downloadable_archive_purchased'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
    }

    /*
     * Show File Archive for Purchased Link Sample
     */
    public function linkSampleAction()
    {
          // Display the Archive Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'downloadable_archive_linksample'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
    }

    /*
     * Show File Archive for Sample
     */
    public function samplesAction()
    {
          // Display the Archive Page
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');

          // Adds action handle 'downloadable_archive_samples'
          $this->addActionLayoutHandles();

          $this->loadLayoutUpdates();
          $this->generateLayoutXml()->generateLayoutBlocks();
          $this->renderLayout();
    }

}
