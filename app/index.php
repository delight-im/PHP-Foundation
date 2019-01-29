<?php

use Delight\Foundation\App;

$app->get('/', function (App $app) {
	// do something
	// ...

	// and return a view
	echo $app->view('welcome.html.twig', [
		'users' => [ 'Alice', 'Bob' ]
	]);
});

$app->get('/greet/:name', function (App $app, $name) {
	// do something
	// ...

	// and return a view
	echo $app->view('greeting.html.twig', [
		'name' => $name,
		'greetingId' => $app->input()->get('greetingId', TYPE_INT)
	]);
});

$app->post('/photos/:id/delete', function (App $app, $id) {
	// do something
	// ...
});

// return an error page for undefined pages
$app->setStatus(404);
echo $app->view('404.html.twig');
