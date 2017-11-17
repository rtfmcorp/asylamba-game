<?php

namespace Asylamba\Classes\Library\Session;

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;

use Asylamba\Classes\Container\ArrayList;
use Asylamba\Classes\Container\StackList;
use Asylamba\Classes\Container\EventList;

class Session
{
    /** @var array **/
    public $flashbags = [];
    /** @var array **/
    protected $items = [];
    /** @var array **/
    protected $history = [];
    
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
    }
    
    /**
     * @param string $key
     * @param string $value
     */
    public function add($key, $value)
    {
        $this->items[$key] = $value;
    }
    
    /**
     * @param string $key
     * @return boolean
     */
    public function exist($key)
    {
        return isset($this->items[$key]);
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->exist($key) ? $this->items[$key] : null;
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
    
    /**
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
        return false;
    }
    
    public function destroy()
    {
        $this->items = [];
        $this->flashbags = [];
        $this->history = [];
    }

    public function clear()
    {
        $this->remove('playerInfo');
        $this->remove('playerBase');
        $this->remove('playerEvent');
    }

    ##

    public function initPlayerInfo()
    {
        $this->add('playerInfo', new ArrayList());
    }

    public function initPlayerBase()
    {
        $a = new ArrayList();
        $a->add('ob', new StackList());
        $a->add('ms', new StackList());

        $this->add('playerBase', $a);
    }

    public function initPlayerEvent()
    {
        $this->add('playerEvent', new EventList());
    }

    public function initLastUpdate()
    {
        $l = new ArrayList();
        $l->add('game', Utils::now());
        $l->add('event', Utils::now());

        $this->add('lastUpdate', $l);
    }

    public function initPlayerBonus()
    {
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
    public function addBase($key, $id, $name, $sector, $system, $img, $type)
    {
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
    public function removeBase($key, $id)
    {
        if ($this->exist('playerBase')) {
            $size = $this->get('playerBase')->get($key)->size();
            for ($i = 0; $i < $size; $i++) {
                if ($this->get('playerBase')->get($key)->get($i)->get('id') == $id) {
                    $this->get('playerBase')->get($key)->remove($i);
                    return;
                }
            }
        }
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function baseExist($id)
    {
        $obSize = $this->get('playerBase')->get('ob')->size();
        for ($i = 0; $i < $obSize; $i++) {
            if ($id == $this->get('playerBase')->get('ob')->get($i)->get('id')) {
                return true;
            }
        }
        $msSize = $this->get('playerBase')->get('ms')->size();
        for ($i = 0; $i < $msSize; $i++) {
            if ($id == $this->get('playerBase')->get('ms')->get($i)->get('id')) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param string $path
     */
    public function addHistory($path)
    {
        $this->history[] = $path;
    }
    
    /**
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }
    
    /**
     * @return string
     */
    public function getLastHistory()
    {
        return $this->history[count($this->history) - 1];
    }
    
    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }
    
    public function setData($data)
    {
        $this->history = $data['history'];
        $this->flashbags = $data['flashbags'];
        $this->items = $data['items'];
    }
    
    public function getData()
    {
        return [
            'history' => $this->history,
            'flashbags' => $this->flashbags,
            'items' => $this->items
        ];
    }
}
