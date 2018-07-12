<?php
/**
 * 前台用户忘记密码接口
 * @author Anderson 20180516
 */
namespace app\api\controller;

use think\Session;
use think\Controller;
use think\Db;
use app\common\model\User as UserModel;
use think\Request;
use think\Validate;
use think\Lang;
/**
 * 前台注册
 * Class Register
 * @package app\index\controller
 */
class Forgot extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->user_model = new UserModel();
        
    }
    
    //忘记密码，发送邮件
    public function forgetPasswd()
    {
        if( Request::instance()->isPost() )
        {
            $data =  Request::instance()->param();            
        
            // 验证
            
            $rule = [        
                'email' => 'require|email',
                '__token__' => 'token'
            ];
            $msg = [                
                'email.email'  => Lang::get('error_email_e'),
                'email.require' => Lang::get('error_email_empty'),
                '__token__'  => Lang::get('error_no'),
            ];
            $validate   = Validate::make($rule,$msg);
            if (!$validate->check($data)) {
                $this->error($validate->getError(),'',url('index/Forgot/index'));
            }

            $where['email'] = $data['email'];
            $ishas = $this->user_model->field('id,password')->where($where)->find();
            if(empty($ishas))
            {
                $this->error(Lang::get('error_email_no'));
            }
            $mailurl = $data['email'].$ishas['password'];
            if($data['zllang'] == '?lang=en')
            {
                $mailurl .= '&lang=en';
            }
            $title = Lang::get('editpasswd').'-cst.ph';
            $body = '<html><head><title>'.$title.'</title></head><body><a href="'.$mailurl.'">'.$title.'</a></body></html>';
            // 发送邮件             
            $this->success(Lang::get('success_forgot_mail'));            
        }
    }

    // 更新密码
    public function update()
    {
        if(Request::instance()->isPost())
        {
            $data = Request::instance()->param();
            
            // 验证
            if($data['password']!==$data['rpassword'])
            {
                $this->error(Lang::get('rpasswd_error'));
            }

            $rule = [        
                'password' => 'require|length:8,32',
                '__token__' => 'token',
            ];
            $msg = [                
                'password.require'  => Lang::get('error_passwd_empty'),
                'password.length'   => Lang::get('error_passwd'),
                '__token__'  => Lang::get('error_no'),
            ];
            $validate   = Validate::make($rule,$msg);
            if (!$validate->check($data)) {
                $this->error($validate->getError(),'',$data['uri']);
            }
            // 加密
            $udata = $this->user_model->field('email')->where('id', $data['userid'])
                    ->find();
            $rand   = generate_password();
            $updata['password'] = $data['password'];

            if($this->user_model->where('id',$data['userid'])->update($updata))
            {
                $this->success(Lang::get('passwd_success'),url('index/index/index'));
                // 发送邮件-预留
            }
        }
    }    
}
