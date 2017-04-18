<?php

use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('session_wrapper');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');

$S_LRM = $littleReportManager->getCurrentSession();
$littleReportManager->newSession();
$littleReportManager->load(array('rPlayerAttacker' => $session->get('playerId'), 'statementAttacker' => Report::STANDARD));
$littleReportManager->load(array('rPlayerDefender' => $session->get('playerId'), 'statementDefender' => Report::STANDARD));

if ($littleReportManager->size() > 0) {
	for ($i = 0; $i < $littleReportManager->size(); $i++) {
		if ($littleReportManager->get($i)->rPlayerAttacker == $session->get('playerId')) {
			$littleReportManager->get($i)->statementAttacker = Report::DELETED;
		} elseif ($littleReportManager->get($i)->rPlayerDefender == $session->get('playerId')) {
			$littleReportManager->get($i)->statementDefender = Report::DELETED;
		} else {
			throw new ErrorException('Ces rapport ne vous appartient pas');
		}
	}
}

$littleReportManager->changeSession($S_LRM);