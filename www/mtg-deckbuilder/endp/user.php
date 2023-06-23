<?php
require_once '../respuestas/response.php';
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();
//verifica el token
$auth->verify();

$user = new User();

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':

		$params = $_GET;
		//busca la lista de usuarios
		$usuarios = $user->get($params);

		//monta la url donde se encuentra la imagen de cada usuario en el servidor
		$url_raiz_img = "http://" . $_SERVER['HTTP_HOST'] . "/mtg-deckbuilder/public/img";
		foreach ($usuarios as $usuario) {
			if (!empty($usuario['imageUri'])) {
				$imagen = $usuario['imageUri'];
				$usuario['imageUri'] = $url_raiz_img . "/" . $imagen;
			}
		}

		$response = array(
			'result' => 'ok',
			'usuarios' => $usuarios
		);
		Response::result(200, $response);
		break;

	// case 'POST':
	// 	if (!isset($params)) {
	// 		$response = array(
	// 			'result' => 'error',
	// 			'details' => 'Error en la solicitud'
	// 		);
	// 		Response::result(400, $response);
	// 		exit;
	// 	}

	// 	$insert_id = $user->insert($params);

	// 	$response = array(
	// 		'result' => 'ok',
	// 		'insert_id' => $insert_id
	// 	);
	// 	Response::result(201, $response);
	// 	break;

	case 'PUT':
		//datos del usuario a editar 
		$params = json_decode(file_get_contents('php://input'), true);

		//error si no hay datos en el body o no se manda la id por URL
		if (!isset($params) || !isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud de actualización'
			);
			Response::result(400, $response);
			exit;
		}
		//actualiza el usuario
		$user->update($_GET['id'], $params);
		//actualiza el token
		$auth->modifyToken($_GET['id'], $params["email"]);

		$response = array(
			'result' => 'ok'
		);
		Response::result(200, $response);
		break;

	case 'DELETE':
		//error si no se pasa ID de usuario por URL 
		if (!isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);
			Response::result(400, $response);
			exit;
		}
		//se elimina el usuario
		$user->delete($_GET['id']);

		$response = array(
			'result' => 'ok'
		);
		Response::result(200, $response);
		break;

	default:
		//respuesta por defecto
		$response = array(
			'result' => 'error'
		);
		Response::result(404, $response);
		break;
}
?>