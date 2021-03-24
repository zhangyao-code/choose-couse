<?php
namespace app\Biz;
use think\App;
use \think\db\exception\ModelNotFoundException;
use think\facade\Db;

class BaseService {

    protected $table = '';

    protected $DB = null;

    public function __construct(App $app)
    {
       if(empty($this->table)){
           throw new ModelNotFoundException('缺少必要参数');
       }
       $this->getDB();
    }


    private function getDB()
    {
        if(empty($this->DB)){
            $this->DB = Db::table($this->table);
        }
        return $this->DB;
    }
}