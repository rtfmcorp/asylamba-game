<?php

use Asylamba\Classes\Worker\ASM;

$S_PLM_1 = ASM::$sys->getCurrentSession();
ASM::$sys->newSession(FALSE);
ASM::$sys->load([], [], []);

# functions
function getPosition($type, $x, $y, $multiply = 4) {
	$_return  = 'style="';
	switch($type) {
		case 1 : $_return .= 'top: ' . ceil($y * $multiply) . 'px; left: ' . ($x * $multiply) . 'px;';
			break;
		case 2 : $_return .= 'top: ' . ceil($y * $multiply) . 'px; left: ' . ($x * $multiply) . 'px;';
			break;
		case 3 : $_return .= 'top: ' . ceil(($y * $multiply) - 1) . 'px; left: ' . (($x * $multiply) - 1) . 'px;';
			break;
		case 4 : $_return .= 'top: ' . ceil(($y * $multiply) - 1) . 'px; left: ' . (($x * $multiply) - 1) . 'px;';
			break;
		case 5 : $_return .= 'top: ' . ceil($y * $multiply) . 'px; left: ' . ($x * $multiply) . 'px;';
			break;
	}
	$_return .= '"';
	return $_return;
}

?>
<style type="text/css">
#lieux {
	width: 1000px;
	height: 1000px;
	position: relative;
	background: rgba(0, 0, 0, 1);
}

#lieux .lieu {
	position: absolute;
	border-radius: 50px;
	background: rgba(255, 255, 255, 1);
}

#lieux .l1 {
	width: 1px;
	height: 1px;
	background: rgba(255, 255, 255, 1);
}

#lieux .l2 {
	width: 1px;
	height: 1px;
	box-shadow: 0 0 50px 16px rgba(255, 255, 255, 0.05);
}

#lieux .l3 {
	width: 3px;
	height: 3px;
	box-shadow: 0 0 5px 3px rgba(255, 255, 255, 0.2);
}

#lieux .l4 {
	width: 2px;
	height: 2px;
	box-shadow: 0 0 5px 3px rgba(255, 255, 255, 0.1);
}

#lieux .l5 {
	width: 1px;
	height: 1px;
	box-shadow: 0 0 5px 3px rgba(255, 255, 255, 0.05);
}
</style>

<?php
echo '<div id="lieux">';
	for ($i = 0; $i < ASM::$sys->size(); $i++) {
		$system = ASM::$sys->get($i);

		switch($system->typeOfSystem) {
			case 1: echo '<div class="lieu l1" ' . getPosition($system->typeOfSystem, $system->xPosition, $system->yPosition) . '></div>'; break;
			case 2: echo '<div class="lieu l2" ' . getPosition($system->typeOfSystem, $system->xPosition, $system->yPosition) . '></div>'; break;
			case 3: echo '<div class="lieu l3" ' . getPosition($system->typeOfSystem, $system->xPosition, $system->yPosition) . '></div>'; break;
			case 4: echo '<div class="lieu l4" ' . getPosition($system->typeOfSystem, $system->xPosition, $system->yPosition) . '></div>'; break;
			case 5: echo '<div class="lieu l5" ' . getPosition($system->typeOfSystem, $system->xPosition, $system->yPosition) . '></div>'; break;
			default:
					break;
		}
	}
echo '</div>';
