<?php

namespace Asylamba\Classes\Task;

class CyclicTask extends Task
{
	/**
	 * {@inheritdoc}
	 */
    public function getType()
	{
		return self::TYPE_CYCLIC;
	}
}