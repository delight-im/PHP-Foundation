<?php

namespace App;

use Delight\Foundation\App;

class IntroController {

	public static function getGreet(App $app, $name) {
		// do something
		// ...

		// and return a view
		echo $app->view('greeting.html.twig', [
			'name' => $name,
			'greetingId' => $app->input()->get('greetingId', \TYPE_INT)
		]);
	}

}
