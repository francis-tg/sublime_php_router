<?php

namespace Francis\SublimePhp\ORM;

use Francis\SublimePhp\cli\Cli;
session_start();

use PDO;

class ORM extends db
{
    /**
     * Summary of createTable
     * @param string $table_name
     * @param array $fields
     * @return void
     */

    public function createTable(string $table_name, array $fields):void
    {
        // Start building the SQL query
        $query = "CREATE TABLE IF NOT EXISTS $table_name (\n";
        // Add each field to the query
        foreach ($fields as $field_name => $field_type) {
            $query .= "$field_name $field_type,\n";
        }

        // Remove the trailing comma and add the closing parenthesis
        $query = rtrim($query, ",\n") . "\n)";

        // Execute the query
        $this->pdo->exec($query);
    }
    /**
     * Summary of createRelationship
     * @param string $table_name
     * @param string $field_name
     * @param string $related_table
     * @param string $related_field
     * @return void
     */
    public function createRelationship(string $table_name, string $field_name, string $related_table, string $related_field)
    {
        $fq = "SELECT table_name,
            column_name,
            referenced_table_name,
            referenced_column_name
            FROM information_schema.key_column_usage
            WHERE table_name='$table_name' AND referenced_table_name = '$related_table'";
        $f = $this->pdo->prepare($fq);
        $f->execute();
        if (empty($f->fetch()["referenced_table_name"]) == true) {
            $query = "ALTER TABLE $table_name ADD FOREIGN KEY ($field_name) REFERENCES $related_table($related_field) ON DELETE CASCADE ON UPDATE CASCADE";
            $this->pdo->exec($query);

        }
    }
    /**
     * Summary of addColumn
     * @param string $table_name
     * @param string $field_name
     * @param string $value
     * @return void
     */
    public function addColumn(string $table_name, string $field_name, string $value):bool
    {
        try {
            $query = "ALTER TABLE $table_name ADD IF NOT EXISTS $field_name $value";
            Cli::consoleLog("info", $query);
            $this->pdo->exec($query);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
    /**
     * Summary of select
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param string $order_by
     * @param string $limit
     * @param array $include
     * @return mixed
     */
    public function select(string $table, array $fields = ["*"], array $where = [], string $order_by = "", string $limit = "", array $include = []):?array
    {
        $query = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        if (count($include) > 0) {
            $joins = "";
            foreach ($include as $r_table => $r_column) {
                $joins .= " JOIN $r_table ON $table" . "." . $r_column . " = " . $r_table . ".id ";
            }
            $query .= $joins;
        }
        if (isset($where)&&count($where) !== 0) {
            $joins_where = "";

            $shift_array = array_shift($where);

            $extractfirst = array_keys($shift_array)[0];

            $joins_where .= " WHERE $extractfirst = '" . $shift_array[$extractfirst] . "'";
            foreach ($where as $column => $value) {
                $joins_where .= " AND " . array_keys($value)[0] . " = '" . array_values($value)[0] . "'";
            }
            $query .= $joins_where;
        }
        if ($order_by != "") {
            $query .= " ORDER BY " . $order_by;
        }
       if($limit!==""){
        $query .= " LIMIT " . $limit;

       }
        Cli::consoleLog("info", $query);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    /**
     * Summary of selectOne
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param string $order_by
     * @param array $include
     * @return mixed
     */
    public function selectOne(string $table, array $fields = ["*"], array $where = [], string $order_by = "", array $include = []): ?array
    {
        $limit = 1;
        $query = "SELECT " . implode(", ", $fields) . " FROM " . $table;
        if (count($include) > 0) {
            $joins = "";
            foreach ($include as $r_table => $r_column) {
                $joins .= " JOIN $r_table ON $table" . "." . $r_column . " = " . $r_table . ".id ";
            }
            $query .= $joins;
        }
        
        if (!empty($where)) {
            $joins_where = "";
            
            // On extrait le premier élément du tableau $where
            $first_condition = array_shift($where);
            //var_dump($first_condition);
            // On vérifie que l'élément extrait n'est pas vide et est bien un tableau
            if (!empty($first_condition) && is_array($first_condition)) {
                // On récupère le nom de la première colonne et sa valeur
                $extractfirst_column = key($first_condition);
                $extractfirst_value = current($first_condition);
                
                // On construit la clause WHERE pour le premier élément
                $joins_where .= " WHERE $extractfirst_column = '$extractfirst_value'";
                // On parcourt les autres conditions du tableau $where
                foreach ($where as $column => $value) {
                    // On vérifie que la condition est bien un tableau
                    if (is_array($value)) {
                        // On récupère le nom de la colonne et sa valeur
                        $column_name = key($value);
                        $column_value = current($value);
                        
                        // On ajoute la condition à la clause WHERE
                        $joins_where .= " AND $column_name = '$column_value'";
                    }
                }
            }
            // On ajoute la clause WHERE construite à la requête
            //var_dump($joins_where);
            $query .= $joins_where;
        }

        if ($order_by != "") {
            $query .= " ORDER BY " . $order_by;
        }
        $query .= " LIMIT " . $limit;
        Cli::consoleLog("info", $query);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    /**
     * Summary of INSERT
     * @param string $table
     * @param array $data
     * @return mixed
     */
    public function insert(string $table, array $data)
    {
        $query = "INSERT INTO " . $table . " (" . implode(", ", array_keys($data)) . ") VALUES (:" . implode(", :", array_keys($data)) . ")";
        $stmt = $this->pdo->prepare($query);
        Cli::consoleLog("info", $query);

        return $stmt->execute($data);
    }

    /**
     * Summary of update
     * where 'id='.$user["id"] ||  * where 'id=1'
     * @param string $table
     * @param array $data
     * @param string $where
     * @return mixed
     *
     *
     */
    public function update(string $table, array $data, string $where)
    {
        $query = "UPDATE " . $table . " SET ";
        $query_parts = array();
        foreach ($data as $field => $value) {
            $query_parts[] = $field . " = :" . $field;
        }
        $query .= implode(", ", $query_parts) . " WHERE " . $where;
        $stmt = $this->pdo->prepare($query);
        Cli::consoleLog("info", $query);

        return $stmt->execute($data);
    }
    /**
     * Summary of DELETE
     * @param string $table
     * @param string $where
     * @return mixed
     */
    public function delete(string $table, string $where)
    {
        $query = "DELETE FROM " . $table . " WHERE " . $where;
        $stmt = $this->pdo->prepare($query);
        Cli::consoleLog("info", $query);

        return $stmt->execute();
    }

}
