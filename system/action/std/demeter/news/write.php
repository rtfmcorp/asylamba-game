<?php
# créer une news
# content
# title

include_once DEMETER;

$content 	= Utils::getHTTPData('content');
$title  	= Utils::getHTTPData('title');

if ($title !== FALSE AND $content !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') >= 3) {
		$S_FNM_1 = ASM::$fnm->getCurrentSession();
		ASM::$fnm->newSession();

		$news = new FactionNews();

		$news->rFaction = CTR::$data->get('playerInfo')->get('color');
		$news->title = $title;
		$news->edit($content);
		$news->dCreation = Utils::now();
		
		ASM::$fnm->add($news);
		ASM::$fnm->changeSession($S_FNM_1);
	} else {
		CTR::$alert->add('Vous n\'avez pas le droit pour créer une annonce.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}