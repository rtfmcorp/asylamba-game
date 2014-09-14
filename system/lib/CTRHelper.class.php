<?php
class CTRHelper {
	public static function initializePlayerInfo() {
		$a = new ArrayList();
		CTR::$data->add('playerInfo', $a);
	}

	public static function initializePlayerBase() {
		$a = new ArrayList();
		$sl1 = new StackList();
		$sl2 = new StackList();
		$a->add('ob', $sl1);
		$a->add('ms', $sl2);
		CTR::$data->add('playerBase', $a);
	}

	public static function initializePlayerEvent() {
		$e = new EventList();
		CTR::$data->add('playerEvent', $e);
	}

	public static function initializeLastUpdate() {
		$l = new ArrayList();
		$l->add('game', Utils::now());
		$l->add('event', Utils::now());
		CTR::$data->add('lastUpdate', $l);
	}

	public static function initializePlayerBonus() {
		$b = new StackList();
		CTR::$data->add('playerBonus', $b);
	}

	public static function addBase($key, $id, $name, $sector, $system, $img, $type) {
		if (CTR::$data->exist('playerBase')) {
			if ($key == 'ob' || $key == 'ms') {
				$a = new ArrayList();
				
				$a->add('id', $id);
				$a->add('name', $name);
				$a->add('sector', $sector);
				$a->add('system', $system);
				$a->add('img', $img);
				$a->add('type', $type);

				CTR::$data->get('playerBase')->get($key)->append($a);
			} else {
				return FALSE;
			}
		}
	}

	public static function removeBase($key, $id) {
		if (CTR::$data->exist('playerBase')) {
			for ($i = 0; $i < CTR::$data->get('playerBase')->get($key)->size(); $i++) {
				if (CTR::$data->get('playerBase')->get($key)->get($i)->get('id') == $id) {
					CTR::$data->get('playerBase')->get($key)->remove($i);
				}
			}
		}
	}

	public static function baseExist($id) {
		for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
			if ($id == CTR::$data->get('playerBase')->get('ob')->get($i)->get('id')) {
				return TRUE;
			}
		}
		for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) {
			if ($id == CTR::$data->get('playerBase')->get('ms')->get($i)->get('id')) {
				return TRUE;
			}
		}
		return FALSE;
	}
}