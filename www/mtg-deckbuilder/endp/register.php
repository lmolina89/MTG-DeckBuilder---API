<?php
require_once '../respuestas/response.php';
require_once '../modelos/user.class.php';

//headers para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-Type');
	exit;
}

//registrar nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = new User();
	//body de la solicitud
	$params = json_decode(file_get_contents('php://input'), true);
	//error si no hay datos en el body de la solicitud
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
	//crea el nuevo usuario con los datos del body
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