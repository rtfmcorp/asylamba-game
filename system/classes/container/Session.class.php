<?php
class Session extends ArrayList {
	public function destroy() {
		$this->elements = NULL;
	}

	public function clear() {
		$this->remove('playerInfo', new ArrayList());
		$this->remove('playerBase', new ArrayList());
		$this->remove('playerEvent', new ArrayList());
	}

	##

	public function initPlayerInfo() {
		$this->add('playerInfo', new ArrayList());
	}

	public function initPlayerBase() {
		$a = new ArrayList();
		$a->add('ob', new StackList());
		$a->add('ms', new StackList());

		$this->add('playerBase', $a);
	}

	public function initPlayerEvent() {
		$this->add('playerEvent', new EventList());
	}

	public function initLastUpdate() {
		$l = new ArrayList();
		$l->add('game',  Utils::now());
		$l->add('event', Utils::now());

		$this->add('lastUpdate', $l);
	}

	public function initPlayerBonus() {
		$this->add('playerBonus', new StackList());
	}

	##

	public function addBase($key, $id, $name, $sector, $system, $img, $type) {
		if ($this->exist('playerBase')) {
			if ($key == 'ob' || $key == 'ms') {
				$a = new ArrayList();
				
				$a->add('id', $id);
				$a->add('name', $name);
				$a->add('sector', $sector);
				$a->add('system', $system);
				$a->add('img', $img);
				$a->add('type', $type);

				$this->get('playerBase')->get($key)->append($a);
			} else {
				return FALSE;
			}
		}
	}

	public function removeBase($key, $id) {
		if ($this->exist('playerBase')) {
			for ($i = 0; $i < $this->get('playerBase')->get($key)->size(); $i++) {
				if ($this->get('playerBase')->get($key)->get($i)->get('id') == $id) {
					$this->get('playerBase')->get($key)->remove($i);
				}
			}
		}
	}

	public function baseExist($id) {
		for ($i = 0; $i < $this->get('playerBase')->get('ob')->size(); $i++) {
			if ($id == $this->get('playerBase')->get('ob')->get($i)->get('id')) {
				return TRUE;
			}
		}
		for ($i = 0; $i < $this->get('playerBase')->get('ms')->size(); $i++) {
			if ($id == $this->get('playerBase')->get('ms')->get($i)->get('id')) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>