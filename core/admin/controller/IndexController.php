<?php

namespace core\admin\controller;

use core\base\controller\BaseController;
use core\admin\model\Model;

class IndexController extends BaseController {
    protected function inputData() {

        $db = Model::instance();

        $table = '';

//        $res = $db->get($table, [
//            'fields' => ['id', 'name'],
//            'where' => ['id' => 1, 'name' => 'test2'],
//            'operand' => ['=', '<>'],
//            'condition' => ['AND'],
//            'order' => ['test3', 'name'],
//            'order_direction' => ['ASC', 'DESC'],
//            'limit' => 1
//        ]);

        exit('admin panel');
    }
}