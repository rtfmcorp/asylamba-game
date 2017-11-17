<?php

/**
 * ResearchManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 20.05.13
*/

namespace Asylamba\Modules\Promethee\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Container\StackList;

use Asylamba\Modules\Promethee\Model\Research;

use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Promethee\Helper\ResearchHelper;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Resource\ColorResource;

use Asylamba\Classes\Exception\ErrorException;

class ResearchManager extends Manager
{
    /** @var string **/
    protected $managerType = '_Research';
    /** @var PlayerManager **/
    protected $playerManager;
    /** @var NotificationManager **/
    protected $notificationManager;
    /** @var ResearchHelper **/
    protected $researchHelper;
    /** @var int **/
    protected $researchQuantity;
    
    /**
     * @param Database $database
     * @param PlayerManager $playerManager
     * @param NotificationManager $notificationManager
     * @param ResearchHelper $researchHelper
     * @param int $researchQuantity
     */
    public function __construct(
        Database $database,
        PlayerManager $playerManager,
        NotificationManager $notificationManager,
        ResearchHelper $researchHelper,
        $researchQuantity
    ) {
        parent::__construct($database);
        $this->playerManager = $playerManager;
        $this->notificationManager = $notificationManager;
        $this->researchHelper = $researchHelper;
        $this->researchQuantity = $researchQuantity;
    }
    
    public function load($where = array(), $order = array(), $limit = array())
    {
        $formatWhere = Utils::arrayToWhere($where);
        $formatOrder = Utils::arrayToOrder($order);
        $formatLimit = Utils::arrayToLimit($limit);

        $qr = $this->database->prepare(
            'SELECT *
			FROM research
			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit
        );

        foreach ($where as $v) {
            if (is_array($v)) {
                foreach ($v as $p) {
                    $valuesArray[] = $p;
                }
            } else {
                $valuesArray[] = $v;
            }
        }

        if (empty($valuesArray)) {
            $qr->execute();
        } else {
            $qr->execute($valuesArray);
        }

        while ($aw = $qr->fetch()) {
            $res = new Research();

            $res->rPlayer = $aw['rPlayer'];
            $res->mathLevel = $aw['mathLevel'];
            $res->physLevel = $aw['physLevel'];
            $res->chemLevel = $aw['chemLevel'];
            $res->bioLevel = $aw['bioLevel'];
            $res->mediLevel = $aw['mediLevel'];
            $res->econoLevel = $aw['econoLevel'];
            $res->psychoLevel = $aw['psychoLevel'];
            $res->networkLevel = $aw['networkLevel'];
            $res->algoLevel = $aw['algoLevel'];
            $res->statLevel = $aw['statLevel'];
            $res->naturalTech = $aw['naturalTech'];
            $res->lifeTech = $aw['lifeTech'];
            $res->socialTech = $aw['socialTech'];
            $res->informaticTech = $aw['informaticTech'];
            $res->naturalToPay = $aw['naturalToPay'];
            $res->lifeToPay = $aw['lifeToPay'];
            $res->socialToPay = $aw['socialToPay'];
            $res->informaticToPay = $aw['informaticToPay'];
            
            $this->_Add($res);
        }
    }

    public function add(Research $res)
    {
        $qr = $this->database->prepare('INSERT INTO
			research(rPlayer, mathLevel, physLevel, chemLevel, bioLevel, mediLevel, econoLevel, psychoLevel, networkLevel, algoLevel, statLevel, naturalTech, lifeTech, socialTech, informaticTech, naturalToPay, lifeToPay, socialToPay, informaticToPay)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $qr->execute(array(
            $res->rPlayer,
            $res->mathLevel,
            $res->physLevel,
            $res->chemLevel,
            $res->bioLevel,
            $res->mediLevel,
            $res->econoLevel,
            $res->psychoLevel,
            $res->networkLevel,
            $res->algoLevel,
            $res->statLevel,
            $res->naturalTech,
            $res->lifeTech,
            $res->socialTech,
            $res->informaticTech,
            $res->naturalToPay,
            $res->lifeToPay,
            $res->socialToPay,
            $res->informaticToPay
        ));

        $this->_Add($res);
    }

    public function save()
    {
        $researches = $this->_Save();

        foreach ($researches as $k => $res) {
            $qr = $this->database->prepare('UPDATE research
				SET	rPlayer = ?,
					mathLevel = ?,
					physLevel = ?,
					chemLevel = ?,
					bioLevel = ?,
					mediLevel = ?,
					econoLevel = ?,
					psychoLevel = ?,
					networkLevel = ?,
					algoLevel = ?,
					statLevel = ?,
					naturalTech = ?,
					lifeTech = ?,
					socialTech = ?,
					informaticTech = ?,
					naturalToPay = ?,
					lifeToPay = ?,
					socialToPay = ?,
					informaticToPay = ?
				WHERE rPlayer = ?');
            $qr->execute(array(
                $res->rPlayer,
                $res->mathLevel,
                $res->physLevel,
                $res->chemLevel,
                $res->bioLevel,
                $res->mediLevel,
                $res->econoLevel,
                $res->psychoLevel,
                $res->networkLevel,
                $res->algoLevel,
                $res->statLevel,
                $res->naturalTech,
                $res->lifeTech,
                $res->socialTech,
                $res->informaticTech,
                $res->naturalToPay,
                $res->lifeToPay,
                $res->socialToPay,
                $res->informaticToPay,
                $res->rPlayer
            ));
        }
    }

    public function update(Research $research, $playerId, $naturalInvest, $lifeInvest, $socialInvest, $informaticInvest)
    {
        # prestige
        $player = $this->playerManager->get($playerId);
        $applyPrestige = false;
        if ($player->rColor == ColorResource::APHERA) {
            $applyPrestige = true;
        }
        // natural technologies
        do {
            if ($research->naturalToPay > $naturalInvest) {
                $research->naturalToPay -= $naturalInvest;
                $naturalInvest = 0;
            } else {
                $naturalInvest -= $research->naturalToPay;
                switch ($research->naturalTech) {
                    case 0:
                        $research->mathLevel++;
                        $levelReached = $research->mathLevel;
                        break;
                    case 1:
                        $research->physLevel++;
                        $levelReached = $research->physLevel;
                        break;
                    case 2:
                        $research->chemLevel++;
                        $levelReached = $research->chemLevel;
                        break;
                    default:
                        $levelReached = 0;
                        throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                }

                $n = new Notification();
                $n->setRPlayer($playerId);
                $n->setTitle($this->researchHelper->getInfo($research->naturalTech, 'name') . ' niveau ' . $levelReached);
                $n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Naturelles</strong> ont fait des avancées en <strong>'
                    . $this->researchHelper->getInfo($research->naturalTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau '
                    . $levelReached . '</strong> dans ce domaine. Félicitations !');
                $this->notificationManager->add($n);
                do {
                    $research->naturalTech = rand(0, 2); // 0, 1 ou 2
                    $tech1 = $research->mathLevel;
                    $tech2 = $research->physLevel;
                    $tech3 = $research->chemLevel;
                    switch ($research->naturalTech) {
                        case 0: $tech1++; break;
                        case 1: $tech2++; break;
                        case 2: $tech3++; break;
                        default:
                            throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                    }
                } while (!$this->researchHelper->isResearchPermit($tech1, $tech2, $tech3));
                $research->naturalToPay = $this->researchHelper->getInfo($research->naturalTech, 'level', $research->getLevel($research->naturalTech) + 1, 'price');
            }
        } while ($naturalInvest > 0);
        // life technologies (en fait ce sont les sciences politiques)
        do {
            if ($research->lifeToPay > $lifeInvest) {
                $research->lifeToPay -= $lifeInvest;
                $lifeInvest = 0;
            } else {
                $lifeInvest -= $research->lifeToPay;
                switch ($research->lifeTech) {
                    case 3:
                        $research->bioLevel++;
                        $levelReached = $research->bioLevel;
                        break;
                    case 4:
                        $research->mediLevel++;
                        $levelReached = $research->mediLevel;
                        break;
                    default:
                        $levelReached = 0;
                        throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                }

                $n = new Notification();
                $n->setRPlayer($playerId);
                $n->setTitle($this->researchHelper->getInfo($research->lifeTech, 'name') . ' niveau ' . $levelReached);
                $n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Politiques</strong> ont fait des avancées en <strong>'
                    . $this->researchHelper->getInfo($research->lifeTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau '
                    . $levelReached . '</strong> dans ce domaine. Félicitations !');
                $this->notificationManager->add($n);

                do {
                    $research->lifeTech = rand(3, 4);
                    $tech1 = $research->bioLevel;
                    $tech2 = $research->mediLevel;
                    switch ($research->lifeTech) {
                        case 3: $tech1++; break;
                        case 4: $tech2++; break;
                        default:
                            throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                    }
                } while (!$this->researchHelper->isResearchPermit($tech1, $tech2));
                $research->lifeToPay = $this->researchHelper->getInfo($research->lifeTech, 'level', $research->getLevel($research->lifeTech) + 1, 'price');
            }
        } while ($lifeInvest > 0);
        // social technologies
        do {
            if ($research->socialToPay > $socialInvest) {
                $research->socialToPay -= $socialInvest;
                $socialInvest = 0;
            } else {
                $socialInvest -= $research->socialToPay;
                switch ($research->socialTech) {
                    case 5:
                        $research->econoLevel++;
                        $levelReached = $research->econoLevel;
                        break;
                    case 6:
                        $research->psychoLevel++;
                        $levelReached = $research->psychoLevel;
                        break;
                    default:
                        $levelReached = 0;
                        throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                }

                $n = new Notification();
                $n->setRPlayer($playerId);
                $n->setTitle($this->researchHelper->getInfo($research->socialTech, 'name') . ' niveau ' . $levelReached);
                $n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Economiques et Sociales</strong> ont fait des avancées en <strong>'
                    . $this->researchHelper->getInfo($research->socialTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau '
                    . $levelReached . '</strong> dans ce domaine. Félicitations !');
                $this->notificationManager->add($n);
                do {
                    $research->socialTech = rand(5, 6);
                    $tech1 = $research->econoLevel;
                    $tech2 = $research->psychoLevel;
                    switch ($research->socialTech) {
                        case 5: $tech1++; break;
                        case 6: $tech2++; break;
                        default:
                            throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                    }
                } while (!$this->researchHelper->isResearchPermit($tech1, $tech2));
                $research->socialToPay = $this->researchHelper->getInfo($research->socialTech, 'level', $research->getLevel($research->socialTech) + 1, 'price');
            }
        } while ($socialInvest > 0);
        // informatic technologies
        do {
            if ($research->informaticToPay > $informaticInvest) {
                $research->informaticToPay -= $informaticInvest;
                $informaticInvest = 0;
            } else {
                $informaticInvest -= $research->informaticToPay;
                switch ($research->informaticTech) {
                    case 7:
                        $research->networkLevel++;
                        $levelReached = $research->networkLevel;
                        break;
                    case 8:
                        $research->algoLevel++;
                        $levelReached = $research->algoLevel;
                        break;
                    case 9:
                        $research->statLevel++;
                        $levelReached = $research->statLevel;
                        break;
                    default:
                        $levelReached = 0;
                        throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                }
                
                $n = new Notification();
                $n->setRPlayer($playerId);
                $n->setTitle($this->researchHelper->getInfo($research->informaticTech, 'name') . ' niveau ' . $levelReached);
                $n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département de l\'<strong>Ingénierie Informatique</strong> ont fait des avancées en <strong>'
                    . $this->researchHelper->getInfo($research->informaticTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau '
                    . $levelReached . '</strong> dans ce domaine. Félicitations !');
                $this->notificationManager->add($n);

                do {
                    $research->informaticTech = rand(7, 9);
                    $tech1 = $research->networkLevel;
                    $tech2 = $research->algoLevel;
                    $tech3 = $research->statLevel;
                    switch ($research->informaticTech) {
                        case 7: $tech1++; break;
                        case 8: $tech2++; break;
                        case 9: $tech3++; break;
                        default:
                            throw new ErrorException('une erreur est survenue lors de la mise à jour des technologies');
                    }
                } while (!$this->researchHelper->isResearchPermit($tech1, $tech2, $tech3));
                $research->informaticToPay = $this->researchHelper->getInfo($research->informaticTech, 'level', $research->getLevel($research->informaticTech) + 1, 'price');
            }
        } while ($informaticInvest > 0);
    }

    public function getResearchList(Research $research)
    {
        // return a stacklist of the researches
        $r = new StackList();
        for ($i = 0; $i < $this->researchQuantity; $i++) {
            $r->append($research->getLevel($i));
        }
        return $r;
    }
    
    /* This will no longer work
    public static function deleteByRPlayer($rPlayer) {
        try {
            $db = Database::getInstance();
            $qr = $db->prepare('DELETE FROM research WHERE rPlayer = ?');
            $qr->execute(array($rPlayer));
            return TRUE;
        } catch(Exception $e) {
            $_SESSION[SERVERSESS]['alert'][] = array($e->getMessage(), $e->getCode());
        }
    }*/
}
