<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use App\Classes\Library\Format;
use App\Classes\Library\Parser;
use App\Classes\Library\Utils;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Manager\CreditTransactionManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\CreditTransaction;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SendCreditsToPlayer extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $sender,
		CreditTransactionManager $creditTransactionManager,
		NotificationManager $notificationManager,
		Parser $parser,
		PlayerManager $playerManager
	): Response {
		$name = $request->request->get('name');
		$credit = $request->request->getInt('quantity');
		$text = $request->request->get('text');

		if (null === $name || 0 === $credit) {
			throw new BadRequestHttpException('Le nom ou le montant est invalide');
		}

		if (500 < strlen($text)) {
			throw new BadRequestHttpException('Le message ne doit pas dépasser 500 caractères');
		}

		if (null === ($receiver = $playerManager->getByName($name))) {
			throw new NotFoundHttpException('Le bénéficiaire renseigné n\'existe pas');
		}

		if ($receiver->getId() === $sender->getId()) {
			return $this->redirectToRoute('financial_transfers');
		}

		if ($credit > $sender->getCredit()) {
			throw new BadRequestHttpException('Vous ne disposez pas du montant nécessaire');
		}

		// input protection
		$name = $parser->protect($name);
		$text = $parser->parse($text);

		$playerManager->decreaseCredit($sender, $credit);
		$playerManager->increaseCredit($receiver, $credit);

		# create the transaction
		$ct = new CreditTransaction();
		$ct->rSender = $sender->getId();
		$ct->type = CreditTransaction::TYP_PLAYER;
		$ct->rReceiver = $receiver->id;
		$ct->amount = $credit;
		$ct->dTransaction = Utils::now();
		$ct->comment = $text;
		$creditTransactionManager->add($ct);

		$n = new Notification();
		$n->setRPlayer($receiver->id);
		$n->setTitle('Réception de crédits');
		$n->addBeg();
		$n->addLnk('embassy/player-' . $sender->getId(), $sender->getName());
		$n->addTxt(' vous a envoyé des crédits');
		if ($text !== '') {
			$n->addTxt(' avec le message suivant : ')->addBrk()->addTxt('"' . $text . '"');
		} else {
			$n->addTxt('.');
		}
		$n->addBoxResource('credit', Format::numberFormat($credit), ($credit == 1 ? 'crédit reçu' : 'crédits reçus'), $this->getParameter('media'));
		$n->addEnd();
		$notificationManager->add($n);

		$this->addFlash('success', 'Crédits envoyés');

		return $this->redirectToRoute('financial_transfers');
	}
}
