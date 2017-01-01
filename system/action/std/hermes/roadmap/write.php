<?php
# write a message in the roadmap action

# string content 	contenu du message
# [int statement] 	état (0 = caché, 1 = affiché)

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\RoadMap;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$parser = $this->getContainer()->get('parser');
$roadmapManager = $this->getContainer()->get('hermes.roadmap_manager');

if ($session->get('playerInfo')->get('admin') == FALSE) {
	$response->redirect('profil');
} else {
	$content = $request->request->get('content');
	$statement = $request->query->get('statement');

	if ($content !== FALSE AND $content !== '') { 

		$rm = new RoadMap();
		$rm->rPlayer = $session->get('playerId');
		$rm->setContent($content);
		$rm->setParsedContent($parser->parse($content));
		if ($statement !== FALSE) {
			if ($statement == 0 OR $statement == 1) {
				$rm->statement = $statement;
			}
		} else {
			$rm->statement = RoadMap::DISPLAYED;
		}
		$rm->dCreation = Utils::now();
		$roadmapManager->add($rm);

		$response->flashbag->add('Roadmap publiée', Response::FLASHBAG_SUCCESS);
	} else {
		throw new FormException('pas assez d\'informations pour écrire un message dans la roadmap');
	}
}