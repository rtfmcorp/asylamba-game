<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');
$parser = $this->getContainer()->get('parser');

include COMPONENT . 'project/infos.php';
?>
<div class="component size2 new-message">
    <div class="head skin-2">
        <h2>Reporter un bug</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <form action="<?= Format::actionBuilder('createbug', $sessionToken); ?>" method="post">
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

                <p><button>Reporter le bug</button></p>
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
                Un bug est un fonctionnement anormal du jeu.
                <strong>Il ne constitue pas une nouvelle fonctionnalité</strong>, simplement l'anomalie d'une fonctionnalité existante.
            </p>
            <p class='long-info'>
                Si une fonctionnalité ou une partie du gameplay ne vous semble pas adaptée, ou perfectible, cela n'est pas un bug, mais une <strong>évolution</strong> !
                Vous pourrez donc la proposer via le formulaire correspondant.
            </p>
            <p class='long-info'>
                Pour <strong>renseigner efficacement un bug</strong>, et ainsi aider l'équipe de développer à le corriger rapidement,
                il est important d'être précis dans votre description du problème rencontré.
                Indiquez les étapes de reproduction du bug (où vous vous trouviez, quelles actions vous avez effectué, quel message d'erreur avez-vous reçu...).
            </p>
            <p class='long-info'>
                Si certaines informations vous semblent utiles à reporter mais sont sensibles (détails sur vos flottes, sur vos opérations...),
                Demandez simplement à être recontacté pour plus de détails. Un développeur viendra vers vous pour ces informations.
                <strong>La liste des développeurs est renseignée</strong> dans le gestionnaire de projet, afin que vous ne puissiez pas voir ces informations usurpées.
            </p>
        </div>
    </div>
</div>