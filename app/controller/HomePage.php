<?php
namespace app\controller;

use app\BaseController;
use app\Common\ArrayToolkit;
use think\facade\Db;
use think\facade\View;
use think\Request;

class HomePage extends BaseController
{

    public function index(Request $request): string
    {
        $list = Db::name('course')
            ->order('id', 'desc')
            ->paginate([
                'list_rows'=>60,
                'var_page' => 'page',
                'query' => []
            ]);
        $teacherIds = array_unique(array_filter(array_column($list->items(), 'teacherId')));
        $users = empty($teacherIds)?[]:Db::name('user')
            ->where('id','in' ,$teacherIds)
            ->select();
        $users = empty($teacherIds)?[]:ArrayToolkit::index($users->toArray(), 'id');

        $list->each(
            function (&$course,$key,$users){
                $course['teacher'] = empty($users[$course['teacherId']]) ? '--':$users[$course['teacherId']]['nickname'];
                return $course;
            },
            $users
        );
        $page = $list->render();
        return  View::fetch('homepage/index', [ 'list' => $list,
            'page' => $page,]);
    }

}
