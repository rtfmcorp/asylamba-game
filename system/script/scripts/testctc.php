<?php
class A {
	public $u = '2014-01-29 12:00:00';
	public $credit = 5000;

	public function uMethod() {
		$token = CTC::createContext();

		$interval = Utils::interval($this->u, Utils::now());

		for ($i = 0; $i < $interval; $i++) { 
			CTC::add(Utils::nextOClock($this->u, $i + 1), $this, 'addCredit', 1000);
		}
		# deuxième truc
		$b = new B();
		$b->uMethod();

		CTC::applyContext($token);
	}

	public function addCredit($credit) {
		$this->credit += $credit;
	}
}

class B {
	public $u = '2014-01-29 18:00:00';
	public $point = 5000;

	public function uMethod() {
		$token = CTC::createContext();

		$interval = Utils::interval($this->u, Utils::now());

		for ($i = 0; $i < $interval; $i++) { 
			CTC::add(Utils::nextOClock($this->u, $i + 1), $this, 'addPoint', 1000);
		}

		CTC::applyContext($token);
	}

	public function addPoint($point) {
		$this->point += $point;
	}
}

$a = new A;
$a->uMethod();

var_dump($a);
?>