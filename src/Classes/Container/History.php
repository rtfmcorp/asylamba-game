<?php

namespace App\Classes\Container;

class History {
    /** @var array **/
    private $events = array();
    
    const MAX_SIZE = 5;

    /**
     * @param string $path
     */
    public function add($path) {
        array_unshift($this->events, $path);
        if (count($this->events) > self::MAX_SIZE) {
            $this->events = array_slice($this->events, 0, self::MAX_SIZE);
        } 
    }

    /**
     * @return string
     */
    public function getCurrentPath() {
        return $this->events[0];
    }

    /**
     * @param int $larger
     * @return string
     */
    public function getPastPath($larger = 0) {
        if (isset($this->events[$larger])) {
            return $this->events[$larger];
        }
    }

    public function clear() {
        $this->events = array();
    }
}
