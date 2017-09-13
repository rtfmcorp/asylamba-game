<?php

namespace Asylamba\Modules\Hermes\Subscriber;

use Asylamba\Modules\Hermes\Manager\NewsManager;

use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Modules\Hermes\Model\Press\MilitaryNews;
use Asylamba\Modules\Hermes\Model\Press\PoliticNews;
use Asylamba\Modules\Hermes\Model\Press\TradeNews;

use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;

use Asylamba\Modules\Athena\Model\Transaction;

use Asylamba\Modules\Athena\Event\TransactionProposalEvent;

use Asylamba\Modules\Ares\Event\ConquestEvent;
use Asylamba\Modules\Ares\Event\LootEvent;

use Asylamba\Modules\Demeter\Event\CampaignEvent;
use Asylamba\Modules\Demeter\Event\CandidateEvent;
use Asylamba\Modules\Demeter\Event\ElectionEvent;
use Asylamba\Modules\Demeter\Event\ElectionResultsEvent;
use Asylamba\Modules\Demeter\Event\PutschAttemptEvent;
use Asylamba\Modules\Demeter\Event\PutschFailureEvent;
use Asylamba\Modules\Demeter\Event\PutschSuccessEvent;
use Asylamba\Modules\Demeter\Event\SenateElectionEvent;

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
    
    /**
     * @param ConquestEvent $event
     */
    public function onConquest(ConquestEvent $event)
    {
        if ($event->getIsVictory()) {
            $title = "La planète {$event->getPlace()->getBaseName()} est tombée aux mains de {$event->getAttacker()->getName()} !";
            $content = "";
        } else {
            $title = "{$event->getDefender()->getName()} a repoussé une conquête sur la planète {$event->getPlace()->getBaseName()}";
            $content = "";
        }
        $this->newsManager->create(
            (new MilitaryNews())
            ->setTitle($title)
            ->setContent($content)
            ->setType(MilitaryNews::TYPE_CONQUEST)
            ->setPlace($event->getPlace())
            ->setAttacker($event->getAttacker())
            ->setDefender($event->getDefender())
            ->setIsVictory($event->getIsVictory())
        );
    }
    
    /**
     * @param LootEvent $event
     */
    public function onLoot(LootEvent $event)
    {
        if ($event->getIsVictory()) {
            $title = "La planète {$event->getPlace()->getBaseName()} a subi un pillage de {$event->getAttacker()->getName()} !";
            $content = "";
        } else {
            $title = "{$event->getDefender()->getName()} a repoussé un pillage sur la planète {$event->getPlace()->getBaseName()}";
            $content = "";
        }
        $this->newsManager->create(
            (new MilitaryNews())
            ->setTitle($title)
            ->setContent($content)
            ->setType(MilitaryNews::TYPE_LOOT)
            ->setPlace($event->getPlace())
            ->setAttacker($event->getAttacker())
            ->setDefender($event->getDefender())
            ->setIsVictory($event->getIsVictory())
        );
    }
    
    /**
     * @param CampaignEvent $event
     */
    public function onCampaign(CampaignEvent $event)
    {
        $faction = $event->getFaction();
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle("Une nouvelle campagne électorale démarre au sein de " . ColorResource::getInfo($faction->getId(), 'popularName'))
            ->setContent("Les sénateurs " . ColorResource::getInfo($faction->getId(), 'demonym') . ' vont pouvoir se présenter pour accéder au pouvoir.')
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_CAMPAIGN)
        );
    }
    
    /**
     * @param CandidateEvent $event
     */
    public function onCandidate(CandidateEvent $event)
    {
        $faction = $event->getFaction();
        $candidate = $event->getCandidate();
        
        if ($faction->getRegime() === Color::DEMOCRATIC) {
            $title = "{$candidate->getName()} se présente aux élections de " . ColorResource::getInfo($faction->getId(), 'popularName');
            $content = "Le débat démocratique s'intensifie entre les " . ColorResource::getInfo($faction->getId(), 'demonym');
        } else {
            $title = "{$candidate->getName()} se soumet au regard des dieux, se proposant comme guide de " . ColorResource::getInfo($faction->getId(), 'popularName');
            $content = "Un nouveau nom parmi les " . ColorResource::getInfo($faction->getId(), 'demonym') . " se propose comme Grand Maître.";
        }
        
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle($title)
            ->setContent($content)
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_CANDIDATE)
        );
    }
    
    /**
     * @param ElectionEvent $event
     */
    public function onElection(ElectionEvent $event)
    {
        $faction = $event->getFaction();
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle("Les élections débutent pour " . ColorResource::getInfo($faction->getId(), 'popularName'))
            ->setContent("Les " . ColorResource::getInfo($faction->getId(), 'demonym') . " sont tous appelés aux urnes, pour élire leur prochain dirigeant.")
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_ELECTION)
        );
    }
    
    /**
     * @param ElectionResultsEvent $event
     */
    public function onElectionResults(ElectionResultsEvent $event)
    {
        $faction = $event->getFaction();
        $winner = $event->getWinner();
        
        if ($faction->getRegime() === Color::DEMOCRATIC) {
            $title = "Les élections du dirigeant de " . ColorResource::getInfo($faction->getId(), 'popularName'). ' sont désormais closes';
            $content = "{$winner->getName()} a été choisi par le peuple pour mener les " . ColorResource::getInfo($faction->getId(), 'popularName');
        } else {
            $title = "Les dieux ont choisi {$winner->getName()} pour guider " . ColorResource::get($faction->getId(), 'popularName');
            $content = 'Les oracles ont fait connaître la volonté divine. Un nouveau Grand Maître a été désigné.';
        }
        
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle($title)
            ->setContent($content)
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_RESULTS)
        );
    }
    
    /**
     * @param PutschAttemptEvent $event
     */
    public function onPutschAttempt(PutschAttemptEvent $event)
    {
        $faction = $event->getFaction();
        $pretender = $event->getPretender();
        $leader = $event->getLeader();
        $content =
            ($leader !== null)
            ? "Les partisans des deux camps sont appelés à se déclarer pour leur meneur. {$leader->getName()} réussira-t-il à conserver le pouvoir ?"
            : "Les " . ColorResource::getInfo($faction->getId(), 'demonym') . 'doivent désormais choisir si le prétendant est digne de les diriger.'
        ;
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle("{$pretender->getName()} tente un coup d'état dans " . ColorResource::getInfo($faction->getId(), 'popularName'))
            ->setContent($content)
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_PUTSCH_ATTEMPT)
        );
    }
    
    /**
     * @param PutschFailureEvent $event
     */
    public function onPutschFailure(PutschFailureEvent $event)
    {
        $faction = $event->getFaction();
        $pretender = $event->getPretender();
        $leader = $event->getLeader();
        
        $content =
            ($leader !== null)
            ? "Les " . ColorResource::getInfo($faction->getId(), 'demonym') . " n'ont pas soutenu l'insurrection. {$leader->getName()} conserve le pouvoir."
            : "Les " . ColorResource::getInfo($faction->getId(), 'demonym') . ' n\'ont pas soutenu le coup d\'état.'
        ;
            
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle("Le coup d'état tenté par {$pretender->getName()} à " . ColorResource::getInfo($faction->getId(), 'popularName') . ' a échoué')
            ->setContent($content)
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_PUTSCH_FAILURE)
        );
    }
    
    /**
     * @param PutschSuccessEvent $event
     */
    public function onPutschSuccess(PutschSuccessEvent $event)
    {
        $faction = $event->getFaction();
        $previousLeader = $event->getPreviousLeader();
        $newLeader = $event->getNewLeader();
        
        $title =
            ($previousLeader !== null)
            ? "{$previousLeader->getName()} a été renversé par {$newLeader->getName()}"
            : "{$newLeader->getName()} prend le pouvoir en " . ColorResource::getInfo($faction->getId(), 'popularName')
        ;
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle($title)
            ->setContent("Les " . ColorResource::getInfo($faction->getId(), 'demonym') . " ont un nouveau dirigeant à leur tête.")
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_PUTSCH_SUCCESS)
        );
    }

    /**
     * @param SenateElectionEvent $event
     */
    public function onSenateElection(SenateElectionEvent $event)
    {
        $faction = $event->getFaction();
        $this->newsManager->create(
            (new PoliticNews())
            ->setTitle("Le sénat de " . ColorResource::getInfo($faction->getId(), 'popularName') . ' vient d\'être renouvelé')
            ->setContent("Les nouveaux sénateurs " . ColorResource::getInfo($faction->getId(), 'demonym'))
            ->setFaction($faction)
            ->setType(PoliticNews::TYPE_SENATE)
        );
    }
}
