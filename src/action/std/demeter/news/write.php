<?php
# créer une news
# content
# title

use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Classes\Library\Utils;
use App\Modules\Demeter\Model\Forum\FactionNews;

$factionNewsManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager::class);
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$content = $request->request->get('content');
$title = $request->request->get('title');

if ($title !== FALSE AND $content !== FALSE) {
	if ($session->get('playerInfo')->get('status') >= 3) {
		$news = new FactionNews();
		$news->rFaction = $session->get('playerInfo')->get('color');
		$news->title = $title;
		$factionNewsManager->edit($news, $content);
		$news->dCreation = Utils::now();
		
		$factionNewsManager->add($news);
	} else {
		throw new ErrorException('Vous n\'avez pas le droit pour créer une annonce.');
	}
} else {
	throw new FormException('Manque d\'information.');
}
