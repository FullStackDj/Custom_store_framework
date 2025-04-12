<?php

namespace core\user\controller;

use core\base\controller\BaseController;

class IndexController extends BaseController {

    protected $name;

    protected function inputData() {

        $name = 'test2';

        $content = $this->render('', compact('name'));
        $header = $this->render(TEMPLATE . 'header');
        $footer = $this->render(TEMPLATE . 'footer');

        return compact('header', 'content', 'footer');
    }


}