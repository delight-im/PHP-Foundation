<?php

$app->get('/', function (\Delight\Foundation\App $app) {
	// do something
	// ...

	// and return a view
	echo $app->view('welcome.html', [
		'users' => [ 'Alice', 'Bob' ]
	]);
});

$app->get('/greet/:name', function (\Delight\Foundation\App $app, $name) {
	// do something
	// ...

	// and return a view
	echo $app->view('greeting.html', [
		'name' => $name,
		'greetingId' => $app->input()->get('greetingId', TYPE_INT)
	]);
});

$app->post('/photos/:id/delete', function (\Delight\Foundation\App $app, $id) {
	// do something
	// ...
});

// return an error page for undefined pages
$app->setStatus(404);
echo $app->view('404.html');
