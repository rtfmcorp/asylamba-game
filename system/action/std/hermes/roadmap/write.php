<?php
# write a message in the roadmap action

# string content 	contenu du message
# [int statement] 	état (0 = caché, 1 = affiché)

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hermes\Model\RoadMap;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$parser = $this->getContainer()->get('parser');
$roadmapManager = $this->getContainer()->get('hermes.roadmap_manager');

if ($session->get('playerInfo')->get('admin') == FALSE) {
	$response->redirect('profil');
} else {
	$content = $request->request->get('content');
	$statement = $request->query->get('statement', false);

	if (!empty($content)) { 

		$rm = new RoadMap();
		$rm->rPlayer = $session->get('playerId');
		$rm->setContent($content);
		$rm->setParsedContent($parser->parse($content));
		if ($statement !== false) {
			if ($statement == 0 OR $statement == 1) {
				$rm->statement = $statement;
			}
		} else {
			$rm->statement = RoadMap::DISPLAYED;
		}
		$rm->dCreation = Utils::now();
		$roadmapManager->add($rm);

		$session->addFlashbag('Roadmap publiée', Flashbag::TYPE_SUCCESS);
	} else {
		throw new FormException('pas assez d\'informations pour écrire un message dans la roadmap');
	}
}