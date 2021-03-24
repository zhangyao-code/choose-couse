<?php
namespace app\controller;

use app\BaseController;
use think\annotation\Route;
use think\facade\Db;
use think\facade\Session;
use think\facade\View;
use think\Request;

class Course extends BaseController
{
    /**
     * @return string
     * @Route("/course")
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $data = $this->request->request();
        $name = empty($data['name']) ? '': $data['name'];

        $list = Db::name('course')->order('id', 'desc')->paginate(1000);
        if(!empty($name)){
            $list = Db::name('course')->where('name', $name)->order('id', 'desc')->paginate(1000);
        }
        $page = $list->render();
        return  View::fetch('admin/course/list', ['list' => $list, 'page' => $page, 'name'=>$name]);

    }

    public function create()
    {
        if(!$this->request->isGet()) {
            $data = $this->request->request();
            Db::name('course')->save($data);
            return  redirect('course');
        }

        return  View::fetch('admin/course/create');

    }

}
