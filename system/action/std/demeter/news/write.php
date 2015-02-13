<?php
# créer une news
# content
# title

include_once DEMETER;

$content = Utils::getHTTPData('content');
$title  = Utils::getHTTPData('title');


if ($title AND $content) {
	if (CTR::$data->get('playerInfo')->get('status') >= 3) {
		$news = new FactionNews();
		$news->rFaction = CTR::$data->get('playerInfo')->get('color');
		$news->title = $title;
		$news->dCreation = Utils::now();

		$news->edit($content);
		
		ASM::$fnm->add($news);

		CTR::redirect('faction/');
		CTR::$alert->add('news créé.', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Vous n\'avez pas le droit de créer de news.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_FILLFORM);
}