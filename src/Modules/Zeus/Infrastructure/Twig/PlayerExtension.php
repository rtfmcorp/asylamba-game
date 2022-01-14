<?php

namespace App\Modules\Zeus\Infrastructure\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PlayerExtension extends AbstractExtension
{
	public function __construct(protected RequestStack $requestStack)
	{

	}

	public function getFunctions(): array
	{
		return [
		];
	}
}
