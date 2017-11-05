<?php

use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Exception\ErrorException;

if (($id = $request->query->get('id')) === null) {
    throw new ErrorException('Aucun identifiant renseigné');
}
if (($news = $newsManager->get($id)) === null) {
    throw new ErrorException('La nouvelle demandée n\'existe pas');
}

$type = [
    \Asylamba\Modules\Hermes\Model\Press\MilitaryNews::class => 'Militaire',
    \Asylamba\Modules\Hermes\Model\Press\PoliticNews::class => 'Politique',
    \Asylamba\Modules\Hermes\Model\Press\TradeNews::class => 'Commerce'
][get_class($news)];

?>
<div class="component">
    <div class="head">
        <h1>Article</h1>
    </div>
    <div class="fix-body">
        <div class="body">
            <div class="number-box">
                <span class="label">Rubrique</span>
                <span class="value"><?= $type ?></span>
            </div>
            <div class="number-box">
                <span class="label">Créé le</span>
                <span class="value"><?= Chronos::transform($news->getCreatedAt()) ?></span>
            </div>
            <?php if ($news->getCreatedAt() != $news->getUpdatedAt()) { ?>
                <div class="number-box">
                    <span class="label">Mis à jour le</span>
                    <span class="value"><?= Chronos::transform($news->getUpdatedAt()) ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="component size2 news">
    <div class="head skin-2">
        <h2>Contenu</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <p class="long-info"><?= $news->getContent() ?></p>
        </div>
    </div>
</div>
<div class="component news">
    <div class="head skin-2">
        <h2>Commentaires</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            
        </div>
    </div>
</div>