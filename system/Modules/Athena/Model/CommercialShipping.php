<?php

/**
 * CommercialShipping
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 13.11.13
 */
namespace Asylamba\Modules\Athena\Model;

class CommercialShipping
{
    # statement
    const ST_WAITING = 0;        # pret au départ, statique
    const ST_GOING = 1;            # aller
    const ST_MOVING_BACK = 2;    # retour

    const WEDGE = 1000;    # soute

    # attributes
    public $id = 0;
    public $rPlayer = 0;
    public $rBase = 0;
    public $rBaseDestination = 0;
    public $rTransaction = null;            # soit l'un
    public $resourceTransported = null;        # soit l'autre
    public $shipQuantity = 0;
    public $dDeparture = '';
    public $dArrival = '';
    public $statement = 0;

    public $baseRSystem;
    public $basePosition;
    public $baseXSystem;
    public $baseYSystem;

    public $destinationRSystem;
    public $destinationPosition;
    public $destinationXSystem;
    public $destinationYSystem;

    public $price;
    public $typeOfTransaction;
    public $quantity;
    public $identifier;
    public $commanderAvatar;
    public $commanderName;
    public $commanderLevel;
    public $commanderVictory;
    public $commanderExperience;

    /**
     * @param int $id
     * @return CommercialShipping
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $playerId
     * @return CommercialShipping
     */
    public function setPlayerId($playerId)
    {
        $this->rPlayer = $playerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerId()
    {
        return $this->rPlayer;
    }

    /**
     * @param int $baseId
     * @return CommercialShipping
     */
    public function setBaseId($baseId)
    {
        $this->rBase = $baseId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBaseId()
    {
        return $this->rBase;
    }

    /**
     * @param int $destinationId
     * @return CommercialShipping
     */
    public function setDestinationBaseId($destinationId)
    {
        $this->rBaseDestination = $destinationId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationBaseId()
    {
        return $this->rBaseDestination;
    }

    /**
     * @param int $transactionId
     * @return CommercialShipping
     */
    public function setTransactionId($transactionId)
    {
        $this->rTransaction = $transactionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->rTransaction;
    }

    /**
     * @param int $resources
     * @return CommercialShipping
     */
    public function setResources($resources)
    {
        $this->resourceTransported = $resources;

        return $this;
    }

    /**
     * @return int
     */
    public function getResources()
    {
        return $this->resourceTransported;
    }

    /**
     * @param int $shipQuantity
     * @return CommercialShipping
     */
    public function setShipQuantity($shipQuantity)
    {
        $this->shipQuantity = $shipQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getShipQuantity()
    {
        return $this->shipQuantity;
    }

    /**
     * @param string $departedAt
     * @return CommercialShipping
     */
    public function setDepartedAt($departedAt)
    {
        $this->dDeparture = $departedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartedAt()
    {
        return $this->dDeparture;
    }

    /**
     * @param string $arrivedAt
     * @return CommercialShipping
     */
    public function setArrivedAt($arrivedAt)
    {
        $this->dArrival = $arrivedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getArrivedAt()
    {
        return $this->dArrival;
    }

    /**
     * @param int $statement
     * @return CommercialShipping
     */
    public function setStatement($statement)
    {
        $this->statement = $statement;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param int $systemId
     * @return CommercialShipping
     */
    public function setBaseSystemId($systemId)
    {
        $this->baseRSystem = $systemId;

        return $this;
    }

    /**
     * @return int
     */
    public function getBaseSystemId()
    {
        return $this->baseRSystem;
    }

    /**
     * @param int $position
     * @return CommercialShipping
     */
    public function setBasePosition($position)
    {
        $this->basePosition = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getBasePosition()
    {
        return $this->basePosition;
    }

    /**
     * @param int $xPosition
     * @return CommercialShipping
     */
    public function setBaseSystemXPosition($xPosition)
    {
        $this->baseXSystem = $xPosition;

        return $this;
    }

    /**
     * @return int
     */
    public function getBaseSystemXPosition()
    {
        return $this->baseXSystem;
    }

    /**
     * @param int $yPosition
     * @return CommercialShipping
     */
    public function setBaseSystemYPosition($yPosition)
    {
        $this->baseYSystem = $yPosition;

        return $this;
    }

    /**
     * @return int
     */
    public function getBaseSystemYPosition()
    {
        return $this->baseYSystem;
    }

    /**
     * @param int $systemId
     * @return CommercialShipping
     */
    public function setDestinationBaseSystemId($systemId)
    {
        $this->destinationRSystem = $systemId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationBaseSystemId()
    {
        return $this->destinationRSystem;
    }

    /**
     * @param int $position
     * @return CommercialShipping
     */
    public function setDestinationBasePosition($position)
    {
        $this->destinationPosition = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationBasePosition()
    {
        return $this->destinationPosition;
    }

    /**
     * @param int $xPosition
     * @return CommercialShipping
     */
    public function setDestinationBaseSystemXPosition($xPosition)
    {
        $this->destinationXSystem = $xPosition;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationBaseSystemXPosition()
    {
        return $this->destinationXSystem;
    }

    /**
     * @param int $yPosition
     * @return CommercialShipping
     */
    public function setDestinationBaseSystemYPosition($yPosition)
    {
        $this->destinationYSystem = $yPosition;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationBaseSystemYPosition()
    {
        return $this->destinationYSystem;
    }

    /**
     * @param int $price
     * @return CommercialShipping
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $transactionType
     * @return CommercialShipping
     */
    public function setTransactionType($transactionType)
    {
        $this->typeOfTransaction = $transactionType;

        return $this;
    }

    /**
     * @return int
     */
    public function getTransactionType()
    {
        return $this->typeOfTransaction;
    }

    /**
     * @param int $quantity
     * @return CommercialShipping
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $identifier
     * @return CommercialShipping
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $avatar
     * @return CommercialShipping
     */
    public function setCommanderAvatar($avatar)
    {
        $this->commanderAvatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommanderAvatar()
    {
        return $this->commanderAvatar;
    }

    /**
     * @param string $name
     * @return CommercialShipping
     */
    public function setCommanderName($name)
    {
        $this->commanderName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommanderName()
    {
        return $this->commanderName;
    }

    /**
     * @param int $level
     * @return CommercialShipping
     */
    public function setCommanderLevel($level)
    {
        $this->commanderLevel = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getCommanderLevel()
    {
        return $this->commanderLevel;
    }

    /**
     * @param int $nbVictories
     * @return CommercialShipping
     */
    public function setCommanderVictory($nbVictories)
    {
        $this->commanderVictory = $nbVictories;

        return $this;
    }

    /**
     * @return int
     */
    public function getCommanderVictory()
    {
        return $this->commanderVictory;
    }

    /**
     * @param int $experience
     * @return CommercialShipping
     */
    public function setCommanderExperience($experience)
    {
        $this->commanderExperience = $experience;

        return $this;
    }

    /**
     * @return int
     */
    public function getCommanderExperience()
    {
        return $this->commanderExperience;
    }
}
