<?php

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

$response->headers->add('Content-Type', 'application/json');
echo(json_encode($commercialRouteManager->searchRoutes(
    $orbitalBaseManager->get($session->get('playerParams')->get('base')),
    $session->get('playerId'),
    $request->request->has('factions') ? $request->request->get('factions') : [],
    $request->request->has('min') ? abs(intval($request->request->get('min'))) : 75,
    $request->request->has('max') ? abs(intval($request->request->get('max'))) : 125
)));