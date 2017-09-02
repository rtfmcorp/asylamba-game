<?php

namespace Asylamba\Modules\Hermes\Subscriber;

use Asylamba\Modules\Hermes\Manager\NewsManager;

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;
use Asylamba\Modules\Hermes\Model\Press\PoliticNews;
use Asylamba\Modules\Hermes\Model\Press\TradeNews;

use Asylamba\Modules\Athena\Resource\ShipResource;

use Asylamba\Modules\Athena\Model\Transaction;

use Asylamba\Modules\Athena\Event\TransactionProposalEvent;

use Asylamba\Classes\Library\Format;

class NewsSubscriber
{
    /** @var NewsManager **/
    protected $newsManager;
    
    /**
     * @param NewsManager $newsManager
     */
    public function __construct(NewsManager $newsManager)
    {
        $this->newsManager = $newsManager;
    }
    
    public function onTransactionProposal(TransactionProposalEvent $event)
    {
        $transaction = $event->getTransaction();
        
        switch ($transaction->type) {
            case Transaction::TYP_RESOURCE:
                $title = "Vente de {$transaction->quantity} ressources depuis {$transaction->placeName} !";
                $content =
                    "{$transaction->playerName} organise sur sa planète {$transaction->placeName} " .
                    "la vente de {$transaction->quantity} ressources pour la somme de {$transaction->price} crédits !"
                ;
                break;
            case Transaction::TYP_SHIP:
                $title = "Vente de {$transaction->quantity} " . ShipResource::getInfo($transaction->identifier, 'codeName') . Format::addPlural($transaction->quantity) . " !";
                $content =
                    "{$transaction->playerName} vend depuis sa planète {$transaction->placeName} " .
                    "{$transaction->quantity} vaisseaux pour la somme de {$transaction->price} crédits !"
                ;
                break;
            case Transaction::TYP_COMMANDER:
                $title = "Un {$transaction->commanderLevel} mercenaire propose ses services !";
                $content =
                    "{$transaction->playerName} loue depuis sa planète {$transaction->placeName} " .
                    "les services de son officier {$transaction->commanderName} pour la somme de {$transaction->price} crédits !"
                ;
                break;
        }
        $this->newsManager->create(
            (new TradeNews())
            ->setTitle($title)
            ->setContent($content)
            ->setTransaction($transaction)
        );
    }
}