<?php

namespace core\admin\controller;

use core\base\controller\BaseController;
use core\admin\model\Model;

class IndexController extends BaseController{
    protected function inputData() {

        $db = Model::instance();

        exit('admin panel');
    }
}