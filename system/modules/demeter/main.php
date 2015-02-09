<?php

// inclusion des classes de demeter
include_once 'class/forum/ForumTopic.class.php';
include_once 'class/forum/ForumTopicManager.class.php';
include_once 'class/forum/ForumMessage.class.php';
include_once 'class/forum/ForumMessageManager.class.php';
include_once 'class/forum/newsFaction.class.php';
include_once 'class/forum/newsFactionManager.class.php';

include_once 'class/election/Election.class.php';
include_once 'class/election/ElectionManager.class.php';
include_once 'class/election/Candidate.class.php';
include_once 'class/election/CandidateManager.class.php';
include_once 'class/election/Vote.class.php';
include_once 'class/election/VoteManager.class.php';

include_once 'class/law/VoteLaw.class.php';
include_once 'class/law/VoteLawManager.class.php';
include_once 'class/law/Law.class.php';
include_once 'class/law/LawManager.class.php';


include_once 'class/Color.class.php';
include_once 'class/ColorManager.class.php';

include_once 'resource/ForumResources.php';
include_once 'resource/LawResources.php';

ASM::runDemeter();
