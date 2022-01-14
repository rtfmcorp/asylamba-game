<?php

namespace App\Shared\Infrastructure\EventSubscriber;

use App\Modules\Hermes\Domain\Repository\ConversationRepositoryInterface;
use App\Modules\Hermes\Manager\NotificationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
	protected ?SessionInterface $session;
	
	public function __construct(
		protected Environment $twig,
		protected ConversationRepositoryInterface $conversationRepository,
		protected NotificationManager $notificationManager,
		RequestStack $requestStack,
	) {
		$this->session = $requestStack->getSession();
	}

	public static function getSubscribedEvents(): array
	{
		return [
			ControllerEvent::class => [
				['setCurrentPlayerFactionId'],
				['setCurrentPlayerBases'],
				['setConversationsCount'],
				['setCurrentPlayerNotifications'],
			],
		];
	}

	public function setCurrentPlayerFactionId(): void
	{
		if (null === $this->session->get('playerId')) {
			return;
		}
		$this->twig->addGlobal('current_player_faction_id', $this->session->get('playerInfo')->get('color'));
	}
	
	public function setCurrentPlayerBases(): void
	{
		if (null === $this->session->get('playerId')) {
			return;
		}
		$currentBaseName = NULL;
		$currentBaseImg  = NULL;
		$ob = $this->session->get('playerBase')->get('ob');
		for ($i = 0; $i < $ob->size(); $i++) {
			if ($this->session->get('playerParams')->get('base') == $ob->get($i)->get('id')) {
				$currentBaseName = $ob->get($i)->get('name');
				$currentBaseImg  = $ob->get($i)->get('img');
				break;
			}
		}

		if ($ob->get(0)) {
			$nextBaseId = $ob->get(0)->get('id');
			$isFound = false;
			for ($i = 0; $i < $ob->size(); $i++) {
				if ($isFound) {
					$nextBaseId = $ob->get($i)->get('id');
					break;
				}
				if ($this->session->get('playerParams')->get('base') == $ob->get($i)->get('id')) {
					$isFound = true;
				}
			}
		} else {
			$nextBaseId = 0;
			$currentBaseName = 'Reconnectez-vous';
			$currentBaseImg = '1-1';
		}
		$this->twig->addGlobal('next_base_id', $nextBaseId);
		$this->twig->addGlobal('current_base_name', $currentBaseName);
		$this->twig->addGlobal('current_base_image', $currentBaseImg);
	}

	public function setConversationsCount(): void
	{
		if (null === $this->session->get('playerId')) {
			return;
		}
		$this->twig->addGlobal('conversations_count', $this->conversationRepository->countPlayerConversations(
			$this->session->get('playerId'),
		));
	}

	public function setCurrentPlayerNotifications(): void
	{
		if (null === $this->session->get('playerId')) {
			return;
		}
		$this->twig->addGlobal('current_player_notifications', $this->notificationManager->getUnreadNotifications(
			$this->session->get('playerId'),
		));
	}
}
