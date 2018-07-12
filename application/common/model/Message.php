<?php
namespace app\common\model;

use think\Model;
use think\Session;

class Message extends Model
{
    protected $auto = [];
    protected $insert = ['create_time','create_id'];  
    // protected $update = ["last_login_time","last_login_ip"];  
    
    protected function setCreateTimeAttr()
    {
        return time();
    }
    protected function setCreateIdAttr()
    {
        return Session::get('admin_id');
    }
}