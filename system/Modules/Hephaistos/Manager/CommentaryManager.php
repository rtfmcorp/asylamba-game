<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Hephaistos\Model\Commentary;

use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\Player;

class CommentaryManager
{
    /** @var FeedbackGateway **/
    protected $feedbackGateway;
    /** @var PlayerManager **/
    protected $playerManager;
    
    /**
     * @param FeedbackGateway $feedbackGateway
     * @param PlayerManager $playerManager
     */
    public function __construct(FeedbackGateway $feedbackGateway, PlayerManager $playerManager)
    {
        $this->feedbackGateway = $feedbackGateway;
        $this->playerManager = $playerManager;
    }
    
    /**
     * @param string $feedbackId
     * @param string $feedbackType
     * @param string $content
     * @param Player $author
     * @return Response
     */
    public function create($feedbackId, $feedbackType, $content, Player $author)
    {
        return $this->feedbackGateway->createCommentary($feedbackId, $feedbackType, $content, $author->getName(), $author->getBind());
    }
    
    /**
     * @param type $data
     * @param type $getAuthor
     * @return type
     */
    public function format($data, $getAuthor = false)
    {
        return
            (new Commentary())
            ->setId($data['id'])
            ->setContent($data['content'])
            ->setAuthor(($getAuthor) ? $this->playerManager->getByName($data['author']['username']) : $data['author']['username'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
    }
}