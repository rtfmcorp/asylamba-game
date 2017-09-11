<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\DependencyInjection\Container;

class EventDispatcher
{
    /** @var array **/
    protected $events = [];
    /** @var Container **/
    protected $container;
    
    /**
     * @param \Asylamba\Classes\Worker\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * @param array $event
     */
    public function registerEvent($event)
    {
        $this->events[$event['class']::NAME] = [];
    }
    
    /**
     * @param array $listener
     * @param string $event
     * @param int $order
     */
    public function registerListener($listener, $event, $order = 0)
    {
        $this->events[$event][$order][] = $listener;
        // Sort the event listeners by order
        ksort($this->events[$event]);
    }
    
    /**
     * @param object $event
     */
    public function dispatch($event)
    {
        if (!isset($this->events[$event::NAME])) {
            return;
        }
        foreach ($this->events[$event::NAME] as $listeners) {
            foreach ($listeners as $listenerData) {
                $listener = $this->container->get($listenerData['key']);

                $listener->{$listenerData['method']}($event);
            }
        }
    }
}
