<?php
class Response
{
	//respuesta de la api en JSON
	public static function result($code, $response)
	{
		header('Content-Type: application/json');
		http_response_code($code);

		echo json_encode($response);
	}
}

?>