<?php
# commanderDetail component
# in ares package

# affichage le détail d'un commandant

# require
    # {commander}		commander_commanderDetail

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Game;

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');

echo '<div class="component">';
    echo '<div class="head skin-1"></div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            if ($commander_commanderDetail->statement == Commander::AFFECTED) {
                echo '<div class="tool">';
                echo '<span><a href="' . Format::actionBuilder('emptycommander', $sessionToken, ['id' => $commander_commanderDetail->id]) . '">Retirer tous les vaisseaux</a></span>';
                echo '<span><a href="' . Format::actionBuilder('firecommander', $sessionToken, ['id' => $commander_commanderDetail->id]) . '" class="hb lt" title="licencier l\'officier">&#215;</a></span>';
                echo '<span><a href="' . Format::actionBuilder('affectcommander', $sessionToken, ['id' => $commander_commanderDetail->id]) . '" class="hb lt" title="remettre dans l\'école">E</a></span>';
                echo '</div>';
            }

            echo '<div class="number-box">';
                echo '<span class="label">État de l\'officier</span>';
                if ($commander_commanderDetail->statement == Commander::INSCHOOL) {
                    echo '<span class="value">À l\'école</span>';
                } elseif ($commander_commanderDetail->statement == Commander::AFFECTED) {
                    echo '<span class="value">À quai</span>';
                } elseif ($commander_commanderDetail->statement == Commander::MOVING) {
                    echo '<span class="value">En mission</span>';
                } else {
                    echo '<span class="value">Tombé au combat</span>';
                }
            echo '</div>';

            if ($commander_commanderDetail->statement == Commander::MOVING) {
                echo '<div class="number-box">';
                echo '<span class="label">Mission</span>';
                switch ($commander_commanderDetail->getTravelType()) {
                            case Commander::MOVE:
                                    echo '<span class="value">Déplacement</span>';
                                echo '</div>';
                                echo '<div class="number-box">';
                                    echo '<span class="label">Vers</span>';
                                    echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
                                break;
                            case Commander::LOOT:
                                    echo '<span class="value">Pillage</span>';
                                echo '</div>';
                                echo '<div class="number-box">';
                                    echo '<span class="label">Cible</span>';
                                    echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
                                break;
                            case Commander::COLO:
                                    echo '<span class="value">Colonisation</span>';
                                echo '</div>';
                                echo '<div class="number-box">';
                                    echo '<span class="label">Cible</span>';
                                    echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
                                break;
                            case Commander::BACK:
                                    echo '<span class="value">Retour victorieux</span>';
                                echo '</div>';
                                echo '<div class="number-box">';
                                    echo '<span class="label">Ressources transportées</span>';
                                    echo '<span class="value">' . Format::numberFormat($commander_commanderDetail->getResources()) . '</span>';
                                break;
                            default: break;
                        }
                echo '</div>';
            }

            echo '<hr />';

            echo '<div class="number-box grey">';
                echo '<span class="label">Nom</span>';
                echo '<span class="value">' . $commander_commanderDetail->getName() . '</span>';
            echo '</div>';
            echo '<div class="number-box">';
                echo '<span class="label">Victoire' . Format::addPlural($commander_commanderDetail->getPalmares()) . '</span>';
                echo '<span class="value">' . $commander_commanderDetail->getPalmares() . '</span>';
            echo '</div>';
            echo '<div class="number-box grey">';
                echo '<span class="label">Grade</span>';
                echo '<span class="value">' . CommanderResources::getInfo($commander_commanderDetail->level, 'grade') . '</span>';
            echo '</div>';

            if (in_array($commander_commanderDetail->getStatement(), [Commander::AFFECTED, Commander::MOVING, Commander::INSCHOOL])) {
                echo '<div class="number-box grey">';
                echo '<span class="label">Expérience</span>';
                $expToLvlUp = $commanderManager->experienceToLevelUp($commander_commanderDetail);
                $percent = Format::percent($commander_commanderDetail->getExperience() - ($expToLvlUp / 2), $expToLvlUp - ($expToLvlUp / 2));
                echo '<span class="value">' . Format::numberFormat($commander_commanderDetail->getExperience()) . ' / ' . Format::numberFormat($commanderManager->experienceToLevelUp($commander_commanderDetail)) . '</span>';
                echo '<span title="' . $percent . ' %" class="progress-bar hb bl">';
                echo '<span class="content" style="width: ' . $percent . '%;"></span>';
                echo '</span>';
                echo '</div>';
            }

            echo '<hr />';

            echo '<div class="number-box grey">';
                echo '<span class="label">Salaire de l\'officier</span>';
                echo '<span class="value">' . Format::numberFormat($commander_commanderDetail->level * Commander::LVLINCOMECOMMANDER) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits"></span>';
            echo '</div>';

            echo '<div class="number-box grey">';
                echo '<span class="label">Frais d\'entretien des vaisseaux</span>';
                echo '<span class="value">' . Format::numberFormat(Game::getFleetCost($commander_commanderDetail->getNbrShipByType())) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits"></span>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';
