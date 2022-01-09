<?php

use App\Classes\Exception\ErrorException;

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$actionPath = $container->getParameter('action');

# démarre la redirection standard vers la page précédente
$response->redirect($session->getLastHistory());

if ($request->query->has('sftr')) {
	$session->add('sftr', $request->query->get('sftr'));
}

if ($request->query->has('token') && $session->get('token') === $request->query->get('token')) {
	switch ($request->query->get('a')) {
		# GENERAL
		case 'switchbase': 				include $actionPath . 'common/switchBase.php'; break;
		case 'switchparams':			include $actionPath . 'common/switchParams.php'; break;
		case 'sendsponsorshipemail': 	include $actionPath . 'common/sendSponsorshipEmail.php'; break;
		case 'discordrequest': 			include $actionPath . 'common/discordRequest.php'; break;

		# ATHENA
		case 'updateinvest': 			include $actionPath . 'athena/general/updateInvest.php'; break;
		case 'switchdockmode':			include $actionPath . 'athena/general/switchDockMode.php'; break;
		case 'createschoolclass':		include $actionPath . 'athena/general/createSchoolClass.php'; break;
		case 'giveresource':			include $actionPath . 'athena/general/giveResource.php'; break;
		case 'giveships':				include $actionPath . 'athena/general/giveShips.php'; break;
		case 'renamebase':				include $actionPath . 'athena/general/renameBase.php'; break;
		case 'changebasetype':			include $actionPath . 'athena/general/changeBaseType.php'; break;
		case 'leavebase':				include $actionPath . 'athena/general/leaveBase.php'; break;

		case 'buildbuilding':			include $actionPath . 'athena/building/build.php'; break;
		case 'dequeuebuilding':			include $actionPath . 'athena/building/dequeue.php'; break;

		case 'buildship':				include $actionPath . 'athena/ship/build.php'; break;
		case 'dequeueship':				include $actionPath . 'athena/ship/dequeue.php'; break;
		case 'recycleship':				include $actionPath . 'athena/ship/recycle.php'; break;

		case 'proposeroute':			include $actionPath . 'athena/route/propose.php'; break;
		case 'acceptroute':				include $actionPath . 'athena/route/accept.php'; break;
		case 'refuseroute':				include $actionPath . 'athena/route/refuse.php'; break;
		case 'cancelroute':				include $actionPath . 'athena/route/cancel.php'; break;
		case 'deleteroute':				include $actionPath . 'athena/route/delete.php'; break;

		case 'proposetransaction': 		include $actionPath . 'athena/transaction/propose.php'; break;
		case 'accepttransaction':		include $actionPath . 'athena/transaction/accept.php'; break;
		case 'canceltransaction':		include $actionPath . 'athena/transaction/cancel.php'; break;

		case 'createmission':			include $actionPath . 'athena/recycling/createMission.php'; break;
		case 'cancelmission':			include $actionPath . 'athena/recycling/cancelMission.php'; break;
		case 'addtomission': 			include $actionPath . 'athena/recycling/addToMission.php'; break;

		# HERMES
		case 'startconversation': 		include $actionPath . 'hermes/conversation/start.php'; break;
		case 'writeconversation': 		include $actionPath . 'hermes/conversation/write.php'; break;
		case 'leaveconversation':		include $actionPath . 'hermes/conversation/leave.php'; break;
		case 'adduserconversation':		include $actionPath . 'hermes/conversation/addUser.php'; break;
		case 'updatedisplayconversation':include $actionPath .'hermes/conversation/updateDisplay.php'; break;
		case 'updatetitleconversation':	include $actionPath . 'hermes/conversation/updateTitle.php'; break;
		case 'writeofficialconversation':include $actionPath .'hermes/conversation/writeOfficial.php'; break;
		case 'writefactionconversation':include $actionPath . 'hermes/conversation/writeFaction.php'; break;
		
		case 'readallnotif': 			include $actionPath . 'hermes/notification/readAll.php'; break;
		case 'deleteallnotif':			include $actionPath . 'hermes/notification/deleteAll.php'; break;
		case 'deletenotif':				include $actionPath . 'hermes/notification/delete.php'; break;
		case 'archivenotif': 			include $actionPath . 'hermes/notification/archive.php'; break;

		case 'writeroadmap':			include $actionPath . 'hermes/roadmap/write.php'; break;

		# PROMETHEE
		case 'buildtechno':				include $actionPath . 'promethee/technology/build.php'; break;
		case 'dequeuetechno':			include $actionPath . 'promethee/technology/dequeue.php'; break;

		# ZEUS
		case 'searchplayer':			include $actionPath . 'zeus/player/searchPlayer.php'; break;
		case 'updateuniinvest':			include $actionPath . 'zeus/player/updateUniInvest.php'; break;
		case 'disconnect': 				include $actionPath . 'zeus/player/disconnect.php'; break;
		case 'sendcredit': 				include $actionPath . 'zeus/player/sendCredit.php'; break;
		case 'sendcredittofaction':		include $actionPath . 'zeus/player/sendCreditToFaction.php'; break;
		case 'sendcreditfromfaction':	include $actionPath . 'zeus/player/sendCreditFromFaction.php'; break;
		case 'abandonserver': 			include $actionPath . 'zeus/player/abandonServer.php'; break;
		case 'switchadvertisement':		include $actionPath . 'zeus/player/switchAdvertisement.php'; break;

		case 'validatestep':			include $actionPath . 'zeus/tutorial/validateStep.php'; break;

		# ARTEMIS
		case 'spy':						include $actionPath . 'artemis/spy.php'; break;
		case 'deletespyreport':			include $actionPath . 'artemis/delete.php'; break;
		case 'deleteallspyreport':		include $actionPath . 'artemis/deleteAll.php'; break;

		# ARES
		case 'archivereport':			include $actionPath . 'ares/report/archive.php'; break;
		case 'deletereport':			include $actionPath . 'ares/report/delete.php'; break;
		case 'deleteallreport':			include $actionPath . 'ares/report/deleteAll.php'; break;
		
		case 'movefleet':				include $actionPath . 'ares/fleet/move.php'; break;
		case 'loot':					include $actionPath . 'ares/fleet/loot.php'; break;
		case 'colonize':				include $actionPath . 'ares/fleet/colonize.php'; break;
		case 'conquer':					include $actionPath . 'ares/fleet/conquer.php'; break;
		case 'cancelmove':				include $actionPath . 'ares/fleet/cancel.php'; break;

		case 'affectcommander':			include $actionPath . 'ares/commander/affect.php'; break;
		case 'putcommanderinschool':	include $actionPath . 'ares/commander/putInSchool.php'; break;
		case 'updatenamecommander':		include $actionPath . 'ares/commander/updateName.php'; break;
		case 'emptycommander':			include $actionPath . 'ares/commander/empty.php'; break;
		case 'firecommander':			include $actionPath . 'ares/commander/fire.php'; break;
		case 'changeline':				include $actionPath . 'ares/commander/changeLine.php'; break;

		# DEMETER
		case 'writemessageforum':		include $actionPath . 'demeter/message/write.php'; break;
		case 'movetopicforum':			include $actionPath . 'demeter/topic/move.php'; break;
		case 'closetopicforum':			include $actionPath . 'demeter/topic/close.php'; break;
		case 'uptopicforum':			include $actionPath . 'demeter/topic/up.php'; break;
		case 'archivetopicforum':		include $actionPath . 'demeter/topic/archive.php'; break;
		case 'editmessageforum':		include $actionPath . 'demeter/message/edit.php'; break;
		case 'createtopicforum':		include $actionPath . 'demeter/topic/createTopic.php'; break;

		case 'writenews':				include $actionPath . 'demeter/news/write.php'; break;
		case 'editnews':				include $actionPath . 'demeter/news/edit.php'; break;
		case 'pinnews':					include $actionPath . 'demeter/news/pin.php'; break;
		case 'deletenews':				include $actionPath . 'demeter/news/delete.php'; break;

		case 'postulate':				include $actionPath . 'demeter/election/postulate.php'; break;
		case 'makeacoup':				include $actionPath . 'demeter/election/makeACoup.php'; break;
		case 'vote':					include $actionPath . 'demeter/election/vote.php'; break;
		case 'choosegovernment':		include $actionPath . 'demeter/election/chooseGovernment.php'; break;
		case 'fireminister':			include $actionPath . 'demeter/election/fire.php'; break;
		case 'resign':					include $actionPath . 'demeter/election/resign.php'; break;
		case 'abdicate':				include $actionPath . 'demeter/election/abdicate.php'; break;

		case 'votelaw':					include $actionPath . 'demeter/law/vote.php'; break;
		case 'createlaw':				include $actionPath . 'demeter/law/createLaw.php'; break;

		case 'updatefactiondesc':		include $actionPath . 'demeter/updateFactionDesc.php'; break;

		case 'donate':					include $actionPath . 'demeter/donate.php'; break;

		default :
			throw new ErrorException('action inconnue ou non-référencée');
	}
} elseif ($request->query->get('a') == 'switchbase') {
	# action sans token
	include $actionPath . 'common/switchBase.php';
} else {
	throw new ErrorException('votre token CSRF a expiré');
}
