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


    case 'POST':
        $params = json_decode(file_get_contents('php://input'), true);
//        print_r($_GET);
//        print_r($_SERVER);
//        print_r($params);exit;

        if (!isset($_GET['deck_id']) && empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'No se ha especificado la id del mazo'
            );
            Response::result(400, $response);
            exit;
        }

        $params['deck_id'] = $_GET['deck_id'];
        if ($card->insert($params)) {
            $response = array(
                'result' => 'ok',
                'details' => 'insertado correctamente'
            );
//            echo "Fin del metodo POST dentro de card.php";
//            exit;
            Response::result(200, $response);
        }
        break;


    case 'PUT':
        $params = json_decode(file_get_contents('php://input'), true);

        if (!isset($_GET['deck_id']) && empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'No se ha especificado la id del mazo'
            );
            Response::result(400, $response);
            exit;
        }


        $params['deck_id'] = $_GET['deck_id'];
//        print_r($params);exit;
        if ($card->update($params)) {
            $response = array(
                'result' => 'ok',
                'details' => 'Numero de cartas modificado correctamente'
            );
//            echo "Fin del metodo POST dentro de card.php";
//            exit;
            Response::result(200, $response);
        }
        break;

    case 'DELETE':
//        $params = json_decode(file_get_contents('php://input'), true);

        if(!isset($_GET['deck_id']) || empty($_GET['deck_id'])){
            $response = array(
                'result' => 'error',
                'details' => 'Falta el dato deck_id'
            );
            Response::result(400, $response);
            exit;
        }

        if(!isset($_GET['card_id']) || empty($_GET['card_id'])){
            $response = array(
                'result' => 'error',
                'details' => 'Falta el dato card_id'
            );
            Response::result(400, $response);
            exit;
        }
        $params = $_GET;
//        $params['deck_id'] = $_GET['deck_id'];

        $card->delete($params);
//        echo "hasta aqui hemos llegado";exit;
        $response = array(
            'result' => 'ok',
            'details' => 'carta eliminada correctamente'
        );

        Response::result(200,$response);
        break;
}


?>