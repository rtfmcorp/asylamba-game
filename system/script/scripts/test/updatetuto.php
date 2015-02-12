<?php
include_once ZEUS;
echo '<h1>Update tuto</h1>';
echo '<p>Insert step 17 and shift players who are further.</p>';

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);
ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE)));

echo '<p>Loading of ' . ASM::$pam->size() . ' players.</p>';

$updateQty = 0;
for ($i = 0; $i < ASM::$pam->size(); $i++) { 
	$player = ASM::$pam->get($i);

	if ($player->stepTutorial >= 17) {
		$player->stepTutorial++;
		$updateQty++;
	} 
	// else do nothing
}

echo '<p>Nombre de joueurs mis à jour : ' . $updateQty . '.</p>';

ASM::$pam->changeSession($S_PAM1);
?>