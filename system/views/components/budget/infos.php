<?php
use Asylamba\Classes\Library\Format;
?>
<div class="component profil">
	<div class="head skin-1">
		<h1>Financement</h1>
	</div>
	<div class="fix-body">
		<div class="body">
			<h4>Contribuez à la vie de la communauté !</h4>
			<p>Actuellement, le jeu est maintenu et entretenu par sa communauté. 
            Les serveurs permettant de le faire fonctionner sont en effet à la charge d\'un contributeur open-source, 
            qui les paie chaque mois pour répondre aux besoins des asylambiens. 
            Cela est fait de manière tout à fait bénévole ! 
            Certains joueurs s\'investissent également et contribuent, en effectuant un don de la valeur qu\'ils souhaitent pour participer aux frais des machines</p>
            <p>Si le jeu vous plait, et que vous voulez également faire partie des contributeurs, n\'hésitez pas à faire un don !</p>

			<h4>Quelques précisions</h4>
			<p>Dans un soucis d\'équité, il nous apparaît naturel que les personnes effectuant un don ne seront pas avantagées par rapport aux autres. 
            Le don doit être totalement désintéressé et n\'apporte aucun pouvoir supplémentaire au sein de la communauté.</p>
            <p>En retour, nous offrons toute notre gratitude, et faisons au mieux pour faire évoluer le jeu, pour le rendre de plus en plus passionnant !</p>
            <div class="number-box <?= ($playerSum === 0) ? 'grey' : '' ?>">
                <span class="label">Total de vos donations</span>
                <span class="value"> <?= Format::number($playerSum / 100, 2); ?> €</span>
            </div>
        </div>
	</div>
</div>