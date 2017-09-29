<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Classes\Library\Session\SessionWrapper;

use Asylamba\Modules\Hephaistos\Model\Donation;
use Asylamba\Modules\Zeus\Model\Player;

use Stripe\Stripe;
use Stripe\Charge;

class DonationManager
{
    /** @var EntityManager **/
    protected $entityManager;
    /** @var PlayerManager **/
    protected $playerManager;
    /** @var SessionWrapper **/
    protected $sessionWrapper;
    /** @var string **/
    protected $apiKey;
    
    /**
     * @param EntityManager $entityManager
     * @param PlayerManager $playerManager
     * @param SessionWrapper $sessionWrapper
     * @param string $apiKey
     */
    public function __construct(
        EntityManager $entityManager,
        PlayerManager $playerManager,
        SessionWrapper $sessionWrapper,
        $apiKey
    )
    {
        $this->entityManager = $entityManager;
        $this->playerManager = $playerManager;
        $this->sessionWrapper = $sessionWrapper;
        $this->apiKey = $apiKey;
        Stripe::setApiKey($apiKey);
    }
    
    /**
     * @param string $token
     * @param string $amount
     */
    public function createCharge($token, $amount)
    {
        $charge = Charge::create(array(
            "amount" => $amount,
            "currency" => "eur",
            "description" => "Donation de {$this->sessionWrapper->get('playerInfo')->get('name')}",
            "source" => $token,
        ));
        
        $player = $this->playerManager->get($this->sessionWrapper->get('playerId'));
        
        $donation =
            (new Donation())
            ->setAmount($charge->amount)
            ->setToken($charge->id)
            ->setPlayer($player)
            ->setCreatedAt(new \DateTime())
        ;
        
        $this->entityManager->persist($donation);
        $this->entityManager->flush($donation);
        
        return $charge;
    }
    
    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerSum(Player $player)
    {
        return $this->entityManager->getRepository(Donation::class)->getPlayerSum($player);
    }
    
    /**
     * @param Player $player
     * @return array
     */
    public function getPlayerDonations(Player $player)
    {
        return $this->entityManager->getRepository(Donation::class)->getPlayerDonations($player);
    }
    
    /**
     * @return array
     */
    public function getAllDonations()
    {
        return $this->entityManager->getRepository(Donation::class)->getAllDonations();
    }
}