<?php
require_once '../jwt/JWT.php';
require_once '../modelos-datos/authModel.php';
require_once '../respuestas/response.php';
use Firebase\JWT\JWT;

class Authentication extends AuthModel
{
	private $table = 'users';
	private $key = 'clave_secreta_muy_discreta';
	private $idUser = '';

	//login de usuario
	public function signIn($user)
	{
		//error si no se envia email o password en el body
		if (!isset($user['email']) || !isset($user['passwd']) || empty($user['email']) || empty($user['passwd'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Los campos password y email son obligatorios'
			);
			Response::result(400, $response);
			exit;
		}
		//busca el usuario en la base de datos
		$result = parent::login($user['email'], hash('sha256', $user['passwd']));
		//error si no existe el usuario
		if (sizeof($result) == 0) {
			$response = array(
				'result' => 'error',
				'details' => 'El email y/o la contraseña son incorrectas'
			);
			Response::result(403, $response);
			exit;
		}
		//genera un nuevo token
		$dataToken = array(
			'iat' => time(),
			'data' => array(
				'id' => $result[0]['id'],
				'email' => $result[0]['email']
			)
		);

		$jwt = JWT::encode($dataToken, $this->key);
		//actualiza el token en la base de datos
		parent::update($result[0]['id'], $jwt);

		return $jwt;
	}

	//devuelve la ID de usuario
	public function getIdUser()
	{
		return $this->idUser;
	}

	//verifica si el token del header corresponde al usuario que ha iniciado sesion y si ha caducado
	public function verify()
	{
		//error si no existe el token en el header
		if (!isset($_SERVER['HTTP_API_KEY'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Usted no tiene los permisos para esta solicitud'
			);
			Response::result(403, $response);
			exit;
		}

		$jwt = $_SERVER['HTTP_API_KEY'];
		//comprueba que el token que se envia en el header es el mismo que el usuario tiene en la base de datos
		try {
			$data = JWT::decode($jwt, $this->key, array('HS256'));
			$user = parent::getById($data->data->id);
			$this->idUser = $data->data->id;

			if ($user[0]['token'] != $jwt) {
				throw new Exception();
			}
			return $data;
		} catch (\Throwable $th) {
			$response = array(
				'result' => 'error',
				'details' => 'No tiene los permisos para esta solicitud'
			);
			Response::result(403, $response);
			exit;
		}
	}

	//devuelve si el usuario es administrador
	public function isAdmin($token)
	{
		$data = JWT::decode($token, $this->key, array('HS256'));
		$user = parent::getUserById($data->data->id);
		return $user[0]['admin'];
	}

	//devuelve si el usuario esta activo
	public function isActive($token)
	{
		$data = JWT::decode($token, $this->key, array('HS256'));
		$user = parent::getUserById($data->data->id);
		return $user[0]['active'];
	}

	//devuelve el nick del usuario a partir de su token
	public function getNick($token)
	{
		$data = JWT::decode($token, $this->key, array('HS256'));
		$user = parent::getUserById($data->data->id);
		return $user[0]['nick'];
	}

	//genera un nuevo token de usuario
	public function modifyToken($id, $email)
	{
		$dataToken = array(
			'iat' => time(),
			'data' => array(
				'id' => $id,
				'email' => $email
			)
		);

		$jwt = JWT::encode($dataToken, $this->key);
		parent::update($id, $jwt);
		return $jwt;
	}
}