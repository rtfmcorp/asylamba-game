<?php
# créer une news
# content
# title

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Forum\FactionNews;

$factionNewsManager = $this->getContainer()->get('demeter.faction_news_manager');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

$content = $request->request->get('content');
$title = $request->request->get('title');

if ($title !== false and $content !== false) {
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
