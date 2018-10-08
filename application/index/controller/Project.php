<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use app\common\model\Country as CountryModel;
use app\common\model\Project as ProjectModel;
use app\common\model\ProjectField as ProjectFieldModel;
use app\common\model\ProjectPlan as ProjectPlanModel;
use app\common\model\ProjectRoute as ProjectRouteModel;
use app\common\model\ProjectTeam as ProjectTeamModel;
use app\common\model\ProjectWhiteBook as WhiteBookModel;
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
        $this->project_model       = new ProjectModel();
        $this->project_field_model = new ProjectFieldModel();
        $this->white_book_model    = new WhiteBookModel();
        $this->route_model         = new ProjectRouteModel();
        $this->project_team_model  = new ProjectTeamModel();
        $this->project_plan_model  = new ProjectPlanModel();
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
        $this->state_check($project_id);
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('project_id', $project_id);
        if ($project_id != 0)
        {
            $istrue = $this->project_model->where('create_user_id', $this->user_id)->where('id', $project_id)->count();
            if (empty($istrue))
            {
                $this->redirect_add('index/index/index');
            }
        }
        if ($step == 0)
        {
            if ($project_id != 0)
            {
                $first                = $this->project_model->find($project_id);
                $first['account_url'] = json_decode($first['account_url'], true);
                $this->assign('first', $first);
                return $this->fetch('project_edit');
            }
            else
            {
                return $this->fetch('project_add');
            }
        }
        else if ($step == 1)
        {
            $second = $this->white_book_model->where(['project_id' => $project_id])->find();
            if ($second)
            {
                $second['book_path'] = json_decode($second['book_path'], true);
                $this->assign('second', $second);
                return $this->fetch('project_oneedit');
            }
            return $this->fetch('project_oneadd');
        }
        else if ($step == 2)
        {
            $third = $this->route_model->where(['project_id' => $project_id])->find();
            if ($third)
            {
                $this->assign('third', $third);
                return $this->fetch('project_twoedit');
            }
            return $this->fetch('project_twoadd');
        }
        else if ($step == 3)
        {
            $team_list = $this->project_team_model->where('project_id', $project_id)->order('id', 'ASC')->select();
            if (empty($team_list))
            {
                return $this->addteam($project_id);
            }
            $this->assign('team_list', $team_list);
            return $this->fetch('project_teamlist');
        }
        else
        {
            $fourth = $this->project_plan_model->where(['project_id' => $project_id])->find();
            if ($fourth)
            {
                $this->assign('fourth', $fourth);
                return $this->fetch('project_fouredit');
            }
            return $this->fetch('project_fouradd');
        }
    }
    public function save()
    {
        if ($this->request->isPost())
        {
            $data            = $this->request->param();
            $validate_result = $this->validate($data, 'ProjectOne.one');
            if ($validate_result !== true)
            {
                $this->error($validate_result);
            }
            if (!empty($data['project_id']))
            {
                $this->state_check($data['project_id']);
            }
            else
            {
                $this->state_check();
            }
        }
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
