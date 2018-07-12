<?php
namespace app\common\controller;

use org\Auth;
use think\Controller;
use think\Db;
use think\Loader;
use think\Session;

/**
 * 后台公用基础控制器
 * Class 
 * @package app\common\controller
 */
class AuditBase extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->checkAuth();
        $this->getMenu();
        // 输出当前请求控制器（配合后台侧边菜单选中状态）
        $this->assign('controller', Loader::parseName($this->request->controller()));
    }

    /**
     * 权限检查
     * @return bool
     */
    protected function checkAuth()
    {
        if (!Session::has('audit_id'))
        {
            $this->redirect('audit/Login/index');
        }
    }

    /**
     * 获取侧边栏菜单
     */
    protected function getMenu()
    {
         
    }
}
