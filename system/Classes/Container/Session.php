<?php

namespace Asylamba\Classes\Container;

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;

class Session extends ArrayList {
	/** @var array **/
	public $flashbags = [];
	
	public function __construct()
	{
		if (isset($_SESSION['flashbags'])) {
			$this->flashbags = unserialize($_SESSION['flashbags']);
		}
	}
	
	public function __destruct()
	{
		if (!empty($this->flashbags)) {
			$_SESSION['flashbags'] = serialize($this->flashbags);
		}
	}
	
	/**
	 * @param string $message
	 * @param string $type
	 */ 
	public function addFlashbag($message, $type)
	{
		$this->flashbags[] = new Flashbag($message, $type);
	}
	
	/**
	 * @return array
	 */
	public function getFlashbags()
	{
		return $this->flashbags;
	}
	
	public function flushFlashbags()
	{
		$this->flashbags = [];
		unset($_SESSION['flashbags']);
	}
	
    public function destroy() {
		$this->elements = NULL;
		unset($_SESSION['flashbags']);
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
    /**
     * @param string $key
     * @param int $id
     * @param string $name
     * @param string $sector
     * @param string $system
     * @param string $img
     * @param string $type
     * @return boolean
     */
    public function addBase($key, $id, $name, $sector, $system, $img, $type) {
        if ($this->exist('playerBase')) {
            if ($key != 'ob' && $key != 'ms') {
                return false;
            }
            $a = new ArrayList();

            $a->add('id', $id);
            $a->add('name', $name);
            $a->add('sector', $sector);
            $a->add('system', $system);
            $a->add('img', $img);
            $a->add('type', $type);

            $this->get('playerBase')->get($key)->append($a);
        }
    }

    /**
     * @param string $key
     * @param int $id
     */
    public function removeBase($key, $id) {
        if ($this->exist('playerBase')) {
            $size = $this->get('playerBase')->get($key)->size();
            for ($i = 0; $i < $size; $i++) {
                if ($this->get('playerBase')->get($key)->get($i)->get('id') == $id) {
                    $this->get('playerBase')->get($key)->remove($i);
                }
            }
        }
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function baseExist($id) {
        $obSize = $this->get('playerBase')->get('ob')->size();
        for ($i = 0; $i < $obSize; $i++) {
            if ($id == $this->get('playerBase')->get('ob')->get($i)->get('id')) {
                return TRUE;
            }
        }
        $msSize = $this->get('playerBase')->get('ms')->size();
        for ($i = 0; $i < $msSize; $i++) {
            if ($id == $this->get('playerBase')->get('ms')->get($i)->get('id')) {
                return TRUE;
            }
        }
        return FALSE;
    }
}