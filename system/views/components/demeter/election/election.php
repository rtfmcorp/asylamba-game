<?php
# require
$S_ELM_1 = ASM::$elm->getCurrentSession();
ASM::$elm->newSession();
ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

$S_CAM_1 = ASM::$cam->getCurrentSession();
ASM::$cam->newSession();
ASM::$cam->load(array('rElection' => ASM::$elm->get(0)->id));

echo '<div class="component size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Election</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$cam->size(); $i++) { 
				echo '<p><a href="' . APP_ROOT . 'action/a-vote/relection-' . ASM::$elm->get(0)->id . '/rcandidate-' . ASM::$cam->get($i)->rPlayer . '">voter pour ' . ASM::$cam->get($i)->rPlayer . '</a></p>';
				echo '<p>' . ASM::$cam->get($i)->program . '</p>';
				echo '<hr />';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$cam->changeSession($S_CAM_1);

ASM::$elm->changeSession($S_ELM_1);
?>