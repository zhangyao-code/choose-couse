<?php
namespace app\controller;

use app\BaseController;
use app\Biz\User\UserService;
use think\annotation\Route;

use think\facade\Db;
use think\facade\Session;
use think\facade\View;
use think\Request;

class Login extends BaseController
{
    public function login(Request $request)
    {
        if(!$request->isGet()){
            $login = $request->request();
            $UserService = new UserService($this->app);
            $user = $UserService->getUserByNickname($login['username']);
            if(empty($user)){
                return  json(['status'=>'error','message'=>'用户不存在']);
            }
            if($user['password']!=$login['password']){
                return  json(['status'=>'error','message'=>'密码错误']);
            }
            Session::set('user.id', $user['id']);
            Session::set('user.nickname', $user['nickname']);
            Session::set('user.email', $user['email']);
            Session::set('user.loginDate', time());
            Session::set('flag', true);
            $UserService->updateUser($user['id'], ['loginDate'=>time()]);
            return  redirect('/');
        }
      return  View::fetch('login/index');
    }

    public function check()
    {
        $data = $this->request->param();
        $user = Db::table('user')->where('nickname',$data['nickname'])->find();
        return \json(!empty($user)&&$user['password']==$data['password']);
    }

    /**
     * @param Request $request
     * @return string
     * @Route("logout")
     */
    public function logout(Request $request)
    {
        session(null);
        return  redirect('login');
    }
}
