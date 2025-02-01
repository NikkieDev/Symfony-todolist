<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RandomController
{
	#[Route('/random/word')]
	public function word(): Response
	{
		define("words", array("Hello", "Goodbye", "Symfony is awesome!"));
		$randomNumber = random_int(0, count(words));

		if ($randomNumber > (count(words) -1)) {
			throw new \Exception("Random number exceeds word arraysize");
		}

		return new Response(
			words[$randomNumber]
		);
	}
}