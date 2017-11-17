<?php

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Format;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');

$hasIPresented = false;
foreach ($candidates as $candidate) {
    if ($candidate->rPlayer == $session->get('playerId')) {
        $hasIPresented = true;
        break;
    }
}

echo '<div class="component">';
    echo '<div class="head"></div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<h4>Candidat' . Format::plural($nbCandidate) . ' à l\'élection</h4>';

            echo '<div class="set-item">';
                if ($faction->electionStatement == Color::CAMPAIGN && !$hasIPresented) {
                    echo '<a class="' . (($request->query->get('candidate') === 'create')  ? 'active' : null) . ' item" href="' . APP_ROOT . 'faction/view-election/candidate-create">';
                    echo '<div class="left">';
                    echo '<span>+</span>';
                    echo '</div>';

                    echo '<div class="center">';
                    echo 'Proposer sa candidature';
                    echo '</div>';
                    echo '</a>';
                }

                if ($nbCandidate > 0) {
                    foreach ($candidates as $candidat) {
                        $status = ColorResource::getInfo($faction->id, 'status');

                        echo '<div class="item">';
                        echo '<div class="left">';
                        echo '<img src="' . MEDIA . 'avatar/small/' . $candidat->avatar . '.png" alt="' . $candidat->name . '" />';
                        echo '</div>';

                        echo '<div class="center">';
                        echo '<strong>' . $candidat->name . '</strong>';
                        echo $status[$candidat->status - 1];
                        echo '</div>';

                        echo '<div class="right">';
                        echo '<a class="' . (($request->query->get('candidate') === $candidat->id) ? 'active' : null) . '" href="' . APP_ROOT . 'faction/view-election/candidate-' . $candidat->id . '"></a>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>';
                } else {
                    echo '</div>';
                    echo '<p>Il n\'y a aucun candidat pour l\'instant.</p>';
                }
        echo '</div>';
    echo '</div>';
echo '</div>';
