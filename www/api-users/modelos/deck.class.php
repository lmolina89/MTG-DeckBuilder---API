<?php
require_once '../respuestas/response.php';
require_once '../modelos-datos/database.php';

class Deck extends Database
{
    private $table = 'deck'; //nombre de la tabla

    //parámetros permitidos para hacer consultas selección.
//sólo permito hacer consultas get siempre que esten estos parámetros aqui
    private $allowedConditions_get = array(
        'user_id',
        'name'
    );

    //parámetros permitidos para la inserción. Al hacer el POST
    private $allowedConditions_insert = array(
        'id',
        'user_id',
        'name'
    );

    //parámetros permitidos para la actualización.
    private $allowedConditions_update = array(
        'id',
        'name',
        'deckImage'
    );


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



        // if (isset($data['deckImage']) && !empty($data['deckImage'])) {

        //     /*
        //     separo por la secuencia ;base64, el lado derecho, 
        //     es el archivo y el lado izquierdo el tipo de fichero(formato codificación).
        //     */
        //     $img_array = explode(';base64,', $data['deckImage']);
        //     //hago un explode para separar por / y la parte derecha es la extensión y la izquierda 'data_image'
        //     //lo paso a mayúsculas, por tanto tengo JPEG ó PNG ó JPG
        //     $extension = strtoupper(explode('/', $img_array[0])[1]); //me quedo con jpeg
        //     if ($extension != 'PNG' && $extension != 'JPG' && $extension != 'JPEG') {
        //         $response = array('result' => 'error', 'details' => 'Formato de la imagen no permitida, sólo PNG/JPE/JPEG');
        //         Response::result(400, $response);
        //         exit;
        //     } //fin extensión
        //     /*echo "La imagen es: ".$img_array[1]."<br>";
        //     echo "La extensión es: ".$extension;
        //     exit;*/
        // } //fin isset 



        return true;
    }


    private function validateUpdate($data)
    {
        // echo $_GET['id'];exit;
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $response = array(
                'result' => 'error',
                'details' => 'El campo id es obligatorio'
            );

            Response::result(400, $response);
            exit;
        }

        // if (isset($data['deckImage']) && !empty($data['deckImage'])) {
        // 	$img_array = explode(';base64,', $data['deckImage']);
        // 	$extension = strtoupper(explode('/', $img_array[0])[1]); //me quedo con jpeg
        // 	if ($extension != 'PNG' && $extension != 'JPG' && $extension != 'JPEG') {
        // 		$response = array('result' => 'error', 'details' => 'Formato de la imagen no permitida, sólo PNG/JPE/JPEG');
        // 		Response::result(400, $response);
        // 		exit;
        // 	} //fin extensión
        // } //fin isset 

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

        //ejecuta el método getDB de Database. Contendrá todos los decks.
        if($params['user_id'] == 1){
            return parent::getDB($this->table);
        }
        $decks = parent::getDB($this->table, $params);
        return $decks;
    }


    public function insert($params)
    {
        // print_r($params);exit;
        //recordamos que params, es un array asociaivo del tipo 'id'=>'1', 'nick'=>'santi'
        foreach ($params as $key => $param) {
            // echo $key." = ".$params[$key]." ";exit;
            if (!in_array($key, $this->allowedConditions_insert)) {
                print_r($key);
                unset($params[$key]);
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la solicitud. Parametro no permitido al insertar'
                );

                Response::result(400, $response);
                exit;
            }
        }
        //ejecutará la función que valida los parámetros pasados.

        if ($this->validateInsert($params)) {

            // if (isset($params['deckImage'])) {
            // 	/*echo "Tiene imagen";
            // 	exit;*/
            // 	$img_array = explode(';base64,', $params['deckImage']); //datos de la imagen
            // 	$extension = strtoupper(explode('/', $img_array[0])[1]); //formato de la imagen
            // 	$datos_imagen = $img_array[1]; //aqui me quedo con la imagen
            // 	$nombre_imagen = uniqid(); //creo un único id.
            // 	//del directorio actual de user.class, subo un nivel (1) y estando en el directorio api-pueblos, concateno public\img
            // 	$path = dirname(__DIR__, 1) . "/public/img/" . $nombre_imagen . "." . $extension;
            // 	/*echo "La imagen es ".$nombre_imagen.".".$extension;
            // 	echo "El path es ".$path;
            // 	exit;*/
            // 	file_put_contents($path, base64_decode($datos_imagen)); //subimos la imagen al servidor.
            // 	$params['deckImage'] = $nombre_imagen . '.' . $extension; //pasamos como parametro en foto, con el nombre y extensión completo.
            // 	//exit;  //hay que quitarlo una vez verificado que se sube la imagen
            // } //fin isset

            //se llama al padre con el método inserDB.
            return parent::insertDB($this->table, $params);
        }
    }

    public function update($id, $params)
    {
        foreach ($params as $key => $parm) {
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
 
            //Si mandamos imagen.
            // if (isset($params['deckImage'])) {
            //     //necesito saber el nombre del fichero antiguo a partir del id y eliminarlo del servidor.
            //     $decks = parent::getDB($this->table, $_GET);
            //     $deck = $decks[0];
            //     $imagen_antigua = $deck['deckImage'];
            //     //echo $imagen_antigua;
            //     $path = dirname(__DIR__, 1) . "/public/img/" . $imagen_antigua;
            //     //si no puedo eliminar la imagen antigua, lo indico.
            //     if ($imagen_antigua != null && !unlink($path)) {
            //         $response = array(
            //             'result' => 'warning',
            //             'details' => 'No se ha podido eliminar el fichero antiguo'
            //         );
            //         Response::result(200, $response);
            //         exit;

            //     }

            //     /*foreach ($usu as $item => $value) 
            //     echo $item.": ".$value;
            //     */
            //     //exit;

            //     //ahora tengo que crear la nueva imagen y actualizar registro.

            //     // $img_array = explode(';base64,', $params['deckImage']); //datos de la imagen
            //     // $extension = strtoupper(explode('/', $img_array[0])[1]); //formato de la imagen
            //     // $datos_imagen = $img_array[1]; //aqui me quedo con la imagen
            //     // $nombre_imagen = uniqid(); //creo un único id.
            //     // $path = dirname(__DIR__, 1) . "/public/img/" . $nombre_imagen . "." . $extension;
            //     // file_put_contents($path, base64_decode($datos_imagen)); //subimos la imagen al servidor.
            //     // $params['deckImage'] = $nombre_imagen . '.' . $extension; //pasamos como parametro en foto, con el nombre y extensión completo.
            // } //fin isset




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
    }

	public function delete($id)
	{
		//Necesito eliminar su imagen, en el supuesto de que exista.	
		$decks = parent::getDB($this->table, $_GET);
        // print_r($decks);exit;
		$deck = $decks[0];
		$imagen_antigua = $deck['deckImage'];
		if (!empty($imagen_antigua)) {
			$path = dirname(__DIR__, 1) . "/public/img/" . $imagen_antigua;
			if (!unlink($path)) {
				$response = array(
					'result' => 'warning',
					'details' => 'No se ha podido eliminar la imagen del usuario'
				);
				Response::result(200, $response);
				exit;
			}
		}

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