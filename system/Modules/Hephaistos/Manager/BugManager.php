<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Hephaistos\Model\Bug;
use Asylamba\Modules\Zeus\Model\Player;

class BugManager
{
    /** @var FeedbackGateway **/
    protected $gateway;
    
    /**
     * @param FeedbackGateway $gateway
     */
    public function __construct(FeedbackGateway $gateway)
    {
        $this->gateway = $gateway;
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
    
    public function getBugs()
    {
        $result = json_decode($this->gateway->getBugs()->getBody(), true);
        foreach ($result as &$data) {
            $data = $this->format($data);
        }
        return $result;
    }
    
    public function getBug($id)
    {
        return $this->format($this->gateway->getBug($id)->getBody());
    }
    
    protected function format($data)
    {
        $bug =
            (new Bug())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setAuthor($data['author'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
        foreach ($data['commentaries'] as $commentary) {
            $bug->addCommentary($commentary);
        }
        return $bug;
    }
}