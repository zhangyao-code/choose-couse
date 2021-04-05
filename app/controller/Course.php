<?php
namespace app\controller;

use app\BaseController;
use app\Common\ArrayToolkit;
use League\Flysystem\NotSupportedException;
use think\annotation\Route;
use think\db\exception\DataNotFoundException;
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
        $data = $this->request->param();

        $name = empty($data['name']) ? '': $data['name'];

        $list = Db::name('course')
            ->where('name','like' ,'%'.$name.'%')
            ->order('id', 'desc')
            ->paginate([
                'list_rows'=>10,
                'var_page' => 'page',
                'query' => $data
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
        return  View::fetch('admin/course/list', [
            'list' => $list,
            'page' => $page,
            'name'=>$name,
        ]);

    }

    public function create()
    {
        if(!$this->request->isGet()) {
            $data = $this->request->request();
            $data['randImg'] = rand(1,5);
            Db::name('course')->save($data);
            return  redirect('/course');
        }

        return  View::fetch('admin/course/create');

    }

    public function update($id)
    {
        $course = Db::table('course')->where('id', $id)->find();
        $teacher = Db::table('user')->where('id', $course['teacherId'])->find();
        if(!$this->request->isGet()) {
            $data = $this->request->request();
            Db::name('course')->where('id', $id)->update($data);
            return  redirect('/course');
        }

        return  View::fetch('admin/course/update', [
            'course'=>$course,
            'teacher' => json_encode(empty($teacher)?[]:['id'=>$teacher['id'], 'name'=>$teacher['nickname']])]);
    }

    public function delete($id)
    {
        $course = Db::table('course')->where('id', $id)->find();
        if(!$this->request->isGet()&&!empty($course)) {
            Db::startTrans();
            try {
                Db::table('course')->delete($id);
                Db::table('course_member')->where('courseId',$id)->delete();
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
            }

            return json(true);
        }
    }

    public function member($id)
    {
        $course = Db::table('course')->where('id', $id)->find();
        if(empty($course)){
            throw new DataNotFoundException('课程不存在');
        }
        $data = $this->request->param();

        if(!empty($data['nickname'])){
            $users = Db::name('user')
                ->where('nickname','like' ,'%'.$data['nickname'].'%')
                ->field('id')
                ->select()
                ->toArray();
            $users = empty($users)?[]:ArrayToolkit::column($users, 'id');
        }
        $DB = Db::name('course_member');
        if(!empty($users)){
            $DB = $DB->whereIn('userId',$users);
        }
         $list = $DB->where('courseId', $id)
            ->order('id', 'desc')
            ->paginate([
                'list_rows'=>10,
                'var_page' => 'page',
                'query' => []
            ]);
        $userIds = array_column($list->items(), 'userId');
        $users = empty($userIds)?[]:Db::name('user')
            ->where('id','in' ,$userIds)
            ->field('id,nickname')
            ->select()
            ->toArray();
        $users = empty($users)?[]:ArrayToolkit::index($users, 'id');
        $list->each(
            function (&$member,$key,$users){
                $member['nickname'] = empty($users[$member['userId']]) ? '--':$users[$member['userId']]['nickname'];
                return $member;
            },
            $users
        );
        $page = $list->render();
        return  View::fetch('admin/course/member/list', [
            'list' => $list,
            'page' => $page,
            'nickname' => empty($data['nickname']) ? '' :$data['nickname'],
            'course' => $course
        ]);
    }

    public function addMember($id)
    {
        $course = Db::table('course')->where('id', $id)->find();
        if(empty($course)){
            throw new DataNotFoundException('课程不存在');
        }
        if(!$this->request->isGet()) {
            $userIds = $this->request->request('userIds', [-1]);

            if(!empty($userIds)){
                $members = Db::name('course_member')
                    ->where('courseId',$id)
                    ->whereIn('userId', $userIds)
                    ->field('userId')
                    ->select()
                    ->toArray();
                $existUserIds = array_column($members, 'userId');
                foreach ($userIds as $userId){
                 if(in_array($userId, $existUserIds)){
                     continue;
                 }
                 Db::name('course_member')->save([
                     'courseId'=>$id,
                     'userId' =>$userId,
                     'createdTime' => time()
                 ]);
                }
            }
            return  redirect('/course/'.$id.'/member');

        }
    }

    public function memberDelete($id)
    {
        $member = Db::table('course_member')->where('id', $id)->find();
        if(!$this->request->isGet()&&!empty($member)) {
            Db::startTrans();
            try {
                Db::table('course_member')->delete($id);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
            }

            return json(true);
        }
    }

    public function teacherMatch()
    {
        $data = $this->request->param();
        $name = empty($data['q']) ? '': $data['q'];
        $list = Db::name('user')
            ->where('nickname','like' ,'%'.$name.'%')
            ->where('roles', 'like', '%teacher%')
            ->limit(10)
            ->select();

        $users = [];
        foreach ($list as $user){
            $users[] = ['id'=>$user['id'], 'name'=> $user['nickname']];
        }
        return  json($users);
    }

    public function userMatch($courseId)
    {
        $members = Db::name('course_member')
            ->where('courseId',$courseId)
            ->field('userId')
            ->select()
            ->toArray();
        $usrIds = array_column($members, 'userId');
        $data = $this->request->param();
        $name = empty($data['q']) ? '': $data['q'];
        $list = Db::name('user')
            ->where('id', 'not in', $usrIds)
            ->where('nickname','like' ,'%'.$name.'%')
            ->limit(10)
            ->select();

        $users = [];
        foreach ($list as $user){
            $users[] = ['id'=>$user['id'], 'name'=> $user['nickname']];
        }
        return  json($users);
    }
}
