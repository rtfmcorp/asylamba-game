<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use App\Classes\Library\Chronos;
use App\Classes\Library\Format;
use App\Classes\Library\Utils;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Promethee\Manager\TechnologyManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneratorController extends AbstractController
{
	public function __invoke(
		Request $request,
		OrbitalBaseHelper $orbitalBaseHelper,
		TechnologyManager $technologyManager,
		int $buildingResourceRefund,
	): Response {
		$session = $request->getSession();

		$q = '';
		$b = array('', '', '', '', '', '', '', '', '', '');
		$realSizeQueue = 0;

		for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
			$name 		= ucfirst($orbitalBaseHelper->getBuildingInfo($i, 'name'));
			$aLevel[$i] = intval(call_user_func(array($ob_generator, 'getLevel' . $name)));
			$rLevel[$i] = intval(call_user_func(array($ob_generator, 'getReal' . $name . 'Level')));
		}

		$q .= '<div class="queue">';
		$nextTime = 0;
		$nextTotalTime = 0;

		for ($i = 0; $i < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::GENERATOR, 'level', $ob_generator->levelGenerator, 'nbQueues'); $i++) {
			if (isset($ob_generator->buildingQueues[$i])) {
				$qe = $ob_generator->buildingQueues[$i];

				$realSizeQueue++;
				$nextTime = Utils::interval(Utils::now(), $qe->dEnd, 's');
				$nextTotalTime += $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time');

				$q .= '<div class="item ' . (($realSizeQueue > 1) ? 'active' : '') . ' progress" data-progress-output="lite" data-progress-current-time="' . $nextTime . '" data-progress-total-time="' . $nextTotalTime . '">';
				$q .= '<a href="' . Format::actionBuilder('dequeuebuilding', $sessionToken, ['baseid' => $ob_generator->getId(), 'building' => $qe->buildingNumber]) . '"' .
					'class="button hb lt" title="annuler la construction (attention, vous ne récupérerez que ' . $buildingResourceRefund * 100 . '% du montant investi)">×</a>';
				$q .= '<img class="picto" src="' . $mediaPath . 'orbitalbase/' . $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
				$q .= '<strong>';
				$q .= $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'frenchName');
				$q .= ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
				$q .= '</strong>';
				if ($realSizeQueue > 1) {
					$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';
					$q .= '<span class="progress-container"></span>';
				} else {
					$q .= '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';

					$q .= '<span class="progress-container">';
					$q .= '<span style="width: ' . Format::percent($nextTotalTime - $nextTime, $nextTotalTime) . '%;" class="progress-bar">';
					$q .='</span>';
					$q .= '</span>';
				}
				$q .= '</div>';
			} else {
				$q .= '<div class="item empty">';
				$q .= '<span class="picto"></span>';
				$q .= '<strong>Emplacement libre</strong>';
				$q .= '<span class="progress-container"></span>';
				$q .= '</div>';
			}
		}
		$q .= '</div>';


# building
		$technology = $technologyManager->getPlayerTechnology($session->get('playerId'));
		for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) {
			$level = $aLevel[$i];
			$nextLevel =  $rLevel[$i] + 1;

			$b[$i] .= ($rLevel[$i]) ? '<div class="build-item">' : '<div class="build-item disable">';
			$b[$i] .= '<div class="name">';
			$b[$i] .= '<img src="' . $mediaPath . 'orbitalbase/' . $orbitalBaseHelper->getBuildingInfo($i, 'imageLink') . '.png" alt="" />';
			$b[$i] .= '<strong>' . $orbitalBaseHelper->getBuildingInfo($i, 'frenchName') . '</strong>';
			if ($level != 0) {
				$b[$i] .= '<span class="level hb lt" title="niveau actuel">' . $level . '</span>';
			}
			$b[$i] .= '<a href="#" class="addInfoPanel info hb lt" title="plus d\'info" data-building-id="' . $i . '" data-info-type="building" data-building-current-level="' . $level . '">+</a>';
			$b[$i] .= '</div>';

			$price = Format::numberFormat($orbitalBaseHelper->getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice'), -1) . ' <img src="' .  $mediaPath. 'resources/resource.png" alt="ressources" class="icon-color" />';
			$time  = Chronos::secondToFormat($orbitalBaseHelper->getBuildingInfo($i, 'level', ($nextLevel), 'time'), 'lite') . ' <img src="' .  $mediaPath. 'resources/time.png" alt="relèves" class="icon-color" />';

			if (($answer = $orbitalBaseHelper->haveRights($i, $nextLevel, 'buildingTree', $ob_generator)) !== TRUE) {
				if ($answer == 'niveau maximum atteint') {
					$b[$i] .= '<span class="button disable">';
					$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= 'niveau maximum atteint';
					$b[$i] .= '</span>';
					$b[$i] .= '</span>';
				} else {
					$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
					$b[$i] .= '<span class="text">';
					$b[$i] .= 'construction impossible<br/>';
					$b[$i] .= $price . ' | ' . $time;
					$b[$i] .= '</span>';
					$b[$i] .= '</span>';
				}
			} elseif (($answer = $orbitalBaseHelper->haveRights($i, $nextLevel, 'techno', $technology)) !== TRUE) {
				$b[$i] .= '<span class="button disable hb lt" title="' . $answer . '">';
				$b[$i] .= '<span class="text">';
				$b[$i] .= 'construction impossible<br/>';
				$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			} elseif (!$orbitalBaseHelper->haveRights(OrbitalBaseResource::GENERATOR, $aLevel[OrbitalBaseResource::GENERATOR], 'queue', $realSizeQueue)) {
				$b[$i] .= '<span class="button disable hb lt" title="file de construction pleine, revenez dans un moment">';
				$b[$i] .= '<span class="text">';
				$b[$i] .= 'construction impossible<br/>';
				$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			} elseif (!$orbitalBaseHelper->haveRights($i, $nextLevel, 'resource', $ob_generator->getResourcesStorage())) {
				$missingResource = $orbitalBaseHelper->getBuildingInfo($i, 'level', ($nextLevel), 'resourcePrice') - $ob_generator->getResourcesStorage();
				$b[$i] .= '<span class="button disable hb lt" title="pas assez de ressources, il manque ' . Format::numberFormat($missingResource) . ' ressource' . Format::plural($missingResource) . '">';
				$b[$i] .= '<span class="text">';
				$b[$i] .= 'construction impossible<br/>';
				$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
				$b[$i] .= '</span>';
			} else {
				$b[$i] .= '<a class="button" href="' . Format::actionBuilder('buildbuilding', $sessionToken, ['baseid' => $ob_generator->getId(), 'building' => $i]) . '">';
				$b[$i] .= '<span class="text">';
				$b[$i] .= 'augmenter vers le niveau ' . $nextLevel . '<br/>';
				$b[$i] .= $price . ' | ' . $time;
				$b[$i] .= '</span>';
				$b[$i] .= '</a>';
			}
			$b[$i] .= '</div>';
		}
		return $this->render('pages/athena/generator.html.twig', [
			'buildings' => $b,
		]);
	}
}
