<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use app\common\model\Country as CountryModel;
use app\common\model\Project as ProjectModel;
use think\Db;
use think\Lang;
use think\Request;
use think\Session;

class Project extends HomeBase
{
    protected function _initialize()
    {
        if (!Session::has('user_id'))
        {
            echo "login wrong";
            $this->redirect_add('index/login');
        }
 
        parent::_initialize();

        $this->country_model       = new CountryModel();
        $this->assign('country_list', $country_list);
    }
    public function state_check($project_id = 0)
    {
       if (!Session::has('user_id'))
        {
            $this->error(Lang::get("remind11"));
        }
        if (!Session::has('identity') || Session::get('identity') != 1)
        {
            $this->error(Lang::get("p_audit_1"));
        }
        if ($project_id != 0)
        {
            if (!$this->project_model->where(['create_user_id' => $this->user_id, 'id' => $project_id])->count())
            {
                $this->error(Lang::get("p_audit"));
            }
        }
    }
    public function index()
    {
        $where = [];
        $page  = 1;
        // if (!empty($data['seacheamil']))
        // {
        //     $where[] = ['email', 'like', '%' . $data['seacheamil'] . '%'];
        // }
        // if (!empty($data['seachname']))
        // {
        //     $where[] = ['name', 'like', '%' . $data['seachname'] . '%'];
        // }
        // if (!empty($data['seachnum']))
        // {
        //     $where[] = ['item_number', 'like', '%' . $data['seachnum'] . '%'];
        // }
        $limit        = 12;
        $count        = $this->project_model->where($where)->count();
        $project_list = $this->project_model->where($where)
            ->limit(($page - 1) * $limit, $limit)->order('id', 'DESC')->select();
        $this->assign('page', $page);
        $this->assign('limit', $limit);
        $this->assign('count', $count);
        $this->assign('project_list', $project_list);
        return $this->fetch('project_index');
    }

    public function add($step = 0, $project_id = 0)
    {
      
    }
    public function save()
    {
        
    }
    public function addone()
    {
       
    }
    public function addtwo()
    {
       
    }
    public function addteam($project_id, $tid = 0)
    {

    }
    public function addthree($tid = 0)
    {
        
    }
    public function addfour()
    {
        
    }

    public function delete($pid)
    {
       
    }
    public function deleteteam($tid)
    {
       
    }
    public function authmessage($pid)
    {
        
        // $this->assign('view', $view);
        // $this->assign('team', $team);
        // return $this->fetch('project_view');
    }
}
