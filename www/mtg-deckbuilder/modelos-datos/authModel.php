<?php
class AuthModel
{
	private $connection;
	//constructor que inicializa la conexion con la base de datos
	public function __construct()
	{
		$this->connection = new mysqli('db', 'root', 'example', 'usersDB', '3306');
		if ($this->connection->connect_errno) {
			echo 'Error de conexión a la base de datos';
			exit;
		}
	}

	//busca el usuario que va a iniciar sesion por email y contraseña, si hay resultados es que el usuario existe
	public function login($email, $password)
	{
		$query = "SELECT id, nick, email FROM users WHERE email = '$email' AND passwd = '$password'";
		$results = $this->connection->query($query);

		$resultArray = array();

		if ($results != false) {
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}
		return $resultArray;
	}

	//actualiza el token del usuario en la base de datos
	public function update($id, $token)
	{
		$query = "UPDATE users SET token = '$token' WHERE id = $id";
		$this->connection->query($query);
		if (!$this->connection->affected_rows) {
			return 0;
		}
		return $this->connection->affected_rows;
	}

	//busca el token de usuario por ID
	public function getById($id)
	{
		$query = "SELECT token FROM users WHERE id = $id";
		$results = $this->connection->query($query);
		$resultArray = array();
		if ($results != false) {
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}
		return $resultArray;
	}

	//busca el usuario por ID
	public function getUserById($id)
	{
		$query = "SELECT * FROM users WHERE id = $id";
		$results = $this->connection->query($query);
		$resulArray = array();
		if ($results != false) {
			foreach ($results as $value) {
				$resulArray[] = $value;
			}
		}
		return $resulArray;
	}
}