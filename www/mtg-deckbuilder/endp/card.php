<?php
require_once '../respuestas/response.php';
require_once '../modelos/card.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();
$auth->verify();
$card = new card();

switch ($_SERVER['REQUEST_METHOD']) {
    //busca todas las cartas de un mazo
    case 'GET':
        //datos de los parametros de la URL
        $params = $_GET;
        //error si no se encuentra el deck_id en la URL
        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
            echo $_GET['id'];
            $response = array(
                'result' => 'error',
                'details' => 'El id no corresponde con el del usuario autenticado (cards). '
            );
            Response::result(400, $response);
            exit;
        }
        //busca las cartas del mazo
        $card_list = $card->get($params);
        $response = array(
            'result' => 'ok',
            'cards' => $card_list
        );
        Response::result(200, $response);
        break;

    //inserta una nueva carta en el mazo
    case 'POST':
        //datos de la nueva carta en el body de la solicitud
        $params = json_decode(file_get_contents('php://input'), true);

        //error si en la URL no existe la ID del mazo donde se va a insertar la nueva carta
        if (!isset($_GET['deck_id']) && empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'No se ha especificado la id del mazo'
            );
            Response::result(400, $response);
            exit;
        }
        //se inserta deck_id en el array de params y se crea la nueva carta en el mazo
        $params['deck_id'] = $_GET['deck_id'];
        if ($card->insert($params)) {
            $response = array(
                'result' => 'ok',
                'details' => 'insertado correctamente'
            );
            Response::result(200, $response);
        }
        break;

    //edita el numero de copias de una carta en el mazo
    case 'PUT':
        //datos del body en los que se indica si se aumenta o disminuye el numero de cartas
        $params = json_decode(file_get_contents('php://input'), true);
        //error si no se pasa la ID del mazo en la URL
        if (!isset($_GET['deck_id']) && empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'No se ha especificado la id del mazo'
            );
            Response::result(400, $response);
            exit;
        }
        //inserta deck_id en el array de params para la consulta
        $params['deck_id'] = $_GET['deck_id'];

        //si se cambia el numero correctamente devuelve ok
        if ($card->update($params)) {
            $response = array(
                'result' => 'ok',
                'details' => 'Numero de cartas modificado correctamente'
            );
            Response::result(200, $response);
        }
        break;

    //elimina una carta del mazo
    case 'DELETE':
        //error si no se encuentra en la URL la ID del mazo
        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Falta el dato deck_id'
            );
            Response::result(400, $response);
            exit;
        }

        //error si no se encuentra en la URL la ID de la carta que se va a eliminar
        if (!isset($_GET['card_id']) || empty($_GET['card_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Falta el dato card_id'
            );
            Response::result(400, $response);
            exit;
        }

        $params = $_GET;
        //elimina la carta del mazo
        $card->delete($params);

        $response = array(
            'result' => 'ok',
            'details' => 'carta eliminada correctamente'
        );
        Response::result(200, $response);
        break;
}
?>