<?php

namespace Asylamba\Classes\Kernel;

interface KernelInterface
{
	public function boot(): void;

	public function init(): void;
}
