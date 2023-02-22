<?php
require_once '../modelos/user.class.php';
require_once '../modelos/auth.class.php';
require_once '../respuestas/response.php';


/**
 ***********SÓLO PARA EL LOGEO************
 * 
 * EndPoint que tiene definido el método POST y lo que 
 * recibe es un username y un password. Tienen que coincidir
 * en la bbdd y si coindice, genera el token para devolverlo al 
 * usuario que deberá agregarlo a su encabezado para posteriormente
 * usarlo en los demás endpoint con normalidad.
 * 
 * Para probarlo por primera vez, voy a crear un usuario y utilizaré la página
 * https://emn178.github.io/online-tools/sha256  para que a partir de una password
 * me genere una contraseña encriptada. Utilizo los datos username = santi y password=santi
 * 
 * Generaremos una llamada a nuestro endpoint auth, donde con el método post le pasaremos
 * nuestro usuario y contraseña que mandamos desde un login. Probamos con username = "santi" y
 * password="santi". Lo que nos deberá generar es un token y lo devolerá para que lo siguamos utilizando
 * en el resto de endpoint. Hay que tener en cuenta, que nuestro endpoint, a partir del password, utilizará
 * un encoder basado en sha256 para encriptar la password y comprobar que efectivamente es la que tiene
 * en la tabla.
 * 
 * Con la password codificada, tenemos que calcular su token, para ello llamamos al método signInt de la clase
 * auth.class. ¿Cómo se genera el token y cual es su flujo?
 */

 /*
 PODRÍA HACER QUE EL TOKEN DURARA POR UN TIEMPO Y QUE ÉSTE SE REFRESCARA HACIENDO UN NUEVO ENDPOINT.
 TENDRÍA QUE AÑADIR EL TIEMPO DE FINALIZACIÓN, JUNTO CON EL TIEMPO DE CREACIÓN DE TOKEN QUE YA UTILIZO.
 NO LO VOY A HACER, PORQUE NO ME DA LA GANA....
 */
$auth = new Authentication();  //crea un objeto con la tabla, la key privada.

//dependiendo del método request, tiene que ser un POST.
switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		$user = json_decode(file_get_contents('php://input'), true);

		//PARANOIA PARA DEPURAR CUANDO LA COSA VA MALLLL...
//		$auth->insertarLog("Entra en el POST de autenticacion"); exit;

		$token = $auth->signIn($user);
		// echo $token;

		//desde el token, saco el id.
		
		$nick = $auth->getNick($token);

		$response = array(
			'result' => 'ok',
			'token' => $token,
			'user_nick' => $nick
		);

		// Se devuelve el token correctamente.
		Response::result(201, $response);

		break;
}