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
        //expresiones regulares
        $email_pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $password_pattern = '/^[a-zA-Z0-9]{5,}$/';

        //validacion de email
        if (!isset($data['email']) || empty($data['email'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo email es obligatorio'
            );
            Response::result(400, $response);
            exit;
        } else {
            $email = $data['email'];
            if (!preg_match($email_pattern, $email)) {
                $response = array(
                    'result' => 'error',
                    'details' => 'El email no tiene un formato correcto'
                );
                Response::result(400, $response);
                exit;
            }
            //compruebo si el email ya existe
            try {
                $extra = [];
                $extra['email'] = $email;
                $users = parent::getDB($this->table, $extra);
                $exists = count($users);
                if ($exists > 0) {
                    $response = array(
                        'result' => 'error',
                        'details' => 'El email ya esta en uso'
                    );
                    Response::result(400, $response);
                    exit;
                }
            } catch (Exception $e) {
                $response = array(
                    'result' => 'error',
                    'details' => 'Error al consultar el email en la base de datos'
                );
                Response::result(500, $response);
                exit;
            }
        }

        //validacion de nick
        if (!isset($data['nick']) || empty($data['nick'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo nick es obligatorio'
            );
            Response::result(400, $response);
            exit;
        } else {
            //compruebo si el nick ya existe
            $nick = $data['nick'];
            try {
                $extra = [];
                $extra['nick'] = $nick;
                $users = parent::getDB($this->table, $extra);
                $exists = count($users);
                if ($exists > 0) {
                    $response = array(
                        'result' => 'error',
                        'details' => 'El nick ya esta en uso'
                    );
                    Response::result(400, $response);
                    exit;
                }
            } catch (Exception $e) {
                $response = array(
                    'result' => 'error',
                    'details' => 'Error al consultar el nick en la base de datos'
                );
                Response::result(500, $response);
                exit;
            }
        }

        //validacion de password
        if (!isset($data['passwd']) || empty($data['passwd'])) {
            $response = array(
                'result' => 'error',
                'details' => 'La contraseña es obligatoria'
            );
            Response::result(400, $response);
            exit;
        } else {
            $password = $data['passwd'];
            if (!preg_match($password_pattern, $password)) {
                $response = array(
                    'result' => 'error',
                    'details' => 'La contraseña no tiene un formato correcto'
                );
                Response::result(400, $response);
                exit;
            }
        }

        //si los datos son correctos devuelve true
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

    //inserta un usuario en la base de datos 
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
            //se llama al padre con la funcion insertDB.
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
                    'details' => 'Uno de los parametros no esta permitido al actualizar usuario'
                );
                Response::result(400, $response);
                exit;
            }
        }

        if (isset($params['passwd'])) {
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