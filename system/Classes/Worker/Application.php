<?php

namespace Asylamba\Classes\Worker;

class Application {
    /** @var Container **/
    protected $container;
    
    public function boot()
    {
        $this->container = new Container();
    }
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}