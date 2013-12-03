<?php
class ManagerSession {
	private $id;
	private $type;
	private $uMode;

	public function __construct($id, $type, $uMode) {
		$this->id = $id;
		$this->type = $type;
		$this->uMode = $uMode;
	}

	public function getId()		{ return $this->id; }
	public function getType()	{ return $this->type; }
	public function getUMode()	{ return $this->uMode; }

	public function toString() 	{ return 'id = ' . $this->id . ', type = ' . $this->type . 
									', uMode = ' . $this->uMode . ' ';}
}
?>