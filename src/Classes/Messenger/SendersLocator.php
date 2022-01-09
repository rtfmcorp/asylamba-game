<?php

namespace App\Classes\Messenger;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpSender;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class SendersLocator implements SendersLocatorInterface
{
	public function __construct(protected AmqpSender $sender)
	{
	}

	public function getSenders(Envelope $envelope): iterable
	{
		return [
			'async' => $this->sender,
		];
	}

	public static function instanciate(Connection $connection): self
	{
		return new self(new AmqpSender($connection));
	}
};
