<?php
// config
include_once 'main.conf.php';

//Class
include_once 'class/Notification.class.php';
include_once 'class/NotificationManager.class.php';

include_once 'class/RoadMap.class.php';
include_once 'class/RoadMapManager.class.php';

include_once 'class/Conversation.php';
include_once 'class/ConversationManager.php';
include_once 'class/ConversationUser.php';
include_once 'class/ConversationUserManager.php';
include_once 'class/ConversationMessage.php';
include_once 'class/ConversationMessageManager.php';

ASM::runHermes();
?>