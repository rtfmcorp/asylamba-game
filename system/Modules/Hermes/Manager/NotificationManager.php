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

class NotificationManager
{
    /** @var EntityManager **/
    protected $entityManager;
    
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function get($id)
    {
        return $this->entityManager->getRepository(Notification::class)->get($id);
    }
    
    public function getUnreadNotifications($playerId)
    {
        return $this->entityManager->getRepository(Notification::class)->getUnreadNotifications($playerId);
    }
    
    public function getPlayerNotificationsByArchive($playerId, $isArchived)
    {
        return $this->entityManager->getRepository(Notification::class)->getPlayerNotificationsByArchive($playerId, $isArchived);
    }
    
    public function getAllByReadState($isReaded)
    {
        return $this->entityManager->getRepository(Notification::class)->getAllByReadState($isReaded);
    }

    public function patchForMultiCombats($commanderPlayerId, $placePlayerId, $arrivedAt)
    {
        $notifications = $this
            ->entityManager
            ->getRepository(Notification::class)
            ->getMultiCombatNotifications($commanderPlayerId, $placePlayerId, $arrivedAt)
        ;
        $nbNotifications = count($notifications);
        if ($nbNotifications > 2) {
            for ($i = 0; $i < $nbNotifications - 2; $i++) {
                $this->entityManager->remove($notifications[$i]);
            }
        }
        $this->entityManager->flush(Notification::class);
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
