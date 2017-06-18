<?php

namespace Asylamba\Classes\Task;

class CyclicTask extends Task
{
	const DEFAULT_ESTIMATED_TIME = 100.0;
	/**
	 * {@inheritdoc}
	 */
    public function getType()
	{
		return self::TYPE_CYCLIC;
	}
}