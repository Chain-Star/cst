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
       
    }
    public function index()
    {
        
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
