<?php

namespace Asylamba\Classes\Worker;

class EventDispatcher {
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
	 */
	public function registerListener($listener, $event)
	{
		$this->events[$event][] = $listener;
	}
	
	/**
	 * @param object $event
	 */
	public function dispatch($event)
	{
		if (!isset($this->events[$event::NAME])) {
			return;
		}
		$listeners = $this->events[$event::NAME];
		
		foreach($listeners as $listenerData) {
			$listener = $this->container->get($listenerData['key']);
			
			$listener->{$listenerData['method']}($event);
		}
	}
}