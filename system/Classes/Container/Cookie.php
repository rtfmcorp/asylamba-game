<?php
/**
 * SUJET A MODIF, ALORS ATTENTION A VOUS
 **/
namespace Asylamba\Classes\Container;

class Cookie extends ArrayList {
    /** @var string **/
    protected $name;

    public function __construct() {
        $this->name = APP_NAME . '_' . SERVER_SESS;
        $this->init();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function add($key, $value) {
        $this->elements[$key] = $value;
        $this->rewrite();
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function remove($key) {
        if (!isset($this->elements[$key])) {
            return false;
        }
        unset($this->elements[$key]);
        $this->rewrite();
    }

    public function clear() {
        $this->elements = array();
        $this->rewrite();
    }

    public function rewrite() {
        setcookie($this->name, serialize($this->elements), time() + 3000000, '/');
    }

    public function init() {
        if (isset($_COOKIE[$this->name])) {
            $this->elements = unserialize($_COOKIE[$this->name]);
        }
    }
}