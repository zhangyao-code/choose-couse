<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::rule('login', 'Login/login');
Route::rule('login_check', 'Login/check');
Route::rule('/', 'HomePage/index');
Route::rule('user/create', 'User/create');
Route::rule('user/create_check', 'User/check');
Route::rule('user/update_check/:id', 'User/checkUser');
Route::rule('user/update/:id', 'User/update');
Route::rule('user/delete/:id', 'User/delete');
Route::rule('course/create', 'Course/create');
