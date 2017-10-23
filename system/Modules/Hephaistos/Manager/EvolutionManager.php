<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Hephaistos\Model\Evolution;
use Asylamba\Modules\Zeus\Model\Player;

class EvolutionManager
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
    
    public function getEvolutions()
    {
        $result = json_decode($this->gateway->getEvolutions()->getBody(), true);
        foreach ($result as &$data) {
            $data = $this->format($data);
        }
        return $result;
    }
    
    public function getEvolution($id)
    {
        return $this->format($this->gateway->getEvolution($id)->getBody());
    }
    
    protected function format($data)
    {
        $evolution =
            (new Evolution())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setAuthor($data['author'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
        foreach ($data['commentaries'] as $commentary) {
            $evolution->addCommentary($commentary);
        }
        return $evolution;
    }
}