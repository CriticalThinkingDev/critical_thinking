<?php

/**
 * @category   Dh
 * @package    Dh_ShipNote
 * @copyright  Copyright (c) 2013 Drew Hunter (http://drewhunter.net)
 */

/**
 * @var Dh_ShipNote_Model_Resource_Setup
 */
$installer = $this;
 
 
$installer->addAttribute('order', 'ship_note', array(
		'type'               => 'text',
        'label'              => 'Ship Note',
        'input'              => 'textarea',
        'required'           => false,
        'sort_order'         => 120,
        'visible'            => false,
        'system'             => false,
        'position'           => 100,
        'user_defined'      => true, 
));  
$installer->endSetup();

 