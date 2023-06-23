<?php
class AuthModel
{
	private $connection;
	//constructor que inicializa la conexion con la base de datos
	// public function __construct()
	// {
	// 	$this->connection = new mysqli('db', 'root', 'example', 'usersDB', '3306');
	// 	if ($this->connection->connect_errno) {
	// 		echo 'Error de conexión a la base de datos';
	// 		exit;
	// 	}
	// }

	public function __construct()
	{
		$host = 'db';
		$dbname = 'usersDB';
		$username = 'root';
		$password = 'example';

		try {
			$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
			$this->connection = new PDO($dsn, $username, $password);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$response = array(
				'result' => 'error',
				'details' => 'Error de conexión a la base de datos: ' . $e->getMessage()
			);
			Response::result(400, $response);
			exit;
		}
	}

	//busca el usuario que va a iniciar sesion por email y contraseña, si hay resultados es que el usuario existe
	public function login($email, $password)
	{
		$query = "SELECT id, nick, email FROM users WHERE email = :email AND passwd = :password";
		$statement = $this->connection->prepare($query);
		$statement->bindParam(':email', $email, PDO::PARAM_STR);
		$statement->bindParam(':password', $password, PDO::PARAM_STR);
		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}

	//actualiza el token del usuario en la base de datos
	public function update($id, $token)
	{
		$query = "UPDATE users SET token = :token WHERE id = :id";
		$statement = $this->connection->prepare($query);
		$statement->bindParam(':token', $token, PDO::PARAM_STR);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		$rowCount = $statement->rowCount();
		return $rowCount;
	}

	//busca el token de usuario por ID
	public function getById($id)
	{
		$query = "SELECT token FROM users WHERE id = :id";
		$statement = $this->connection->prepare($query);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}

	//busca el usuario por ID
	public function getUserById($id)
	{
		$query = "SELECT * FROM users WHERE id = :id";
		$statement = $this->connection->prepare($query);
		$statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results;
	}
}