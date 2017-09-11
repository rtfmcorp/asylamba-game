<?php

namespace Asylamba\Modules\Hermes\Model;

class ConversationMessage
{
    const MESSAGE_BY_PAGE        = 25;

    const TY_STD                = 1;
    const TY_SYSTEM                = 2;

    public $id                    = 0;
    public $rConversation        = 0;
    public $rPlayer                = 0;
    public $type                = 0;
    public $content                = 0;
    public $dCreation            = 0;
    public $dLastModification    = 0;

    public $playerColor            = 0;
    public $playerName            = '';
    public $playerAvatar        = '';
    public $playerStatus        = 0;

    public function getId()
    {
        return $this->id;
    }
}
