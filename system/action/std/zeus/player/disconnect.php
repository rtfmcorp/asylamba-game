<?php

$session = $this->getContainer()->get('app.session');
$this->getContainer()->get('client_manager')->removeClient($session->get('session_id'));
$session->destroy();
$this->getContainer()->get('app.response')->redirect('profil', TRUE);