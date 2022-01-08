<?php

namespace Asylamba\Classes\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;

class BusFactory
{
	public static function createBus(
		HandlersLocatorInterface $handlersLocator,
		SendersLocator $sendersLocator,
		LoggerInterface $logger,
		?bool $enableLogs,
	): MessageBusInterface {
		$setLogger = (true === $enableLogs)
			? function ($middleware) use ($logger) {
				$middleware->setLogger($logger);

				return $middleware;
			}
			: fn ($middleware) => $middleware;

		return new MessageBus([
			$setLogger(new SendMessageMiddleware($sendersLocator)),
			$setLogger(new HandleMessageMiddleware($handlersLocator)),
		]);
	}
}
