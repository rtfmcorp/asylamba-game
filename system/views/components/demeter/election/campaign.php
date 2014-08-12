<?php
# require
$S_ELM_1 = ASM::$elm->getCurrentSession();
ASM::$elm->newSession();
ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

echo '<div class="component size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Campagne</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-postulate/relection-' . ASM::$elm->get(0)->id . '" method="post">';
				echo '<textarea name="program"></textarea>';
				echo '<input type="submit" value="se présenter" />';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$elm->changeSession($S_ELM_1);
?>