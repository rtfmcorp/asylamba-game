<?php
include_once ZEUS;
# validate tutorial step action

$playerId = CTR::$data->get('playerId');
$stepTutorial = CTR::$data->get('playerInfo')->get('stepTutorial');
$stepDone = CTR::$data->get('playerInfo')->get('stepDone');

if ($stepDone == TRUE AND TutorialResource::stepExists($stepTutorial)) {

	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('id' => $playerId));
	$player = ASM::$pam->get();

	$experience = TutorialResource::getInfo($stepTutorial, 'experienceReward');
	$player->increaseExperience($experience);
	CTR::$alert->add('Etape validée, vous gagnez ' . $experience . ' points d\'expérience. La prochaine étape vous attend.', ALERT_STD_SUCCESS);
	
	$nextStep = $stepTutorial;
	if (TutorialResource::isLastStep($stepTutorial)) {
		$nextStep = -1;
		CTR::$alert->add('Bravo, vous avez terminé le tutoriel. Bonne continuation et bon amusement sur Asylamba, vous pouvez maintenant voler de vos propres ailes !', ALERT_STD_SUCCESS);
	} else {
		$nextStep += 1;
	}

	$player->stepTutorial = $nextStep;
	CTR::$data->get('playerInfo')->add('stepTutorial', $nextStep);
	$player->stepDone = FALSE;
	CTR::$data->get('playerInfo')->add('stepDone', FALSE);

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('Impossible de valider l\'étape avant de l\'avoir effectuée.', ALERT_STD_FILLFORM);
}
?>