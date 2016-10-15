<?php

$app->get('/', function ($app) {
	// do something

	// and return a view
	echo $app->view('welcome.html', array(
		'users' => array('Alice', 'Bob')
	));
});

$app->get('/greet/:name', function ($app, $name) {
	// do something

	// and return a view
	echo $app->view('greeting.html', array(
		'name' => $name,
		'greetingId' => $app->input()->get('greetingId', TYPE_INT)
	));
});

$app->post('/photos/:id/delete', function ($app, $id) {
	// do something ...
});

// return an error page for undefined pages
header('HTTP/1.0 404 Not Found');
echo $app->view('404.html');
