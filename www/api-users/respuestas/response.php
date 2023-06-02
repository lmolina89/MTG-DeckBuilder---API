<?php
class Response
{
	public static function result($code, $response){

		// header('Access-control-Allow-headers: content-type');
		// header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		http_response_code($code);

		echo json_encode($response);  //escribir la respuesta en json
	}
}

?>