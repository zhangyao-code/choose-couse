<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\Request;

class HomePage extends BaseController
{

    public function index(Request $request): string
    {
        return  View::fetch('homepage/index');
    }

}
