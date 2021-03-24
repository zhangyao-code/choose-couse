<?php
namespace app\Biz\User;

use app\Biz\BaseService;
use think\facade\Db;

class UserService extends BaseService {

    protected $table = 'user';

    public function getUser($id)
    {
       return $this->DB->where('id',$id)->find();
    }

    public function getUserByNickname($nickname)
    {
        return $this->DB->where('nickname',$nickname)->find();
    }

    public function updateUser($id, $date)
    {
        Db::name($this->table)
            ->where('id', $id)
            ->update($date);
    }

}