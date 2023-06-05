<?php
use Firebase\JWT\JWT;

require_once '../respuestas/response.php';
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';

/*
ESTE ENDPOINT, SERÁ LLAMADO SIEMPRE QUE QUERAMOS HACER UN
****LISTADO (GET)
****MODIFICAR-USUARIO (PUT)
*****ELIMINAR-USUARIO(DELETE)

Para comprobar si tiene permisos y está autorizado.

 * endpoint para cualquier petición de usuarios.
 * Tendremos dos endpoint.
 * 1.- El de la autenticación
 * 2.- Para la petición de datos de usuarios.
 */

/**
 * SE SUPONE QUE AL ACCEDER A ESTE ENDPOINT, YA NOS HEMOS LOGEADO.
 */
$auth = new Authentication(); //***** CAPA DE AUTHENTICATION  *****/
//Compara que el token sea el correcto y que la decodificación con clave privada
//sea la correcta.
$auth->verify(); /* VERIFICAMOS LA AUTENTICACIÓN.  */
//hasta aquí, el token está perfectamente verificada.
$user = new User(); //creamos un objeto de la clase User.

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		$params = $_GET; 
		$headers = getallheaders();
		// echo $headers['api-key']; exit;
		$token = $headers['api-key'];
		$user_data = $auth->decodeToken($token);
		// echo $user_data->data->id;exit;
		$current_user = $auth->getUserById($user_data->data->id)[0];
		// print_r($user);exit;

		//si tengo permisos de administrador y estoy activo puedo ver la lista de usuarios
		if ($current_user['admin'] == 1 && $current_user['active'] == 1) {
			$usuarios = $user->get($params);
			$url_raiz_img = "http://" . $_SERVER['HTTP_HOST'] . "/api-users/public/img";
			foreach ($usuarios as $usuario) {
				if (!empty($usuario['imageUri'])) {
					$imagen = $usuario['imageUri'];
					$usuario['imageUri'] = $url_raiz_img . "/" . $imagen;
					//echo $usuario['imageUri'];
				}
			}
			$response = array(
				'result' => 'ok',
				'usuarios' => $usuarios
			);

			Response::result(200, $response);
			break;
		}
		//si no tengo permisos o no estoy activo lanzo una respuesta de error
		$response = array(
			'result' => 'error',
			'details' => 'No tienes permisos de administrador'
		);

		Response::result(403, $response);
		break;

	case 'POST':
		$params = json_decode(file_get_contents('php://input'), true); //supongo que se envía por @body

		if (!isset($params)) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);
			Response::result(400, $response);
			exit;
		}
		//aquí insertamos el nuevo usuario a partir de nuestro objeto user.
		$insert_id = $user->insert($params);

		$response = array(
			'result' => 'ok',
			'insert_id' => $insert_id
		);
		Response::result(201, $response);
		break;

	case 'PUT':
		//volvemos a pasar nuestro json a un arry asociativo
		$params = json_decode(file_get_contents('php://input'), true);

		/*
					Es obligatorio que al editar un usuario, exista el parámetro id y valor.
					*/
		if (!isset($params) || !isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud de actualización'
			);

			Response::result(400, $response);
			exit;
		}

		//actualizamos por id.
		$user->update($_GET['id'], $params);
		/**
		 * toca actualizar el token del usuario, ya que modificó obligatoriamente
		 * el campo email.
		 */
		$auth->modifyToken($_GET['id'], $params["email"]);
		$response = array(
			'result' => 'ok'
		);

		Response::result(200, $response);

		break;

	/**
	 * Comprueba que le hemos pasado por url el id y que no esté vacío. En cuyo caso, armamos la 
	 * respuesta de error y nos salimos. Llamamos al método delete pasándole el id y éste
	 * eliminará dicho registro. Por último arma la respuesta ok.
	 */

	case 'DELETE':

		/*
					Es obligatorio el id por GET
					*/
		if (!isset($_GET['id']) || empty($_GET['id'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);

			Response::result(400, $response);
			exit;
		}
		//eliminamos al usuario, cuya id pasamos.
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