<?php
/**
 * Downloadable Admin Downloads block
 *
 * @category    Pillwax
 * @package     Pillwax_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 */

class Pisc_Downloadplus_Block_Adminhtml_Dashboard_Container extends Mage_Adminhtml_Block_Widget_Container
{
	/*
	 * Constructor
	 */
	  public function __construct()
	  {
	      parent::__construct();
	      $this->setId('downloadplus_dashboardContainer');
	      $this->setSaveParametersInSession(true);
	  }

}