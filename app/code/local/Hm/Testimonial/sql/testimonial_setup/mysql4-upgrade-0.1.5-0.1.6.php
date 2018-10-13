<?php
$this->startSetup()
->run("ALTER TABLE {$this->getTable('testimonial')}
    ADD COLUMN sort_order int(11) NOT NULL default '0'")
->endSetup();