<?php
require_once '../respuestas/response.php';
require_once '../modelos/card.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();
$auth->verify();
$card = new card();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $params = $_GET;

        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
                echo $_GET['id'];
            $response = array(
                'result' => 'error',
                'details' => 'El id no corresponde con el del usuario autenticado (cards). '
            );
            Response::result(400, $response);
            exit;            
        } 

        $card_list = $card->get($params);
        // print_r($card_list);exit;


        $response = array(
            'result' => 'ok',
            'cards' => $card_list
        );

        Response::result(200, $response);
        break;


    // case 'POST':
    //     $params = json_decode(file_get_contents('php://input'), true);

}




?>