<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');
$parser = $this->getContainer()->get('parser');

include COMPONENT . 'project/infos.php';
?>
<div class="component size2 new-message">
    <div class="head skin-2">
        <h2>Proposer une évolution</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <form action="<?= Format::actionBuilder('createevo', $sessionToken); ?>" method="post">
                <p>
                    Titre 
                </p>
                
                <p class="input input-text">
                    <input autocomplete="off" class="ac_input" name="title" placeholder="Titre" type="text"/>
                </p>

                <p>
                    Description
                </p>
                <p class="input input-area">
                    <span class="wysiwyg" data-id="new-bug-wysiwyg">
                        <?= $parser->getToolbar(); ?>
                        <textarea name="description" id="new-bug-wysiwyg"></textarea>
                    </span>
                </p>

                <p><button>Envoyer la proposition</button></p>
            </form>
        </div>
    </div>
</div>
<div class="component size1">
    <div class='head skin-2'>
        <h2>Procédure</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <p class="long-info">
                Une évolution est une proposition d'amélioration ou de création de fonctionnalités.
                N'importe quel membre de la communauté peut créer une proposition.
                Les propositions peuvent ensuite être discutées par commentaires, éditées et même votées.
                <strong>Cependant, l'aval de l'équipe propriétaire du jeu est nécessaire</strong> pour lancer les développements d'une évolution.
            </p>
            <p class='long-info'>
                Une fois une évolution acceptée, un développeur de la communauté open-source prendra en charge le développement de la fonctionnalité
                et soumettra ensuite le résultat à l'équipe de développement pour une revue de code.
                Ensuite, les nouveautés seront <strong>testées en pré-production</strong> avant d'être déployées sur une partie en cours.
            </p>
            <p class='long-info'>
                <strong>Une évolution remettant profondément en cause le modèle de données du jeu</strong>
                ne pourra dans la plupart des cas être déployée en cours de partie.
                Cela reste à l'appréciation de l'ensemble de l'équipe de développement.
                Une telle évolution sera livrée aux joueurs lors de la partie suivante.
            </p>
        </div>
    </div>
</div>