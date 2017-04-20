<?php

/**
 * Notification Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 20.05.13
*/
namespace Asylamba\Modules\Hermes\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Hermes\Model\Notification;

class NotificationManager {
    /** @var EntityManager **/
    protected $entityManager;
    
    /**
     * @param EntityManager $entityManager
     */
	public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
	}

	public function add(Notification $notification)
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush($notification);
	}

	public function deleteByRPlayer($rPlayer)
    {
        return $this->entityManager->getRepository(Notification::class)->removePlayerNotifications($rPlayer);
	}
}