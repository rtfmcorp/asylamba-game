<?php
include_once HERMES;
# write a message in the roadmap action

# string content 	contenu du message
# [int statement] 	état (0 = caché, 1 = affiché)

if (CTR::$data->get('playerInfo')->get('admin') == FALSE) {
	CTR::redirect('profil');
} else {
	if (CTR::$get->exist('content')) {
		$content = CTR::$get->get('content');
	} elseif (CTR::$post->exist('content')) {
		$content = CTR::$post->get('content');
	} else {
		$content = FALSE;
	}
	if (CTR::$get->exist('statement')) {
		$statement = CTR::$get->get('statement');
	} elseif (CTR::$post->exist('statement')) {
		$statement = CTR::$post->get('statement');
	} else {
		$statement = FALSE;
	}

	if ($content !== FALSE AND $content !== '') { 

		$rm = new RoadMap();
		$rm->rPlayer = CTR::$data->get('playerId');
		$rm->setContent($content);
		if ($statement !== FALSE) {
			if ($statement == 0 OR $statement == 1) {
				$rm->statement = $statement;
			}
		} else {
			$rm->statement = RoadMap::DISPLAYED;
		}
		$rm->dCreation = Utils::now();
		ASM::$rmm->add($rm);

		CTR::$alert->add('Roadmap publiée', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('pas assez d\'informations pour écrire un message dans la roadmap', ALERT_STD_FILLFORM);
	}
}
?>