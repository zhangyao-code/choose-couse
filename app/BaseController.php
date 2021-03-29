<?php
declare (strict_types = 1);

namespace app;

use app\Biz\CurrentUser;
use think\App;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Session;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $user = Session::get('user');

        if(!in_array($this->request->baseUrl(), ['/login', '/login_check']) && empty($user)){
            return $this->redirectTo('/login');
        }

        if($this->request->baseUrl() == '/login' && !empty($user)){
            return $this->redirectTo('/');
        }
        bind('user', new CurrentUser($user));
        // 控制器初始化
        $this->initialize();
    }

    /**
     * 自定义重定向方法 重要的操作二
     * @param $args
     */
    public function redirectTo(...$args)
    {
        throw new HttpResponseException(redirect(...$args));
    }


    protected function initialize()
    {

    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

}
