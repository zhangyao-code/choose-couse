<?php
namespace app\controller;

use app\BaseController;
use League\Flysystem\NotSupportedException;
use think\annotation\Route;
use think\facade\Db;
use think\facade\View;
use think\Response;
use think\response\Json;

class User extends BaseController
{
    /**
     * @return string
     * @Route("user")
     * @throws \think\db\exception\DbException
     */
    public function index(): string
    {
        $data = $this->request->param();
        $nickname = empty($data['nickname']) ? '': $data['nickname'];

        $list = Db::name('user')
            ->where('nickname','like' ,'%'.$nickname.'%')
            ->where('isDelete', 0)
            ->order('id', 'desc')
            ->paginate([
            'list_rows'=>10,
            'var_page' => 'page',
            'query' => $data
        ]);

        $list->each(
            function (&$user){
                $user['roles'] = str_replace('student', '学生', $user['roles']);
                $user['roles'] = str_replace('teacher', '教师', $user['roles']);
                $user['roles'] = str_replace('admin', '管理员', $user['roles']);
                return $user;
            }
        );

        $page = $list->render();

        return  View::fetch('admin/user/list', ['list' => $list, 'page' => $page, 'nickname'=>$nickname, 'currentUser'=>$this->app['user']->toArray()]);

    }

    public function create()
    {
        if(!$this->request->isGet()) {
            $data = $this->request->request();
            $data['roles'] = empty($data['roles']) ? 'student' : implode($data['roles'], ',');
            Db::name('user')->save($data);

            return  redirect('/user');
        }

        return  View::fetch('admin/user/create');

    }

    public function check()
    {
        $data = $this->request->param();
        $key = array_keys($data);
        $result = Db::query("select * from user where {$key[0]}=:data limit 1", ['data' => $data[$key[0]]]);

        return \json(empty($result));
    }

    public function checkUser($id)
    {
        $data = $this->request->param();
        $key = array_keys($data);
        $result = Db::query("select * from user where {$key[0]}=:data limit 1", ['data' => $data[$key[0]]]);

        return \json(empty($result) || $id==$result[0]['id']);
    }

    public function update($id)
    {
        $user = Db::table('user')->where('id', $id)->find();
        if(!$this->request->isGet()) {
            $data = $this->request->request();
            $data['roles'] = empty($data['roles']) ? 'student' : implode($data['roles'], ',');
            Db::name('user')->where('id', $id)->update($data);

            return  redirect('/user');
        }

        return  View::fetch('admin/user/update', ['user'=>$user]);

    }

    public function delete($id)
    {
        $user = Db::table('user')->where('id', $id)->find();
         if(!empty($user)){
             $user['isDelete'] = 1;
             Db::table('user')->where('id', $id)->update($user);
         }
        return  redirect('/user');

    }

}
