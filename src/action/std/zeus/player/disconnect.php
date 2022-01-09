<?php

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionId = $session->get('session_id');
$session->destroy();
$this->getContainer()->get(\Asylamba\Classes\Daemon\ClientManager::class)->removeClient($sessionId);
$this->getContainer()->get('app.response')->redirect('profil', TRUE);
