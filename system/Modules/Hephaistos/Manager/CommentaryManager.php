<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Modules\Hephaistos\Gateway\FeedbackGateway;

use Asylamba\Modules\Hephaistos\Model\Commentary;
use Asylamba\Modules\Hephaistos\Model\Feedback;

use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Hermes\Model\Notification;

use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Model\Player;

class CommentaryManager
{
    /** @var FeedbackGateway **/
    protected $feedbackGateway;
    /** @var NotificationManager **/
    protected $notificationManager;
    /** @var PlayerManager **/
    protected $playerManager;
    
    /**
     * @param FeedbackGateway $feedbackGateway
     * @param NotificationManager $notificationManager
     * @param PlayerManager $playerManager
     */
    public function __construct(FeedbackGateway $feedbackGateway, NotificationManager $notificationManager, PlayerManager $playerManager)
    {
        $this->feedbackGateway = $feedbackGateway;
        $this->notificationManager = $notificationManager;
        $this->playerManager = $playerManager;
    }
    
    /**
     * @param string $feedbackId
     * @param string $feedbackType
     * @param string $content
     * @param Player $author
     * @return Response
     */
    public function create(Feedback $feedback, $content, Player $author)
    {
        $commentary = $this->format(json_decode($this
            ->feedbackGateway
            ->createCommentary($feedback->getId(), $feedback->getType(), $content, $author->getName(), $author->getBind())
            ->getBody()
        , true));
        
        $notif =
            (new Notification())
            ->setTitle('Nouveau commentaire')
            ->addBeg()
            ->addLnk("embassy/player-{$author->getId()}", $author->getName())
            ->addTxt(' a posté un commentaire sur ' . (($feedback->getType() === Feedback::TYPE_BUG) ? 'le bug ': 'l\'évolution '))
            ->addLnk("feedback/id-{$feedback->getId()}/type-{$feedback->getType()}", "\"{$feedback->getTitle()}\"")
            ->addTxt('.')
            ->addEnd()
        ;
        // We avoid sending notification to the comment author, whether he is the feedback author or not
        $players = [$author->getId()];
        if ($feedback->getAuthor()->getId() !== $author->getId() && $feedback->getAuthor()->getId() !== 0) {
            $players[] = $feedback->getAuthor()->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($feedback->getAuthor()->getId());
            $this->notificationManager->add($authorNotif);
        }
        foreach ($feedback->getCommentaries() as $comment) {
            $commentAuthor = $comment->getAuthor();
            
            if (in_array($commentAuthor->getId(), $players) || $commentAuthor->getId() === 0) {
                continue;
            }
            $players[] = $commentAuthor->getId();
            $authorNotif = clone $notif;
            $authorNotif->setRPlayer($commentAuthor->getId());
            $this->notificationManager->add($authorNotif);
        }
        return $commentary;
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
            ->setAuthor($this->getAuthor($data['author']['username'], $getAuthor))
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
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