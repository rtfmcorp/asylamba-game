<?php

namespace Asylamba\Modules\Hephaistos\Model;

class Evolution extends Feedback
{
    public function getType()
    {
        return self::TYPE_EVOLUTION;
    }
}