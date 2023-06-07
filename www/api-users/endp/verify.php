<?php
require_once '../modelos/auth.class.php';
require_once '../respuestas/response.php';

$auth = new Authentication();


switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST': {
            $headers = getallheaders();
            // print_r($headers) ; exit;
            $token = $headers['api-key'];
            if (!isset($headers['api-key'])) {
                $response = array(
                    'result' => 'error',
                    'details' => 'No se ha enviado el token'
                );
                Response::result(400, $response);
                exit;
            }

            // print_r($user);exit; true);
            $auth->verifyAdmin($token);
        }
}