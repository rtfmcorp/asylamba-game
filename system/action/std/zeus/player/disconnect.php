<?php

$session = $this->getContainer()->get('app.session');
$sessionId = $session->get('session_id');
$session->destroy();
$this->getContainer()->get('client_manager')->removeClient($sessionId);
$this->getContainer()->get('app.response')->redirect('profil', TRUE);