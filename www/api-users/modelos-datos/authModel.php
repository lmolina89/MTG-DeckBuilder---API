<?php

/**
 * Modelo para la authenticación.
 */
class AuthModel
{
	private $connection;
	
	public function __construct(){
		$this->connection = new mysqli('db', 'root', 'example', 'usersDB', '3306');

		if($this->connection->connect_errno){
			echo 'Error de conexión a la base de datos';
			exit;
		}
	}

	/**
	 * Este método, recibe el email y el password ya codificado.
	 * Realiza una query, devolviendo el id, nombres a partir del username y de la password codificada.
	 */

	public function login($email, $password)
	{
		$query = "SELECT id, nick, email FROM users WHERE email = '$email' AND passwd = '$password'";

		$results = $this->connection->query($query);

		$resultArray = array();

		if($results != false){
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}

		//devuelve un array con el id, nombres y username.
		return $resultArray;
	}

	/**
	 * Setea el token a partir del id. Cada logeo, tenemos que actualizar el registro.
	 */

	public function update($id, $token)
	{
		$query = "UPDATE users SET token = '$token' WHERE id = $id";

		$this->connection->query($query);
		
		if(!$this->connection->affected_rows){
			return 0;
		}

		return $this->connection->affected_rows;
	}

	/**
	 * Retorna el token dado un id de usuario.
	 */
	public function getById($id)
	{
		$query = "SELECT token FROM users WHERE id = $id";

		$results = $this->connection->query($query);

		$resultArray = array();

		if($results != false){
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}

		return $resultArray;
	}

	public function getUserById($id){
		$query = "SELECT * FROM users WHERE id = $id";
		$results = $this->connection->query($query);

		$resulArray = array();
		if($results != false){
			foreach($results as $value){
				$resulArray[] = $value;
			}
		}
		return $resulArray;
	}



	// public function insertarLog($milog){
	// 	$query = "INSERT INTO log (log) VALUES('$milog')";
	// 	//echo $query;exit;
	// 	$this->connection->query($query);
	// }
}
