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
     * @return Response
     */
    public function update(Bug $bug)
    {
        return $this->gateway->updateBug($bug);
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
            ->setAuthor(($getAuthor) ? $this->playerManager->getByName($data['author']['username']) : $data['author'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
        foreach ($data['commentaries'] as $commentary) {
            $bug->addCommentary($this->commentaryManager->format($commentary, true));
        }
        return $bug;
    }
}