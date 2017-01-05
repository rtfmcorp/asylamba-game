<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Demeter\Resource\ColorResource;

echo '<h1>Création automatisée de statistiques</h1>';

echo '<h2>Joueurs</h2>';

echo '<p>';
	echo 'Joueurs actifs<br />';
	echo '<strong>' . Format::number(PlayerManager::count(array('statement' => Player::ACTIVE))) . '</strong>';
echo '</p>';

echo '<p>';
	echo 'Joueurs inscrits<br />';
	echo '<strong>' . Format::number(PlayerManager::count(array('statement' => array(Player::ACTIVE, Player::INACTIVE)))) . '</strong>';
echo '</p>';

$S_PAM = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);
ASM::$pam->load([], ['credit', 'DESC'], [0, 3]);

echo '<p>';
	echo 'Joueurs les plus riches';
	for ($i = 0; $i < ASM::$pam->size(); $i++) {
		$p = ASM::$pam->get($i);

		echo '<br />';
		echo '<strong>' . ColorResource::getInfo($p->rColor, 'status')[$p->status] . ' ' . $p->name . ' de ' . ColorResource::getInfo($p->rColor, 'popularName') . '</strong>';
		echo ' (' . Format::number($p->credit) . ' crédits)';
	}
echo '</p>';

ASM::$pam->changeSession($S_PAM);

echo '<h2>Commerce</h2>';

echo '<p>nombre de RC</p>';
echo '<p>max RC</p>';

echo '<p>CommercialShipping</p>';
echo '<p>CreditTransaction</p>';
echo '<p>transaction</p>';

echo '<h2>Politique / Factions</h2>';
echo '<p>Candidate</p>';
echo '<p>Color</p>';
echo '<p>Election</p>';
echo '<p>Law</p>';
echo '<p>vote</p>';
echo '<p>votelaw</p>';

echo '<h2>Guerre</h2>';
echo '<p>Commandant</p>';
echo '<p>report</p>';
echo '<p>spyreport</p>';



echo '<h2>Interactions</h2>';
echo '<p>ForumMessage</p>';
echo '<p>ForumTopic</p>';
echo '<p>Message</p>';
echo '<p>Notifications</p>';

echo '<h2>Bases / Construction</h2>';
echo '<p>ob</p>';
echo '<p>vaisseau</p>';
echo '<p>batiment</p>';
echo '<p>tech</p>';
echo '<p>recycl</p>';
echo '<p>recycllog</p>';