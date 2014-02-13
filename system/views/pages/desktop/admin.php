<?php
# bases loading
if (CTR::$data->get('playerInfo')->get('admin') == FALSE) {
	header('Location: ' . APP_ROOT . 'profil');
	exit();
}

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'basesElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# admin component
	include COMPONENT . 'apollon/admin/adminNav.php';

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'main') {
		# main admin
	} elseif (CTR::$get->get('view') == 'message') {
		# main message
		include COMPONENT . 'apollon/message/newOfficialMessage.php';
	} elseif (CTR::$get->get('view') == 'bugtracker') {
		$S_BTM1 = ASM::$btm->getCurrentSession();
		ASM::$btm->newSession();
		ASM::$btm->load(array('statement' => array(BugTracker::ST_WAITING, BugTracker::ST_ARCHIVED)), array('dSending', 'DESC'));
	
		$bugtracker = array();
		$waitingBugQuantity = 0;
		$archivedBugQuantity = 0;
		$listName = 'catégorie invalide';

		if (!CTR::$get->exist('type')) {
			# all the bugreports
			$listName = 'Tous les rapports d\'erreur';
			for ($i = 0; $i < ASM::$btm->size(); $i++) {
				$bugtracker[] = ASM::$btm->get($i);
				
				if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
					$waitingBugQuantity++;
				} else {
					$archivedBugQuantity++;
				}
			}
		} else {
			# only a special type of bugreports
			switch(CTR::$get->get('type')) {
				case BugTracker::TYPE_BUG:
					$listName = 'Bugs & erreurs';
					for ($i = 0; $i < ASM::$btm->size(); $i++) {
						if (ASM::$btm->get($i)->type == BugTracker::TYPE_BUG) {
							$bugtracker[] = ASM::$btm->get($i);
							if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
								$waitingBugQuantity++;
							} else {
								$archivedBugQuantity++;
							}
						}
					}
					break;
				case BugTracker::TYPE_ORTHOGRAPH:
					$listName = 'Fautes d\'orthographe';
					for ($i = 0; $i < ASM::$btm->size(); $i++) {
						if (ASM::$btm->get($i)->type == BugTracker::TYPE_ORTHOGRAPH) {
							$bugtracker[] = ASM::$btm->get($i);
							if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
								$waitingBugQuantity++;
							} else {
								$archivedBugQuantity++;
							}
						}
					}
					break;
				case BugTracker::TYPE_DISPLAY:
					$listName = 'Problèmes d\'affichage';
					for ($i = 0; $i < ASM::$btm->size(); $i++) {
						if (ASM::$btm->get($i)->type == BugTracker::TYPE_DISPLAY) {
							$bugtracker[] = ASM::$btm->get($i);
							if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
								$waitingBugQuantity++;
							} else {
								$archivedBugQuantity++;
							}
						}
					}
					break;
				case BugTracker::TYPE_CALIBRATION:
					$listName = 'Problèmes de calibrage';
					for ($i = 0; $i < ASM::$btm->size(); $i++) {
						if (ASM::$btm->get($i)->type == BugTracker::TYPE_CALIBRATION) {
							$bugtracker[] = ASM::$btm->get($i);
							if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
								$waitingBugQuantity++;
							} else {
								$archivedBugQuantity++;
							}
						}
					}
					break;
				case BugTracker::TYPE_IMPROVEMENT:
					$listName = 'Idées d\'amélioration';
					for ($i = 0; $i < ASM::$btm->size(); $i++) {
						if (ASM::$btm->get($i)->type == BugTracker::TYPE_IMPROVEMENT) {
							$bugtracker[] = ASM::$btm->get($i);
							if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
								$waitingBugQuantity++;
							} else {
								$archivedBugQuantity++;
							}
						}
					}
					break;
			}
		}
		ASM::$btm->changeSession($S_BTM1);



/*
		# bug tracker
		$bugtracker_bug  = array();
		$bugtracker_orthograph  = array();
		$bugtracker_display     = array();
		$bugtracker_calibration = array();
		$bugtracker_improvement = array();

		$waitingBug_mainBugtracker  = 0;
		$archivedBug_mainBugtracker = 0;

		$S_BTM1 = ASM::$btm->getCurrentSession();
		ASM::$btm->newSession();
		ASM::$btm->load(array('statement' => array(BugTracker::ST_WAITING, BugTracker::ST_ARCHIVED)));
		for ($i = 0; $i < ASM::$btm->size(); $i++) {
			switch (ASM::$btm->get($i)->type) {
				case BugTracker::TYPE_ORTHOGRAPH: $bugtracker_orthograph[]   = ASM::$btm->get($i); break; 
				case BugTracker::TYPE_BUG: $bugtracker_bug[]                 = ASM::$btm->get($i); break; 
				case BugTracker::TYPE_ERROR: $bugtracker_error[]             = ASM::$btm->get($i); break; 
				case BugTracker::TYPE_DISPLAY: $bugtracker_display[]         = ASM::$btm->get($i); break; 
				case BugTracker::TYPE_IMPROVEMENT: $bugtracker_improvement[] = ASM::$btm->get($i); break; 


				default:  $bugtracker_bug[]                      = ASM::$btm->get($i); break;
			}
			if (ASM::$btm->get($i)->statement == BugTracker::ST_WAITING) {
				$waitingBug_mainBugtracker++;
			} else {
				$archivedBug_mainBugtracker++;
			}
		}
		ASM::$btm->changeSession($S_BTM1);*/

		# component
		include COMPONENT . 'apollon/bugtracker/mainBugtracker.php';
	} elseif (CTR::$get->get('view') == 'roadmap') {
		# main roadmap
		include COMPONENT . 'apollon/roadmap/addEntry.php';
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>