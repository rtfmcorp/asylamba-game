<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;

use Asylamba\Modules\Hephaistos\Model\Evolution;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Model\Player;

class EvolutionManager
{
    /** @var FeedbackGateway **/
    protected $gateway;
    /** @var CommentaryManager **/
    protected $commentaryManager;
    /** @var NotificationManager **/
    protected $notificationManager;
    /** @var PlayerManager **/
    protected $playerManager;
    
    /**
     * @param FeedbackGateway $gateway
     * @param CommentaryManager $commentaryManager
     * @param NotificationManager $notificationManager
     * @param PlayerManager $playerManager
     */
    public function __construct(FeedbackGateway $gateway, CommentaryManager $commentaryManager, NotificationManager $notificationManager, PlayerManager $playerManager)
    {
        $this->gateway = $gateway;
        $this->commentaryManager = $commentaryManager;
        $this->notificationManager = $notificationManager;
        $this->playerManager = $playerManager;
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param Player $player
     * @return Response
     */
    public function create($title, $description, Player $player)
    {
        return $this->gateway->createEvolution(
            $title,
            $description,
            Evolution::STATUS_TODO,
            $player->getName(),
            $player->getBind()
        );
    }
    
    /**
     * @param Evolution $evolution
     * @param Player $player
     * @return Response
     */
    public function update(Evolution $evolution, Player $player)
    {
        $updatedEvolution = $this->gateway->updateEvolution($evolution);
        
        $notif =
            (new Notification())
            ->setTitle('Evolution mise à jour')
            ->addBeg()
            ->addLnk("embassy/player-{$player->getId()}", $player->getName())
            ->addTxt(' a mis à jour l\'évolution ')
            ->addLnk("feedback/id-{$evolution->getId()}/type-{$evolution->getType()}", "\"{$evolution->getTitle()}\"")
            ->addTxt('.')
            ->addEnd()
        ;
        // We avoid sending notification to the updater, whether he is the feedback author or not
        $players = [$player->getId()];
        if ($evolution->getAuthor()->getId() !== $player->getId()) {
            $players[] = $evolution->getAuthor()->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($evolution->getAuthor()->getId());
            $this->notificationManager->add($authorNotif);
        }
        foreach ($evolution->getCommentaries() as $comment) {
            $commentAuthor = $comment->getAuthor();
            
            if (in_array($commentAuthor->getId(), $players) || $commentAuthor->getId() === null) {
                continue;
            }
            $players[] = $commentAuthor->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($commentAuthor->getId());
            $this->notificationManager->add($authorNotif);
        }
        return $updatedEvolution;
    }
    
    /**
     * @return array
     */
    public function getAll()
    {
        $result = json_decode($this->gateway->getEvolutions()->getBody(), true);
        foreach ($result as &$data) {
            $data = $this->format($data);
        }
        return $result;
    }
    
    /**
     * @param string $id
     * @return Evolution
     */
    public function get($id)
    {
        return $this->format(json_decode($this->gateway->getEvolution($id)->getBody(), true), true);
    }
    
    /**
     * @param array $data
     * @param boolean $getAuthor
     * @return Evolution
     */
    protected function format($data, $getAuthor = false)
    {
        $evolution =
            (new Evolution())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setAuthor(($getAuthor) ? $this->playerManager->getByName($data['author']['username']) : $data['author'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
        foreach ($data['commentaries'] as $commentary) {
            $evolution->addCommentary($this->commentaryManager->format($commentary, true));
        }
        return $evolution;
    }
}