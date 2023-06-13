<?php
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';
require_once '../respuestas/response.php';

$auth = new Authentication();

// Solo se ejecuta si el metodo es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Obtiene los datos de usuario del token
    $user = json_decode(file_get_contents('php://input'), true);
    //se genera el nuevo token 
    $token = $auth->signIn($user);

    //se obtiene el nick a partir del token  
    $nick = $auth->getNick($token);
    //parsea la propiedad admin de integer a boolean
    $isAdmin;
    if ($auth->isAdmin($token) == 1) {
        $isAdmin = true;
    } else {
        $isAdmin = false;
    }
    //Respuesta que se envia al hacer login   								  	    	    	    	    	    	    	    	    	    
    $response = [
        'result' => 'ok',
        'token' => $token,
        'user_nick' => $nick,
        'admin' => $isAdmin
    ];
    // se devuelve el token  
    Response::result(201, $response);
}