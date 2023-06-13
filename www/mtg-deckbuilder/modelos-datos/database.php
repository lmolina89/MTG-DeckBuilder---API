<?php
class Database
{
    private $connection; //guardará la conexión
    private $results_page = 50; //número de resultados por página.

    public function __construct()
    {
        $this->connection = new mysqli('db', 'root', 'example', 'usersDB', '3306');
        if ($this->connection->connect_errno) {
            echo 'Error de conexión a la base de datos';
            exit;
        }
    }

    public function getDBJoin($table_keys, $table_card, $extra = null)
    {
        $page = 0;
        $query = "SELECT c.*, dc.numCards as num_cards FROM $table_card c INNER JOIN $table_keys dc ON c.id = dc.card_id";

        if (isset($extra['page'])) {
            $page = $extra['page'];
            unset($extra['page']);
        }

        if ($extra != null) {
            $query .= ' WHERE dc.';
            foreach ($extra as $key => $condition) {
                $query .= $key . ' = "' . $condition . '"';
                if ($extra[$key] != end($extra)) {
                    $query .= " AND ";
                }
            }
        }

        $results = $this->connection->query($query);
        $resultArray = array();

        foreach ($results as $value) {
            $resultArray[] = $value;
        }
        return $resultArray;
    }

    public function getDB($table, $extra = null)
    {
        $query = "SELECT * FROM $table";

        if ($extra != null) {
            $query .= ' WHERE';
            foreach ($extra as $key => $condition) {
                $query .= ' ' . $key . ' = "' . $condition . '"';
                if ($extra[$key] != end($extra)) {
                    $query .= " AND ";
                }
            }
        }

        $results = $this->connection->query($query);
        $resultArray = array();

        foreach ($results as $value) {
            $resultArray[] = $value;
        }
        return $resultArray;
    }

    public function insertDBJoin($table_keys, $table_cards, $data)
    {
        $cards_db = $this->getDB($table_cards);
        $deck_id = $data['deck_id'];
        $card_id = $data['id'];
        unset($data['deck_id']);
        $fields = implode(',', array_keys($data));
        $values = '"';
        $values .= implode('","', array_values($data));
        $values .= '"';
        $query_numCards = "UPDATE deck AS d JOIN ( SELECT deck_id, SUM(numCards) AS totalCards FROM deckcard GROUP BY deck_id) AS dc ON d.id = dc.deck_id SET d.numCards = dc.totalCards";
        $query = "INSERT INTO $table_keys (deck_id,card_id,numCards) VALUES ($deck_id," . "'" . $card_id . "',1)";
        $query_card = "INSERT INTO $table_cards (" . $fields . ') VALUES (' . $values . ')';
        $exist = false;

        foreach ($cards_db as $card_db) {
            if ($data['id'] == $card_db['id']) {
                $exist = true;
            }
        }

        if (!$exist) {
            $this->connection->query($query_card);
        }

        $this->connection->query($query);

        if ($this->connection->affected_rows != 1) {
            $response = array(
                'result' => 'error',
                'details' => $this->connection->error_list
            );
            Response::result(400, $response);
            exit;
        }
        $this->connection->query($query_numCards);
        return true;
    }

    public function insertDB($table, $data)
    {
        $fields = implode(',', array_keys($data));
        $values = '"';
        $values .= implode('","', array_values($data));
        $values .= '"';
        $query = "INSERT INTO $table (" . $fields . ') VALUES (' . $values . ')';
        $this->connection->query($query);
        return $this->connection->insert_id;
    }

    public function updateDB($table, $id, $data)
    {
        $query = "UPDATE $table SET ";

        if (isset($data['active'])) {
            if ($data['active'] == true) {
                $data['active'] = 1;
            } else {
                $data['active'] = 0;
            }
        }

        if (isset($data['admin'])) {
            if ($data['admin'] == true) {
                $data['admin'] = 1;
            } else {
                $data['admin'] = 0;
            }
        }

        foreach ($data as $key => $value) {
            $query .= "$key = '$value'";
            if (sizeof($data) > 1 && $key != array_key_last($data)) {
                $query .= " , ";
            }
        }

        $query .= ' WHERE id = ' . $id;

        $this->connection->query($query);

        if (!$this->connection->affected_rows) {
            return 0;
        }

        return $this->connection->affected_rows;
    }

    public function updateDBJoin($table_dc, $table_c, $data)
    {
        $query_numCards = "UPDATE deck AS d JOIN ( SELECT deck_id, SUM(numCards) AS totalCards FROM deckcard GROUP BY deck_id) AS dc ON d.id = dc.deck_id SET d.numCards = dc.totalCards";
        $query_up = "UPDATE $table_dc SET numCards = LEAST(numCards + 1, 4) WHERE deck_id = " . $data['deck_id'] . " AND card_id = '" . $data['card_id'] . "'";
        $query_down = "UPDATE $table_dc SET numCards = GREATEST(numCards - 1, 1) WHERE deck_id = " . $data['deck_id'] . " AND card_id = '" . $data['card_id'] . "'";

        if ($data['action'] == "up") {
            $this->connection->query($query_up);
        } elseif ($data['action'] == "down") {

            $this->connection->query($query_down);
        } else {
            $response = array(
                'result' => 'error',
                'details' => 'parametros de action introducidos diferentes de "up" o "down"'
            );
            Response::result(400, $response);
            exit;
        }

        if (!$this->connection->affected_rows) {
            return false;
        }
        $this->connection->query($query_numCards);
        return true;
    }

    public function deleteDBJoin($table_dc, $data)
    {
        $deck_id = $data['deck_id'];
        $card_id = $data['card_id'];
        $query = "DELETE FROM $table_dc WHERE deck_id = $deck_id AND card_id = '" . $card_id . "'";
        $this->connection->query($query);
        if (!$this->connection->affected_rows) {
            return 0;
        }
        return $this->connection->affected_rows;
    }

    public function deleteDB($table, $id)
    {
        $query = "DELETE FROM " . $table . " WHERE id = " . $id;
        if ($table == 'deckcard') {
            $query = "DELETE FROM $table WHERE deck_id = $id";
        }
        $this->connection->query($query);
        if (!$this->connection->affected_rows) {
            return 0;
        }
        return $this->connection->affected_rows;
    }
}
?>