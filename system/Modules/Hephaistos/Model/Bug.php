<?php

namespace Asylamba\Modules\Hephaistos\Model;

class Bug extends Feedback
{
    public function getType()
    {
        return self::TYPE_BUG;
    }
}