<?php
include_once ARES;

$S_COM_MAP_COM = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.rBase' => CTR::$data->get('playerParams')->get('base'), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));

echo '<div id="subnav">';
	echo '<div class="overflow">';
		for ($i = 0; $i < ASM::$com->size(); $i++) { 
			echo '<a href="/" class="item">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c6.png" alt="" />';
					echo '<span class="number">' . ASM::$com->get($i)->getLevel() . '</span>';
				echo '</span>';
				echo '<span class="content skin-2">';
					echo '<span class="sub-content">';
						echo 'Commandant ' . ASM::$com->get($i)->getName() . ' | ' . ASM::$com->get($i)->getPev() . ' pev';
						echo '<hr />';
						echo 'A quai sur Frudulu';
						echo '<hr />';

						for ($j = 0; $j < 12; $j++) { 
							echo '<span class="ship">';
								echo '<img src="' . MEDIA . 'ship/picto/lightFighter.png" />';
								echo '<span class="number">10</span>';
							echo '</span>';
						}
					echo '</span>';
				echo '</span>';
			echo '</a>';
		}
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM_MAP_COM);
?>