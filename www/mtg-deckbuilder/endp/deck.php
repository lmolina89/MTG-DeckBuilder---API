<?php
require_once '../respuestas/response.php';
require_once '../modelos/deck.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();

$auth->verify();

$deck = new deck();

switch ($_SERVER['REQUEST_METHOD']) {
    //buscar lista de mazos del usuario
    case 'GET':
        //parametros de la URL
        $params = $_GET;
        //error si no se pasa la ID del usuario por URL o el ID del token no corresponde con el de la URL
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            if ($_GET['id'] != $auth->getIdUser()) {
                $response = array(
                    'result' => 'error',
                    'details' => 'El id no corresponde con el del usuario autenticado. '
                );
                Response::result(400, $response);
                exit;
            }
        } else {
            $params['user_id'] = $auth->getIdUser();
        }
        //busca la lista de mazos del usuario
        $decks = $deck->get($params);
        //monta la URL de la imagen del mazo
        $url_raiz_img = "http://" . $_SERVER['HTTP_HOST'] . "/api-users/public/img";
        for ($i = 0; $i < count($decks); $i++) {
            if (!empty($decks[$i]['deckImage']))
                $decks[$i]['deckImage'] = $url_raiz_img . "/" . $decks[$i]['deckImage'];
        }

        $response = array(
            'result' => 'ok',
            'decks' => $decks
        );
        Response::result(200, $response);
        break;

    //crea un nuevo mazo
    case 'POST':
        //datos del nuevo mazo del body de la solicitud
        $params = json_decode(file_get_contents('php://input'), true);
        //error si no hay datos en el body
        if (!isset($params)) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud deck'
            );
            Response::result(400, $response);
            exit;
        }

        //extrae la id de usuario del token
        $params['user_id'] = $auth->getIdUser();

        //si se inserta correctamente devuelve el ID del nuevo mazo
        $params_deck_id["deck_id"] = $deck->insert($params);
        $insert_id_deck = $params_deck_id['deck_id'];

        $response = array(
            'result' => 'ok',
            'insert_id' => $insert_id_deck,
            'user_id' => $params['user_id']
        );
        Response::result(201, $response);
        break;

    //edita un mazo 
    case 'PUT':
        //datos del mazo que se va a actualizar del body de la solicitud
        $params = json_decode(file_get_contents('php://input'), true);
        // error si no se envia el ID del mazo por la URL 
        if (!isset($params) || !isset($_GET['id']) || empty($_GET['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud de actualización'
            );
            Response::result(400, $response);
            exit;
        }
        //actualiza el mazo
        $deck->update($_GET['id'], $params);

        $response = array(
            'result' => 'ok',
            'details' => 'Se ha actualizado correctamente'
        );
        Response::result(200, $response);
        break;

    //elimina un mazo por ID
    case 'DELETE':
        //error si no se envia la ID por URL
        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud de eliminacion'
            );
            Response::result(400, $response);
            exit;
        }
        //se elimina el mazo
        $deck->delete($_GET['deck_id']);

        $response = array(
            'result' => 'ok',
            'details' => 'eliminado correctamente'
        );
        Response::result(200, $response);
        break;

    default:
        //respuesta por defecto
        $response = array(
            'result' => 'error',
            'details' => 'ha ocurrido algun problema'
        );
        Response::result(404, $response);
        break;
}
?>