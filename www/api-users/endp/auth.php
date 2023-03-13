<?php
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';
require_once '../respuestas/response.php';


/** 
 * EndPoint que tiene definido el método POST y lo que 
 * recibe es un username y un password. Tienen que coincidir
 * en la BBDD y si coindice, genera el token para devolverlo al 
 * usuario que deberá agregarlo a su encabezado para posteriormente
 * usarlo en los demás endpoint con normalidad. 
 */

// Se crea un objeto con la tabla, la key privada. 
$auth = new Authentication();

// Dependiendo del método request, tiene que ser un POST. 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se obtiene los datos del usuario desde el input. 
    $user = json_decode(file_get_contents('php://input'), true);

    // Se genera el token con los datos del usuario. 
    $token = $auth->signIn($user);

    // Desde el token, se obtiene el nombre de usuario (nick).  
    $nick = $auth->getNick($token);

    // Se crea un array con los datos a devolver al usuario (resultado, token y nick).  								  	    	    	    	    	    	    	    	    	    
    $response = ['result' => 'ok', 'token' => $token, 'user_nick' => $nick];

    // Se devuelve el token correctamente al usuario. 
    Response::result(201, $response);
}