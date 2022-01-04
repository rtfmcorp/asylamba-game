<?php

namespace Asylamba\Classes\Worker;

use Symfony\Component\DependencyInjection\Container;

class EventDispatcher
{
	protected array $events = [];

	public function __construct(
		protected Container $container
	) {
	}
	
	public function registerEvent(array $event): void
	{
		$this->events[$event['class']::NAME] = [];
	}
	
	public function registerListener(array $listener, string $event, int $order = 0): void
	{
		$this->events[$event][$order][] = $listener;
		// Sort the event listeners by order
		\ksort($this->events[$event]);
	}
	
	public function dispatch(object $event): void
	{
		if (!isset($this->events[$event::NAME])) {
			return;
		}
		foreach($this->events[$event::NAME] as $listeners) {
			foreach($listeners as $listenerData) {
				$listener = $this->container->get($listenerData['key']);

				$listener->{$listenerData['method']}($event);
			}
		}
	}
}
