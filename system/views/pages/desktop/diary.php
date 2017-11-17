<?php

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');

$player = $request->query->has('player')
    ? $request->query->get('player')
    : $session->get('playerId');
$this->getContainer()->get('app.response')->redirect('embassy/player-' . $player);
