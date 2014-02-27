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
				echo '<span class="content skin-1">';
					echo '<span>' . ASM::$com->get($i)->getName() . '</span>';
				echo '</span>';
			echo '</a>';
		}
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM_MAP_COM);
?>