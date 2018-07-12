<?php
namespace app\common\model;

use think\Model;
use think\Session;

class NoncoreProjects extends Model
{
    protected $auto = [];
    protected $insert = ['create_time'];

    protected function setCreateTimeAttr()
    {
        return time();
    }
}