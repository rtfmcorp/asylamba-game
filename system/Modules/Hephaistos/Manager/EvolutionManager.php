<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Zeus\Manager\PlayerManager;

use Asylamba\Modules\Hephaistos\Model\Evolution;
use Asylamba\Modules\Zeus\Model\Player;

class EvolutionManager
{
    /** @var FeedbackGateway **/
    protected $gateway;
    /** @var CommentaryManager **/
    protected $commentaryManager;
    /** @var PlayerManager **/
    protected $playerManager;
    
    /**
     * @param FeedbackGateway $gateway
     * @param PlayerManager $playerManager
     */
    public function __construct(FeedbackGateway $gateway, CommentaryManager $commentaryManager, PlayerManager $playerManager)
    {
        $this->gateway = $gateway;
        $this->commentaryManager = $commentaryManager;
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
     * @return Response
     */
    public function update(Evolution $evolution)
    {
        return $this->gateway->updateEvolution($evolution);
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