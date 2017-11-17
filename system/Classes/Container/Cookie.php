<?php
/**
 * SUJET A MODIF, ALORS ATTENTION A VOUS
 **/
namespace Asylamba\Classes\Container;

class Cookie extends ArrayList
{
    /** @var array **/
    protected $newElements = [];

    /**
     * @param string $key
     * @param mixed $value
     */
    public function add($key, $value, $new = false)
    {
        $this->elements[$key] = $value;
        
        if ($new === true) {
            $this->newElements[$key] = $value;
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->elements[$key])) {
            return $this->elements[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function remove($key)
    {
        if (!isset($this->elements[$key])) {
            return false;
        }
        unset($this->elements[$key]);
    }

    public function clear()
    {
        $this->elements = array();
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->elements;
    }
    
    /**
     * @return array
     */
    public function getNewElements()
    {
        return $this->newElements;
    }
}
