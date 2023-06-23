<?php
require_once '../respuestas/response.php';
require_once '../modelos-datos/database.php';

class User extends Database
{
    //tablas
    private $table = 'users'; //nombre de la tabla

    //parametros validos para GET
    private $allowedConditions_get = array(
        'id',
        'nick',
        'email',
        'imageUri'
    );

    //parámetros permitidos para la inserción. Al hacer el POST
    private $allowedConditions_insert = array(
        'email',
        'passwd',
        'nick',
        'imageUri',
        'active',
        'admin'
    );

    //parámetros permitidos para PUT.
    private $allowedConditions_update = array(
        'email',
        'passwd',
        'nick',
        'imageUri',
        'admin',
        'active'
    );

    //validar insertar usuario
    private function validateInsert($data)
    {
        if (!isset($data['email']) || empty($data['email'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo email es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }

        if (!isset($data['nick']) || empty($data['nick'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo nick es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }

        if (!isset($data['passwd']) || empty($data['passwd'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El password es obligatoria'
            );
            Response::result(400, $response);
            exit;
        }
        return true;
    }

    //validar actualizar usuario
    private function validateUpdate($data)
    {
        if (!isset($data['email']) || empty($data['email'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo email es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }

        if (!isset($data['passwd']) || empty($data['passwd'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El password es obligatorio'
            );
            Response::result(400, $response);
            exit;
        }
        return true;
    }

    //busca todos los usuarios de la base de datos
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
        $users = parent::getDB($this->table, $params);
        return $users;
    }

    //inserta un usuario en la base de datos (igual que el endpoint register)
    public function insert($params)
    {
        foreach ($params as $key => $param) {
            if (!in_array($key, $this->allowedConditions_insert)) {
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud. Parametro no permitido'
                );
                Response::result(400, $response);
                exit;
            }
        }

        if ($this->validateInsert($params)) {
            //ahora debemos encriptar la password
            $password_encriptada = hash('sha256', $params['passwd']);
            $params['passwd'] = $password_encriptada;
            //se llama al padre con el método inserDB.
            return parent::insertDB($this->table, $params);
        }
    }

    //actualiza un usuario
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
            $password_encriptada = hash('sha256', $params['passwd']);
            $params['passwd'] = $password_encriptada;
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

    //elimina un usuario de la base de datos
    public function delete($id)
    {
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