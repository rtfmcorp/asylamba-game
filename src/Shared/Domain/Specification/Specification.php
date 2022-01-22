<?php

namespace App\Shared\Domain\Specification;

interface Specification
{
	public function isSatisfiedBy($candidate): bool;
}
