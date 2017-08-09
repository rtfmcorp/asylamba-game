<?php

namespace Asylamba\Classes\Task;

class TechnicalTask extends Task
{
    const DEFAULT_ESTIMATED_TIME = 10.00;
    
	/**
	 * {@inheritdoc}
	 */
    public function getType()
	{
		return self::TYPE_TECHNICAL;
	}
}