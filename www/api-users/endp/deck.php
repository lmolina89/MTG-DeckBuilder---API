<?php
require_once '../respuestas/response.php';
require_once '../modelos/deck.class.php';
require_once '../modelos/auth.class.php';

$auth = new Authentication();  //***** CAPA DE AUTHENTICATION  *****/
//Compara que el token sea el correcto y que la decodificación con clave privada
//sea la correcta.
$auth->verify();  /* VERIFICAMOS LA AUTENTICACIÓN.  */
//hasta aquí, el token está perfectamente verificada.
$deck = new deck();  //creamos un objeto de la clase deck.

switch ($_SERVER['REQUEST_METHOD']) {	
	case 'GET':
		$params = $_GET; //leemos los parámetros por URL
		/*
		Dentro de la clase deck, están todas las validaciones. El método get recibe
		los parámetros y devuelve un array con los datos en forma de array.
		*/
		if (isset($_GET['id']) && !empty($_GET['id'])){
            // echo "Pasamos id_usuario es ".$_GET['id_usuario']." y el id del token es ".$auth->getIdUser();
            if ($_GET['id'] != $auth->getIdUser()){
                $response = array(
                    'result' => 'error',
                    'details' => 'El id no corresponde con el del usuario autenticado. '
                ); 
                Response::result(400, $response);
			    exit;
            }
        }else{
            //hay que añadir a $params el id del usuario.
            $params['user_id'] = $auth->getIdUser();
        }


		$decks = $deck->get($params);
        //$auth->insertarLog('lleva a solicitud de decks');
        // $url_raiz_img = "http://".$_SERVER['HTTP_HOST']."/api-users/public/img";
		// for($i=0; $i< count($decks); $i++){
		// 	if (!empty($decks[$i]['imagen']))
		// 		$decks[$i]['imagen'] = $url_raiz_img ."/". $decks[$i]['imagen'];
		// }

        $response = array(
            'result'=> 'ok',
            'decks'=> $decks
        );
       // $auth->insertarLog('devuelve decks'); 
        Response::result(200, $response);
        break;


		/*
		Los parámetros en caso de inserción, no se define en la URL, sino en el body
		con los datos de un JSON. Por ejemplo, 
		{
			"nombres"= "Juan",
			"disponible"=1
		}

		Al momento de enviarlo, vemos que es un POST y recuperamos los parámetro a través de
		la función json_decode(file_get_contents()). Transforma en un array asociativo
		Si hizo un post pero no recibe ningún dato en JSON, devuelve un error 400. En caso de que
		si llegaran bien los parámetros, pasamos a ejecutar nuestro insert con los parámetros.

		Nos devuelve el id del registro insertado. Después armamos la respuesta. 201 de create.
		*/
	case 'POST':
		/*
		Los parámetros del body, los recupera a partir de la función file_get_contents('php://input')
		Decodificamos ese json a partir de json_decode y lo transforma a un array asociativo dentro de params.
		*/
		$params = json_decode(file_get_contents('php://input'), true);  //supongo que se envía por @body

		/*
		Comprueba si existen parámetros. Si no existe, devuelve la respuesta de error 400.
		*/
		if(!isset($params)){
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud'
			);

			Response::result(400, $response);
			exit;
		}

		$params['user_id'] = $auth->getIdUser();

		//aquí insertamos el nuevo deck a partir de nuestro objeto deck.
		$insert_id_deck = $deck->insert($params);
		$params["id"] = $insert_id_deck;


		$response = array(
			'result' => 'ok',
			'insert_id' => $insert_id_deck,
			'user_id' => $params['user_id']
		);

		Response::result(201, $response);


		break;

/**
 * Los campos que queremos editar, también van el el body, pero el id va en la URL
 * Decodifica el json recibido como en el post y comprobamos si tenemos un id. En caso
 * contrario, hay un error porque no sabemos qué registro hay que actualizar.
 * 
 * Llamamos al método update con el id y los parametros a modificar.
 * Al finalizar, se arma la respuesta ok.
 */

	case 'PUT':
		//volvemos a pasar nuestro json a un arry asociativo
		$params = json_decode(file_get_contents('php://input'), true);

		/*
		Es obligatorio que al editar un deck, exista el parámetro id y valor.
		*/
		if(!isset($params) || !isset($_GET['id']) || empty($_GET['id'])){
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud de actualización'
			);

			Response::result(400, $response);
			exit;
		}

		//actualizamos por id.
		$deck->update($_GET['id'], $params);
		/**
		 * toca actualizar el token del deck, ya que modificó obligatoriamente
		 * el campo email.
		 */
		// $auth->modifyToken($_GET['id'], $params["email"]);
		$response = array(
			'result' => 'ok',
			'details' => 'Se ha actualizado correctamente'
		);

		Response::result(200, $response);
		
		break;

/**
 * Comprueba que le hemos pasado por url el id y que no esté vacío. En cuyo caso, armamos la 
 * respuesta de error y nos salimos. Llamamos al método delete pasándole el id y éste
 * eliminará dicho registro. Por último arma la respuesta ok.
 */

	case 'DELETE':

		/*
		Es obligatorio el id por GET
		*/
		// $params = $_REQUEST;
		// print_r($_GET);exit;
		if(!isset($_GET['id']) || empty($_GET['id'])){
			$response = array(
				'result' => 'error',
				'details' => 'Error en la solicitud de eliminacion'
			);

			Response::result(400, $response);
			exit;
		}
		//eliminamos al deck, cuya id pasamos.
		$deck->delete($_GET['id']);

		$response = array(
			'result' => 'ok'
		);

		Response::result(200, $response);
		break;
	default:
		$response = array(
			'result' => 'error'
		);

		Response::result(404, $response);

		break;
}
?>