<?php

use App\Classes\Library\Flashbag;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$this
	->getContainer()
	->get(\Asylamba\Modules\Ares\Manager\ReportManager::class)
	->removePlayerReports(
		$session->get('playerId')
	)
;
$session->addFlashbag('Vos rapports ont été correctement supprimés', Flashbag::TYPE_SUCCESS);

$this->getContainer()->get('app.response')->redirect('fleet/view-archive');
