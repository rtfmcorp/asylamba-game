<?php
include_once ARES;

$S_COM_MAP_COM = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.rBase' => CTR::$data->get('playerParams')->get('base'), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));

echo '<div id="subnav">';
	echo '<div class="overflow">';
		for ($i = 0; $i < ASM::$com->size(); $i++) {
			$commander = ASM::$com->get($i);

			echo '<a href="/" class="item">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c6.png" alt="" />';
					echo '<span class="number">' . $commander->getLevel() . '</span>';
				echo '</span>';
				echo '<span class="content skin-2">';
					echo '<span class="sub-content">';
						echo CommanderResources::getInfo($commander->getLevel(), 'grade') . ' ' . $commander->getName() . '<br />';
						echo Format::numberFormat($commander->getPev()) . ' pev';
						echo '<hr />';
						echo 'A quai sur Frudulu';
						echo '<hr />';

						foreach ($commander->getNbrShipByType() as $k => $nbr) {
							echo '<span class="ship">';
								echo '<img src="' . MEDIA . 'ship/picto/ship' . $k . '.png" ' . ($nbr == 0 ? 'class="zero"' : NULL) . '/>';
								echo '<span class="number">' . $nbr . '</span>';
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