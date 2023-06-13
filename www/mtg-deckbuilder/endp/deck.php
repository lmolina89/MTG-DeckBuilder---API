<?php
require_once '../respuestas/response.php';
require_once '../modelos/deck.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();

$auth->verify();

$deck = new deck();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $params = $_GET;

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


        $decks = $deck->get($params);
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

    case 'POST':

        $params = json_decode(file_get_contents('php://input'), true); //supongo que se envía por @body

        if (!isset($params)) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud deck'
            );

            Response::result(400, $response);
            exit;
        }

        $params['user_id'] = $auth->getIdUser();

        $params_deck_id["deck_id"] = $deck->insert($params);
        $insert_id_deck = $params_deck_id['deck_id'];

        $response = array(
            'result' => 'ok',
            'insert_id' => $insert_id_deck,
            'user_id' => $params['user_id']
        );

        Response::result(201, $response);
        break;

    case 'PUT':
        $params = json_decode(file_get_contents('php://input'), true);

        if (!isset($params) || !isset($_GET['id']) || empty($_GET['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud de actualización'
            );

            Response::result(400, $response);
            exit;
        }

        $deck->update($_GET['id'], $params);

        $response = array(
            'result' => 'ok',
            'details' => 'Se ha actualizado correctamente'
        );

        Response::result(200, $response);

        break;

    case 'DELETE':

        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la solicitud de eliminacion'
            );

            Response::result(400, $response);
            exit;
        }
        $deck->delete($_GET['deck_id']);

        $response = array(
            'result' => 'ok',
            'details' => 'eliminado correctamente'
        );

        Response::result(200, $response);
        break;

    default:
        $response = array(
            'result' => 'error',
            'details' => 'ha ocurrido algun problema'
        );

        Response::result(404, $response);
        break;
}
?>