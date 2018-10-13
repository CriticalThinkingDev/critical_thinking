<?php

$this->startSetup()
    ->run("ALTER TABLE {$this->getTable('testimonial')}
    ADD COLUMN `is_homepage` int(11) unsigned NOT NULL ")
    ->endSetup();