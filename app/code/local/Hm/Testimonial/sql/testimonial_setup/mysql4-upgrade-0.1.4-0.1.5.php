<?php

$this->startSetup()
->run("ALTER TABLE {$this->getTable('testimonial')}
    ADD COLUMN is_teacher smallint(6) NOT NULL default '2'")
->endSetup();