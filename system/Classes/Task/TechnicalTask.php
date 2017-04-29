<?php

namespace Asylamba\Classes\Task;

class TechnicalTask extends Task
{
	/**
	 * {@inheritdoc}
	 */
    public function getType()
	{
		return self::TYPE_TECHNICAL;
	}
}