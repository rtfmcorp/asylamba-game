<?php

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionId = $session->get('session_id');
$session->destroy();
$this->getContainer()->get(\App\Classes\Daemon\ClientManager::class)->removeClient($sessionId);
$this->getContainer()->get('app.response')->redirect('profil', TRUE);
