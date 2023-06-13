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
        'imageUri'
    );

    //parámetros permitidos para la actualización.
    private $allowedConditions_update = array(
        'deck_id',
        'card_id',
        'action'
    );

    private function validateInsert($data)
    {
        if (!isset($data['id']) || empty($data['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id de la carta es obligatorio'
            );

            Response::result(400, $response);
            exit;
        }
        return true;
    }

    private function validateInsertDeckCard()
    {
        if (!isset($data['deck_id']) || empty($data['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo deck_id es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }
        return true;
    }

    private function validateUpdate($data)
    {
        if (!isset($data['card_id']) || empty($data['card_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id de la carta es obligatorio'
            );
            Response::result(400, $response);
        }

        if (!isset($data['deck_id']) || empty($data['deck_id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id del mazo es obligatorio'
            );
            Response::result(400, $response);
        }

        if (!isset($data['action']) || empty($data['action'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo action es obligatorio'
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
        return parent::getDBJoin($this->table_dc, $this->table_c, $params);
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
            return parent::insertDBJoin($this->table_dc, $this->table_c, $params);
        }
    }

    public function update($params)
    {
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_update)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud dentro del modelo datos(update)'
                );
                Response::result(200, $response);
                exit;
            }
        }

        if ($this->validateUpdate($params)) {
            if (!parent::updateDBJoin($this->table_dc, $this->table_c, $params)) {
                $response = array(
                    'result' => 'error',
                    'details' => 'No hubo cambios'
                );
                Response::result(200, $response);
                exit;
            }
        }
        return true;
    }


    public function delete($data)
    {
        $affected_rows = parent::deleteDBJoin($this->table_dc, $data);

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