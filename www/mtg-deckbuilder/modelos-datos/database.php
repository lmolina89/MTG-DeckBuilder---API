<?php
class Database
{
    private $connection; //guardará la conexión
//constructor que inicializa la conexion con la base de datos
    public function __construct()
    {
        $host = 'db';
        $dbname = 'usersDB';
        $username = 'root';
        $password = 'example';

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error de conexión a la base de datos: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }

    //busca todas las cartas de un mazo
    public function getDBJoin($table_keys, $table_card, $extra = null)
    {
        $query = "SELECT c.*, dc.numCards as num_cards FROM $table_card c INNER JOIN $table_keys dc ON c.id = dc.card_id";

        $params = array();
        if ($extra != null) {
            $query .= ' WHERE dc.';
            $conditions = array();
            foreach ($extra as $key => $condition) {
                $conditions[] = $key . ' = ?';
                $params[] = $condition;
            }
            $query .= implode(' AND ', $conditions);
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    //hace una consulta sql de GET de usuarios y mazos
    public function getDB($table, $extra = null)
    {
        $query = "SELECT * FROM $table";

        $params = array();
        if ($extra != null) {
            $query .= ' WHERE ';
            $conditions = array();
            foreach ($extra as $key => $condition) {
                $conditions[] = $key . ' = ?';
                $params[] = $condition;
            }
            $query .= implode(' AND ', $conditions);
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    //inserta cartas en la tabla cards y despues asocia su ID con la ID de mazo en la tabla deckCard

    public function insertDBJoin($table_keys, $table_cards, $data)
    {
        $cards_db = $this->getDB($table_cards);
        $deck_id = $data['deck_id'];
        $card_id = $data['id'];
        unset($data['deck_id']);
        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
        $query_numCards = "UPDATE deck AS d JOIN (SELECT deck_id, SUM(numCards) AS totalCards FROM deckcard GROUP BY deck_id) AS dc ON d.id = dc.deck_id SET d.numCards = dc.totalCards";
        $query = "INSERT INTO $table_keys (deck_id, card_id, numCards) VALUES (:deck_id, :card_id, 1)";
        $query_card = "INSERT INTO $table_cards ($fields) VALUES ($placeholders)";
        $exist = false;

        foreach ($cards_db as $card_db) {
            if ($data['id'] == $card_db['id']) {
                $exist = true;
            }
        }

        if (!$exist) {
            try {
                $statement = $this->connection->prepare($query_card);
                $statement->execute($data);
            } catch (PDOException $e) {
                $response = array(
                    'result' => 'error',
                    'details' => 'Error en la inserción de la carta: ' . $e->getMessage()
                );
                Response::result(400, $response);
                exit;
            }
        }

        $statement = $this->connection->prepare($query);
        $statement->bindParam(':deck_id', $deck_id, PDO::PARAM_INT);
        $statement->bindParam(':card_id', $card_id, PDO::PARAM_STR);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Esta carta ya existe en el mazo'
            );
            Response::result(400, $response);
            exit;
        }

        $this->connection->query($query_numCards);
        return true;
    }


    //inserta mazos y usuarios en la base de datos
    public function insertDB($table, $data)
    {
        try {
            $fields = implode(',', array_keys($data));
            $values = '"' . implode('","', array_values($data)) . '"';
            $query = "INSERT INTO $table ($fields) VALUES ($values)";

            $statement = $this->connection->prepare($query);
            $statement->execute();

            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la inserción: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }

    //actualiza mazos y usuarios de la base de datos
    public function updateDB($table, $id, $data)
    {
        try {
            $query = "UPDATE $table SET ";

            if (isset($data['active'])) {
                $data['active'] = $data['active'] ? 1 : 0;
            }

            if (isset($data['admin'])) {
                $data['admin'] = $data['admin'] ? 1 : 0;
            }

            foreach ($data as $key => $value) {
                $query .= "$key = :$key";
                if (sizeof($data) > 1 && $key != array_key_last($data)) {
                    $query .= " , ";
                }
            }

            $query .= ' WHERE id = :id';

            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);

            foreach ($data as $key => $value) {
                $statement->bindValue(":$key", $value);
            }

            $statement->execute();

            if (!$statement->rowCount()) {
                return 0;
            }

            return $statement->rowCount();
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la actualización: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }

    //actualiza las cartas de un mazo de la base de datos en la tabla deckCard
    public function updateDBJoin($table_dc, $table_c, $data)
    {
        $query_numCards = "UPDATE deck AS d JOIN ( SELECT deck_id, SUM(numCards) AS totalCards FROM deckcard GROUP BY deck_id) AS dc ON d.id = dc.deck_id SET d.numCards = dc.totalCards";
        $query_up = "UPDATE $table_dc SET numCards = LEAST(numCards + 1, 4) WHERE deck_id = :deck_id AND card_id = :card_id";
        $query_down = "UPDATE $table_dc SET numCards = GREATEST(numCards - 1, 1) WHERE deck_id = :deck_id AND card_id = :card_id";

        try {
            if ($data['action'] == "up") {
                $statement = $this->connection->prepare($query_up);
            } elseif ($data['action'] == "down") {
                $statement = $this->connection->prepare($query_down);
            } else {
                $response = array(
                    'result' => 'error',
                    'details' => 'parametros de action introducidos diferentes de "up" o "down"'
                );
                Response::result(400, $response);
                exit;
            }

            $statement->bindValue(':deck_id', $data['deck_id'], PDO::PARAM_INT);
            $statement->bindValue(':card_id', $data['card_id'], PDO::PARAM_STR);
            $statement->execute();

            if (!$statement->rowCount()) {
                return false;
            }
            $this->connection->query($query_numCards);
            return true;
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la actualización: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }

    //elimina una carta de la tabla deckCard
    public function deleteDBJoin($table_dc, $data)
    {
        $deck_id = $data['deck_id'];
        $card_id = $data['card_id'];
        $query = "DELETE FROM $table_dc WHERE deck_id = :deck_id AND card_id = :card_id";
        $query_numCards = "UPDATE deck AS d JOIN ( SELECT deck_id, SUM(numCards) AS totalCards FROM deckcard GROUP BY deck_id) AS dc ON d.id = dc.deck_id SET d.numCards = dc.totalCards";

        try {

            $statement = $this->connection->prepare($query);
            $statement->bindValue(':deck_id', $deck_id, PDO::PARAM_INT);
            $statement->bindValue(':card_id', $card_id, PDO::PARAM_STR);
            $statement->execute();
            if (!$statement->rowCount()) {
                return 0;
            }
            $this->connection->query($query_numCards);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error al eliminar: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }

    // elimina usuarios, mazos y cartas de sus respectivas tablas

    public function deleteDB($table, $id)
    {
        try {
            if ($table === 'users') {
                // Obtener los IDs de los mazos asociados al usuario
                $decks = $this->getDB('deck', ['user_id' => $id]);
                // Eliminar los mazos asociados
                foreach ($decks as $deck) {
                    //elimina las relaciones de cartas con el mazo
                    $this->deleteDB('deckcard', $deck['id']);
                    //elimina el mazo vacio
                    $this->deleteDB('deck', $deck['id']);
                }
                // Eliminar al usuario
                $query = "DELETE FROM " . $table . " WHERE id = :id";
                $statement = $this->connection->prepare($query);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                return $statement->rowCount();
            } else {
                // Eliminar otros elementos de la base de datos
                $query = "DELETE FROM " . $table . " WHERE id = :id";
                //si la tabla es deckcard cambia 'id' por 'deck_id'
                if ($table === 'deckcard') {
                    $query = "DELETE FROM $table WHERE deck_id = :id";
                }
                $statement = $this->connection->prepare($query);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                //si no hay cambios devuelve 0
                if (!$statement->rowCount()) {
                    return 0;
                }

                return $statement->rowCount();
            }
        } catch (PDOException $e) {
            $response = array(
                'result' => 'error',
                'details' => 'Error en la eliminación: ' . $e->getMessage()
            );
            Response::result(400, $response);
            exit;
        }
    }


}
?>