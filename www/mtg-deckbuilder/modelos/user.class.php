<?php
require_once '../respuestas/response.php';
require_once '../modelos-datos/database.php';


class User extends Database
{
    private $table = 'users'; //nombre de la tabla

    //parámetros permitidos para hacer consultas selección.
    //sólo permito hacer consultas get siempre que esten estos parámetros aqui
    private $allowedConditions_get = array(
        'id',
        'nick',
        'email',
        'imageUri',

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

    //parámetros permitidos para la actualización.
    private $allowedConditions_update = array(
        'email',
        'passwd',
        'nick',
        'imageUri',
        'admin',
        'active'
    );


    /**
     * Valida que el campo nick sea obligatorio y su valor distinto a vacío.
     * Llamamos este método desde el insert.
     * También debemos de validar que el disponible, tenga un valor 1 o 0. No puede aceptar
     * otro valor. Debe ser booleano.
     */
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


    /*
    Recorre los parámetros y si no existe alguno de ellos, crea un
    error 400. Si no hubo error, ejecutará el método getDB de la clase
    Database que es el padre.
    Al método que le pasa es el nombre de la tabla y los parámetros. Devuelve
    los objetos de tipo clase.
    */
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

        //ejecuta el método getDB de Database. Contendrá todos los users.
        $users = parent::getDB($this->table, $params);

        return $users;
    }


    /*
    Recorremos todos los parámetros comprobando si están permitidos.
    En el momento que encuentre a un parámetro que no está dentro de los permitidos,
    arma un error 400 y se sale.
    Si no se sale, los parámetros son los correctos, por tanto hay que ejecutar
    una función que valida, porque consideramos que el campo nick debe estar ya que es
    obligatorio y de que no venga su nick vacío.
    Si la validación es correcta, llamamos al método insertDB de database. Nos arma la consulta
    y la ejecutará.
    */
    public function insert($params)
    {
        //recordamos que params, es un array asociaivo del tipo 'id'=>'1', 'nick'=>'santi'
        foreach ($params as $key => $param) {
            //echo $key." = ".$params[$key];
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
        //ejecutará la función que valida los parámetros pasados.

        if ($this->validateInsert($params)) {
            //ahora debemos encriptar la password
            $password_encriptada = hash('sha256', $params['passwd']);
            $params['passwd'] = $password_encriptada;
            //se llama al padre con el método inserDB.
            return parent::insertDB($this->table, $params);
        }
    }


    /**
     * Recibimos el id y los parámetros a modificar.
     * Al igual que antes, comprobamos que todos los parámetros estén dentro de
     * las condiciones permitidas como en el caso del insert. Si hay no está alguno de
     * los parámetros en los permitidos, hay un error.
     *
     * Volvemos a validar los parámetros, ya que debe comprobar que esté el nick y disponible
     * sea booleano. Si da true, llama al método update de la clase database. Le pasamos el nombre
     * de la tabla, el id y los parámetros. La actualización de database, devuelve el número
     * de registros afectados. Comprobamos si ha sido 0, por tanto podemos considerarlo como queramos
     * que en nuestro caso es un error ya que no hubo cambios. En el caso de que hay devuelto más de 0
     * registros, no devuelve nada porque la respuesta la hará desde la clase user.php
     *
     *
     */
    public function update($id, $params)
    {
        foreach ($params as $key => $parm) {
            //debe comprobar que los parámetros son los permitidos.
            //si hubiera otro parámetro como 'codigo', no estaría permitida.
            if (!in_array($key, $this->allowedConditions_update)) {
                unset($params[$key]);
                echo $params[$key];
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud dentro del modelo datos'
                );
                Response::result(400, $response);
                exit;
            }
        }

        /*
        Este método, valida que los datos a actualizar son los correctos y
        obligatorios, como el email, password y si está el parámetro disponible, que sea booleano
        */
        if ($this->validateUpdate($params)) {
            //ahora debemos encriptar la password
            $password_encriptada = hash('sha256', $params['passwd']);
            $params['passwd'] = $password_encriptada;
        }

        //actualizamos el registro a partir de una query que habrá que armar en updateDB
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


    /**
     * Este método, elimina el registro llamando al database. Si
     * el número de registros afectados es 0, no se ha encontrado ese registro
     * y por tanto arma una respuesta de error.
     *
     * Si todo ha ido bien, retorna de la función a user y éste acaba.
     */
    public
        function delete(
        $id
    ) {
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