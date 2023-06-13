<?php
require_once '../respuestas/response.php';
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();

$auth->verify();

$user = new User();

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		$params = $_GET;

		$usuarios = $user->get($params);

		$url_raiz_img = "http://" . $_SERVER['HTTP_HOST'] . "/api-users/public/img";
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

	case 'POST':
		if (!isset($params)) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);
			Response::result(400, $response);
			exit;
		}

		$insert_id = $user->insert($params);

		$response = array(
			'result' => 'ok',
			'insert_id' => $insert_id
		);
		Response::result(201, $response);
		break;

	case 'PUT':
		$params = json_decode(file_get_contents('php://input'), true);

		if (!isset($params) || !isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud de actualización'
			);

			Response::result(400, $response);
			exit;
		}

		$user->update($_GET['id'], $params);

		$auth->modifyToken($_GET['id'], $params["email"]);
		$response = array(
			'result' => 'ok'
		);
		Response::result(200, $response);
		break;

	case 'DELETE':
		if (!isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);

			Response::result(400, $response);
			exit;
		}
		$user->delete($_GET['id']);

		$response = array(
			'result' => 'ok'
		);

		Response::result(200, $response);
		break;
	default:
		$response = array(
			'result' => 'error'
		);

		Response::result(404, $response);

		break;
}
?>