<?php
$player = CTR::$get->exist('player')
	? CTR::$get->get('player')
	: CTR::$data->get('playerId');
CTR::redirect('embassy/player-' . $player);