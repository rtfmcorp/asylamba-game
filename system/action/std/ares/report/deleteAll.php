<?php

use Asylamba\Classes\Library\Flashbag;

$session = $this->getContainer()->get('app.session');

$this
	->getContainer()
	->get('ares.report_manager')
	->removePlayerReports(
		$session->get('playerId')
	)
;
$session->addFlashbag('Vos rapports ont été correctement supprimés', Flashbag::TYPE_SUCCESS);

$this->getContainer()->get('app.response')->redirect('fleet/view-archive');