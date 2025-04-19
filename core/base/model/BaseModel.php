<?php

namespace core\base\model;

use core\admin\model\Model;
use core\base\controller\Singleton;
use core\base\exceptions\DbException;

class BaseModel extends BaseModelMethods {

    use Singleton;

    protected $db;

    private function __construct() {
        $this->db = @new \mysqli(HOST, USER, PASS, DB_NAME);

        if ($this->db->connect_error) {

            throw new DbException('Connection failed: ' . $this->db->connect_errno . ' ' . $this->db->connect_error);
        }

        $this->db->query("SET NAMES UTF8");
    }

    /**
     * @param $query
     * @param $crud = r - SELECT / c - INSERT / u - UPDATE / d - DELETE
     * @param $return_id
     * @return array|bool
     * @throws DbException
     */

    final public function query($query, $crud = 'r', $return_id = false) {

        $result = $this->db->query($query);

        if ($this->db->affected_rows === -1) {
            throw new DbException('Query failed: ' . $query . ' - ' . $this->db->errno . ' ' . $this->db->error);
        }

        switch ($crud) {
            case 'r':

                if ($result->num_rows) {

                    $res = [];

                    for ($i = 0; $i < $result->num_rows; $i++) {
                        $res[] = $result->fetch_assoc();
                    }

                    return $res;
                }

                return false;

            case 's':

                if ($return_id) return $this->db->insert_id;

                return true;

            default:

                return true;
        }
    }

    /**
     * @param $table
     * @param array $set
     * 'fields' => ['id', 'name'],
     * 'where' => ['id' => 1, 'name' => 'test2'],
     * 'operand' => ['=', '<>'],
     * 'condition' => ['AND'],
     * 'order' => ['test3', 'name'],
     * 'order_direction' => ['ASC', 'DESC'],
     * 'limit' => 1
     */

    final public function get($table, $set = []) {

        $fields = $this->createFields($set, $table);

        $order = $this->createOrder($set, $table);

        $where = $this->createWhere($set, $table);

        if (!$where) $new_where = true;
        else $new_where = false;

        $join_arr = $this->createJoin($set, $table, $new_where);

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];

        $fields = rtrim($fields, ',');

        $limit = $set['limit'] ? 'LIMIT ' . $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    /**
     * @param $table - the table for inserting data
     * @param array $set - array of parameters:
     * fields => [field => value]; - if not specified, it processes $_POST[field => value]
     * passing NOW() as a MySQL function in the form of a regular string is allowed
     * files => [field => value]; - an array in the form [field => [array of values]] can be provided
     * except => ['exception 1', 'exception 2'] - excludes these elements from the array in the query
     * return_id => true | false - whether to return the ID of the inserted record or not
     * @return mixed
     */

    final public function add($table, array $set = []) {

        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;

        if (!$set['fields'] && !$set['files']) return false;

        $set['return_id'] = (bool)$set['return_id'];
        $set['except'] = (is_array($set['except']) && !empty($set['except'])) ? $set['except'] : false;

        $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

        if ($insert_arr) {
            $query = "INSERT INTO $table ({$insert_arr['fields']}) VALUES ({$insert_arr['values']})";
            return $this->query($query, 'c', $set['return_id']);
        }

        return false;
    }

    final public function edit($table, $set = []) {

        $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;

        if (!$set['fields'] && !$set['files']) return false;

        $set['except'] = (is_array($set['except']) && !empty($set['except'])) ? $set['except'] : false;

        if (!$set['all_rows']) {

            if ($set['where']) {
                $where = $this->createWhere($set);
            } else {

                $columns = $this->showColumns($table);

                if (!$columns) return false;

                if ($columns['id_row'] && $set['fields'][$columns['id_row']]) {
                    $where = 'WHERE ' . $columns['id_row'] . '=' . $set['fields'][$columns['id_row']];
                    unset($set['fields'][$columns['id_row']]);
                }
            }
        }

        $update = $this->createUpdate($set['fields'], $set['files'], $set['except']);

        $query = "UPDATE $table SET $update $where";

        return $this->query($query, 'u');
    }

    final public function showColumns($table) {

        $query = "SHOW COLUMNS FROM $table";
        $res = $this->query($query);

        $columns = [];

        if ($res) {

            foreach ($res as $row) {
                $columns[$row['Field']] = $row;
                if ($row['Key'] === 'PRI') $columns['id_row'] = $row['Field'];
            }
        }

        return $columns;
    }
}