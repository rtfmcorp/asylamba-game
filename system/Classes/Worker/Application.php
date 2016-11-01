<?php

namespace Asylamba\Classes\Worker;

class Application {
    /** @var Container **/
    protected $container;
    
    public function boot()
    {
        $this->container = new Container();
    }
    
    public function run()
    {
        
    }
    
    /**
     * e
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}