<?php

use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('session_wrapper');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

$factions = $request->request->has('factions') ? $request->request->get('factions') : Params::$params[Params::CR_FACTIONS];
$min = $request->request->has('min') ? abs(intval($request->request->get('min'))) : Params::$params[Params::CR_MIN];
$max = $request->request->has('max') ? abs(intval($request->request->get('max'))) : Params::$params[Params::CR_MAX];

$request->cookies->add('p' . Params::CR_FACTIONS, json_encode($factions), true);
$request->cookies->add('p' . Params::CR_MIN, $min, true);
$request->cookies->add('p' . Params::CR_MAX, $max, true);

$response->headers->add('Content-Type', 'application/json');
echo(json_encode($commercialRouteManager->searchRoutes(
    $orbitalBaseManager->get($session->get('playerParams')->get('base')),
    $session->get('playerId'),
    $session->get('playerInfo')->get('color'),
    $factions,
    $min,
    $max
)));