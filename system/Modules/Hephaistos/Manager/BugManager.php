<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;

use Asylamba\Modules\Hephaistos\Model\Bug;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Model\Player;

class BugManager
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
     * @return mixed
     */
    public function create($title, $description, Player $player)
    {
        return $this->gateway->createBug(
            $title,
            $description,
            Bug::STATUS_TODO,
            $player->getName(),
            $player->getBind()
        );
    }
    
    /**
     * @param Bug $bug
     * @param Player $player
     * @return Response
     */
    public function update(Bug $bug, Player $player)
    {
        $updatedBug = $this->format(json_decode($this->gateway->updateBug($bug)->getBody(), true));
        
        $notif =
            (new Notification())
            ->setTitle('Bug mis à jour')
            ->addBeg()
            ->addLnk("embassy/player-{$player->getId()}", $player->getName())
            ->addTxt(' a mis à jour le bug ')
            ->addLnk("feedback/id-{$bug->getId()}/type-{$bug->getType()}", "\"{$bug->getTitle()}\"")
            ->addTxt('.')
            ->addEnd()
        ;
        // We avoid sending notification to the updater, whether he is the feedback author or not
        $players = [$player->getId()];
        if ($bug->getAuthor()->getId() !== $player->getId() && $bug->getAuthor()->getId() !== null) {
            $players[] = $bug->getAuthor()->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($bug->getAuthor()->getId());
            $this->notificationManager->add($authorNotif);
        }
        foreach ($bug->getCommentaries() as $comment) {
            $commentAuthor = $comment->getAuthor();
            
            if (in_array($commentAuthor->getId(), $players) || $commentAuthor->getId() === null) {
                continue;
            }
            $players[] = $commentAuthor->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($commentAuthor->getId());
            $this->notificationManager->add($authorNotif);
        }
        return $updatedBug;
    }
    
    /**
     * @return array
     */
    public function getAll()
    {
        $result = json_decode($this->gateway->getBugs()->getBody(), true);
        foreach ($result as &$data) {
            $data = $this->format($data);
        }
        return $result;
    }
    
    /**
     * @param string $id
     * @return Bug
     */
    public function get($id)
    {
        
        return $this->format(json_decode($this->gateway->getBug($id)->getBody(), true), true);
    }
    
    /**
     * @param array $data
     * @param boolean $getAuthor
     * @return Bug
     */
    protected function format($data, $getAuthor = false)
    {
        $bug =
            (new Bug())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setAuthor($this->getAuthor($data['author']['username'], $getAuthor))
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
        foreach ($data['commentaries'] as $commentary) {
            $bug->addCommentary($this->commentaryManager->format($commentary, true));
        }
        return $bug;
    }
    
    protected function getAuthor($name, $getAuthorData = false)
    {
        if ($getAuthorData === false) {
            return $name;
        }
        if (($author = $this->playerManager->getByName($name)) === null) {
            return
                (new Player())
                ->setName($name)
                ->setAvatar('rebel')
            ;
        }
        return $author;
    }
}