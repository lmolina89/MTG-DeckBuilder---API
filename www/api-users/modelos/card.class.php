<?php
require_once '../respuestas/response.php';
require_once '../modelos-datos/database.php';

class Card extends Database
{
    private $table_dc = 'deckcard';
    private $table_c = 'card';

    private $allowedConditions_get = array(
        'deck_id'
    );

    //parámetros permitidos para la inserción. Al hacer el POST
    private $allowedConditions_insert = array(
        'deck_id',
        'id',
        'name',
        'manacost',
        'cmc',
        'atributes',
        'text',
        'artist',
        'expansion',
        'imageUri',
        'numCopies'
    );

    //parámetros permitidos para la actualización.
    private $allowedConditions_update = array(
        'deck_id',
        'id',
        'name',
        'manacost',
        'cmc',
        'atributes',
        'text',
        'artist',
        'expansion',
        'imageUri',
        'numCopies'
    );

    private function validateInsert($data)
    {

        if (!isset($data['id']) || empty($data['name'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id de la carta es obligatorio'
            );

            Response::result(400, $response);
            exit;
        }

        return true;
    }

    private function validateUpdate()
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id de la carta es obligatorio'
            );
            Response::result(400, $response);

        }

        if (!isset($_GET['deck_id']) || empty($_GET['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id del mazo es obligatorio'
            );
            Response::result(400, $response);

        }

        return true;
    }

    public function get($params)
    {
 
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_get)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud'
                );
                Response::result(400, $response);
                exit;
            }
        }
    
        return parent::get_db_join($this->table_dc, $this->table_c, $params);
    }

    public function insert($params)
    {
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_insert)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud. Parametro no permitido al insertar'
                );

                Response::result(400, $response);
                exit;
            }
        }

        if ($this->validateInsert($params)) {
            return parent::insertDB($this->table_dc, $params);
        }
    }

    public function update($id, $params)
    {
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_update)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud dentro del modelo datos'
                );

                Response::result(400, $response);
                exit;
            }

            if ($this->validateUpdate($params)) {
                $affected_rows = parent::updateDB($this->table, $id, $params);

                if ($affected_rows == 0) {
                    $response = array(
                        'result' => 'error',
                        'details' => 'No hubo cambios'
                    );

                    Response::result(200, $response);
                    exit;
                }
            }
        }
    }


    public function delete($id)
    {
        $cards = parent::getDB($this->table, $_GET);
        $card = $cards[0];

    }

// if (!empty($imagen_antigua)) {
// 	$path = dirname(__DIR__, 1) . "/public/img/" . $imagen_antigua;
// 	if (!unlink($path)) {
// 		$response = array(
// 			'result' => 'warning',
// 			'details' => 'No se ha podido eliminar la imagen del usuario'
// 		);
// 		Response::result(200, $response);
// 		exit;
// 	}
// }

// $affected_rows = parent::deleteDB($);
// $affected_rows = parent::deleteDB();

// if($affected_rows == 0){
//     $response = array(
//         'result' => 'error',
//         'details' => 'No hubo cambios'
//     )
//     Response::result(200,$response);
//     exit;
// }




}

?>