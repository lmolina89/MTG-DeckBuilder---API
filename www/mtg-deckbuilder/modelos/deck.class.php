<?php
require_once '../respuestas/response.php';
require_once '../modelos-datos/database.php';

class Deck extends Database
{
    private $table = 'deck'; //nombre de la tabla
    private $table_dc = 'deckcard';

    //parametros validos para GET
    private $allowedConditions_get = array(
        'user_id',
        'deck_id'
    );

    //parametros validos para POST
    private $allowedConditions_insert = array(
        'id',
        'user_id',
        'name',
        'deckImage'
    );

    //parametros validos para PUT
    private $allowedConditions_update = array(
        'id',
        'name',
        'deckImage'
    );

    //validar insercion de mazos
    private function validateInsert($data)
    {

        if (!isset($data['name']) || empty($data['name'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo nombre es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }

        if (isset($data['deckImage']) & !empty($data['deckImage'])) {
            $img_array = explode(';base64,', $data['deckImage']);
            $extension = strtoupper(explode('/', $img_array[0])[1]);
            if ($extension != 'PNG' && $extension != 'JPG' && $extension != 'JPEG') {
                $response = array(
                    'result' => 'error',
                    'details' => 'Formato de la imagen no permitida, sólo PNG/JPE/JPEG'
                );
                Response::result(400, $response);
                exit;
            }
        }
        return true;
    }

    //validar actualizacion de mazos
    private function validateUpdate($data)
    {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id es obligatorio'
            );

            Response::result(400, $response);
            exit;
        }

        if (isset($data['deckImage']) && !empty($data['deckImage'])) {
            $img_array = explode(';base64,', $data['deckImage']);
            $extension = strtoupper(explode('/', $img_array[0])[1]); //me quedo con jpeg
            if ($extension != 'PNG' && $extension != 'JPG' && $extension != 'JPEG' && $extension != 'WEBP') {
                $response = array(
                    'result' => 'error',
                    'details' => 'Formato de la imagen no permitida, sólo PNG/JPE/JPEG/WEBP'
                );
                Response::result(400, $response);
                exit;
            }
        }
        return true;
    }

    //validar buscar mazos de usuario
    public function get($params)
    {
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_get)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud get en deck.class'
                );
                Response::result(400, $response);
                exit;
            }
        }
        $decks = parent::getDB($this->table, $params);
        return $decks;
    }

    //insertar un mazo en la lista de mazos de usuario
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
            if (isset($params['deckImage'])) {
                $img_array = explode(';base64,', $params['deckImage']);
                $extension = strtoupper(explode('/', $img_array[0])[1]);
                $datos_imagen = $img_array[1];
                $nombre_imagen = uniqid();
                $path = dirname(__DIR__, 1) . "/public/img/" . $nombre_imagen . "." . $extension;
                file_put_contents($path, base64_decode($datos_imagen)); //subimos la imagen al servidor.
                $params['deckImage'] = $nombre_imagen . '.' . $extension;
            }
            return parent::insertDB($this->table, $params);
        }
    }

    //actualizar un mazo de usuario
    public function update($id, $params)
    {
        foreach ($params as $key => $parm) {
            if (!in_array($key, $this->allowedConditions_update)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud dentro del modelo datos'
                );
                Response::result(400, $response);
                exit;
            }
        }

        if ($this->validateUpdate($params)) {
            if (isset($params['deckImage'])) {
                //necesito saber el nombre del fichero antiguo a partir del id y eliminarlo del servidor.
                $decks = parent::getDB($this->table, $_GET);
                $deck = $decks[0];
                $imagen_antigua = $deck['deckImage'];
                $path = dirname(__DIR__, 1) . "/public/img/" . $imagen_antigua;
                if ($imagen_antigua != null && !unlink($path)) {
                    $response = array(
                        'result' => 'warning',
                        'details' => 'No se ha podido eliminar el fichero antiguo'
                    );
                    Response::result(200, $response);
                    exit;
                }
                $img_array = explode(';base64,', $params['deckImage']);
                $extension = strtoupper(explode('/', $img_array[0])[1]);
                $datos_imagen = $img_array[1];
                $nombre_imagen = uniqid();
                $path = dirname(__DIR__, 1) . "/public/img/" . $nombre_imagen . "." . $extension;
                file_put_contents($path, base64_decode($datos_imagen));
                $params['deckImage'] = $nombre_imagen . '.' . $extension;
            }

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

    //eliminar el mazo de un usuario
    public function delete($id)
    {
        //Necesito eliminar su imagen, en el supuesto de que exista.
        // $decks = parent::getDB($this->table, $_GET);
        // $deck = $decks[0];
        // $imagen_antigua = $deck['deckImage'];
        // if (!empty($imagen_antigua)) {
        //     $path = dirname(__DIR__, 1) . "/public/img/" . $imagen_antigua;
        //     if (!unlink($path)) {
        //         $response = array(
        //             'result' => 'warning',
        //             'details' => 'No se ha podido eliminar la imagen del usuario'
        //         );
        //         Response::result(200, $response);
        //         exit;
        //     }
        // }

        parent::deleteDB($this->table_dc, $id);

        $affected_rows = parent::deleteDB($this->table, $id);
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
?>