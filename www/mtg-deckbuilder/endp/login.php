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
    $isAdmin = false;
    if ($auth->isAdmin($token) == 1) {
        $isAdmin = true;
    }
    //else {
//        $isAdmin = false;
//    }
    //si el usuario esta desactivado no permite el acceso
    if ($auth->isActive($token) == 0) {
        $response = [
            'result' => 'error',
            'details' => 'Este usuario esta desactivado'
        ];
        Response::result(403, $response);
        exit;
    }
    //Respuesta que se envia al hacer login   								  	    	    	    	    	    	    	    	    	    
    $response = [
        'result' => 'ok',
        'token' => $token,
        'user_nick' => $nick,
        'admin' => $isAdmin
    ];
    Response::result(201, $response);
}