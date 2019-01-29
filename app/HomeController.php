<?php

namespace App;

use Delight\Foundation\App;

class HomeController {

	public static function getIndex(App $app) {
		// do something
		// ...

		// and return a view
		echo $app->view('welcome.html.twig', [
			'users' => [ 'Alice', 'Bob' ]
		]);
	}

}
