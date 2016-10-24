<?php
# background paralax
echo '<div id="background-paralax" class="message"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
        # liste des conv's
	$display = CTR::$get->equal('mode', ConversationUser::CS_ARCHIVED)
		? ConversationUser::CS_ARCHIVED
		: ConversationUser::CS_DISPLAY;

	# chargement de toutes les conversations
	ASM::$cvm->newSession();
	ASM::$cvm->load(
		['cu.rPlayer' => CTR::$data->get('playerId'), 'cu.convStatement' => $display],
		['c.dLastMessage', 'DESC'],
		[0, Conversation::CONVERSATION_BY_PAGE]
	);

	$conversation_listmode = FALSE;

	include COMPONENT . 'conversation/list.php';

	if (CTR::$get->exist('conversation')) {
		if (CTR::$get->equal('conversation', 'new')) {
			include COMPONENT . 'conversation/create.php';
		} else {
			# chargement d'une conversation
			ASM::$cvm->newSession();
			ASM::$cvm->load(
				['c.id' => CTR::$get->get('conversation'), 'cu.rPlayer' => CTR::$data->get('playerId')]
			);

			if (ASM::$cvm->size() == 1) {
				# chargement des infos d'une conversation
				ASM::$cum->newSession();
				ASM::$cum->load(['c.rConversation' => CTR::$get->get('conversation')]);

				# mis à jour de l'heure de la dernière vue
				for ($i = 0; $i < ASM::$cum->size(); $i++) { 
					if (ASM::$cum->get($i)->rPlayer == CTR::$data->get('playerId')) {
						$dPlayerLastMessage = ASM::$cum->get($i)->dLastView;
						$currentUser = ASM::$cum->get($i);

						ASM::$cum->get($i)->dLastView = Utils::now();
					}
				}

				# chargement des messages
				ASM::$cme->newSession();
				ASM::$cme->load(
					['c.rConversation' => CTR::$get->get('conversation')],
					['c.dCreation', 'DESC'],
					[0, ConversationMessage::MESSAGE_BY_PAGE]
				);

				$message_listmode = FALSE;

				include COMPONENT . 'conversation/messages.php';
				include COMPONENT . 'conversation/manage.php';
			} else {
				CTR::redirect('message');
			}
		}
	} else {
		include COMPONENT . 'conversation/new.php';
	}

	# NOTIFICATION
	$S_NTM1 = ASM::$ntm->getCurrentSession();

	$C_NTM1 = ASM::$ntm->newSession();
	ASM::$ntm->load(
		array('rPlayer' => CTR::$data->get('playerId'), 'archived' => 0),
		array('dSending', 'DESC'),
		array(0, 50)
	);
	include COMPONENT . 'notif/last.php';

	$C_NTM2 = ASM::$ntm->newSession();
	ASM::$ntm->load(
		array('rPlayer' => CTR::$data->get('playerId'), 'archived' => 1),
		array('dSending', 'DESC'),
		array(0, 50)
	);

	if (ASM::$ntm->size() > 0) {
		include COMPONENT . 'notif/archived.php';
	}

	ASM::$ntm->changeSession($S_NTM1);
echo '</div>';