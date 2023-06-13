<?php
require_once '../respuestas/response.php';
require_once '../modelos/user.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = new User();
	$params = json_decode(file_get_contents('php://input'), true);

	if (!isset($params)) {
		Response::result(
			400,
			array(
				'result' => 'error',
				'details' => 'Error en la solicitud de creación usuario'
			)
		);
		exit;
	}

	$insert_id = $user->insert($params);

	Response::result(
		201,
		array(
			'result' => 'ok',
			'insert_id' => $insert_id
		)
	);
} else {
	Response::result(
		404,
		array(
			'result' => 'error'
		)
	);
}