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

	public function signIn($user)
	{
		if (!isset($user['email']) || !isset($user['passwd']) || empty($user['email']) || empty($user['passwd'])) {
			$response = array(
				'result' => 'error',
				'details' => 'Los campos password y email son obligatorios'
			);

			Response::result(400, $response);
			exit;
		}

		$result = parent::login($user['email'], hash('sha256', $user['passwd']));

		if (sizeof($result) == 0) {
			$response = array(
				'result' => 'error',
				'details' => 'El email y/o la contraseña son incorrectas'
			);

			Response::result(403, $response);
			exit;
		}

		$dataToken = array(
			'iat' => time(),
			'data' => array(
				'id' => $result[0]['id'],
				'email' => $result[0]['email']
			)
		);

		$jwt = JWT::encode($dataToken, $this->key);

		parent::update($result[0]['id'], $jwt);

		return $jwt;
	}

	public function getIdUser()
	{
		return $this->idUser;
	}

	public function verify()
	{
		if (!isset($_SERVER['HTTP_API_KEY'])) {

			echo "No existe HTTP_API_KEY";
			$response = array(
				'result' => 'error',
				'details' => 'Usted no tiene los permisos para esta solicitud'
			);

			Response::result(403, $response);
			exit;
		}

		$jwt = $_SERVER['HTTP_API_KEY'];

		try {
			$data = JWT::decode($jwt, $this->key, array('HS256'));
			//echo "paso";
			$user = parent::getById($data->data->id);
			$this->idUser = $data->data->id;
			//echo $user; exit;

			if ($user[0]['token'] != $jwt) {
				throw new Exception();
			}

			//$this->insertarLog('autenticado correctamente'); exit; 
			return $data;
		} catch (\Throwable $th) {

			//$this->insertarLog( $_SERVER['HTTP_API_KEY']); exit; 

			$response = array(
				'result' => 'error',
				'details' => 'No tiene los permisos para esta solicitud'
			);

			Response::result(403, $response);
			exit;
		}
	}

	public function isAdmin($token)
	{
		$data = JWT::decode($token, $this->key, array('HS256'));
		$user = parent::getUserById($data->data->id);
		return $user[0]['admin'];
	}

	//devuelve el nick del usuario a partir de su token
	public function getNick($token)
	{
		$data = JWT::decode($token, $this->key, array('HS256'));
		$user = parent::getUserById($data->data->id);
		return $user[0]['nick'];
	}

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