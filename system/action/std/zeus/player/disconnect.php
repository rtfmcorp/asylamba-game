<?php

$this->getContainer()->get('app.session')->destroy();
$this->getContainer()->get('app.response')->redirect(GETOUT_ROOT . 'profil', TRUE);