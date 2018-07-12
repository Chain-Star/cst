<?php
/**
 * 前台用户注册接口
 * @author Anderson 20180516
 */
namespace app\api\controller;

use think\Session;
use think\Controller;
use think\Db;
use app\common\model\User as UserModel;
use app\common\model\UserLoginLog as UserLogModel;
use think\Request;
use think\Validate;
use think\Lang;
use think\Cookie;
use app\index\controller\EmailQueue;
/**
 * 前台注册
 * Class Register
 * @package app\index\controller
 */
class Register extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->user_model = new UserModel();
        $this->userlog_model = new UserLogModel();
        $this->email_queue = new EmailQueue();
    }
    
    // 注册
    public function register()
    {
        if( Request::instance()->isPost() )
        {
            $data =  Request::instance()->param();
            if($data['zllang'] == '?lang=en')
            {
                Cookie::set('think_var','en');
                Lang::set('success_register','successfully registered, Please login to verify','cn');
            }
            
            //验证用户名
            $ishas = $this->user_model->field('id')->where('name', $data['name'])->find();
            if(!empty($ishas))
            {
                $this->error(Lang::get('error_name_has'));
            }
            // 验证
            if($data['password']!==$data['rpassword'])
            {
                $this->error(Lang::get('rpasswd_error'));
            }
            
            $where['email'] = $data['email'];
            $ishas = $this->user_model->field('id')->where($where)->find();
            if(!empty($ishas))
            {
                $this->error(Lang::get('error_email_has'));
            }
            $rule = [
                'name'  => 'require|length:3,20',
                'password'  => 'require|length:8,32',
                'rpassword'  => 'require|length:8,32',
                'email' => 'require|email',
                '__token__' => 'token',
            ];
            $msg = [
                'name.require' => Lang::get('error_name_empty'),
                'name.length'  => Lang::get('error_name'),
                'password.require'  => Lang::get('error_passwd_empty'),
                'password.length'   => Lang::get('error_passwd_length'),
                'rpassword.require' => Lang::get('error_passwd_empty'),
                'rpassword.length'  => Lang::get('error_passwd_length'),
                'email.require'  => Lang::get('error_email_empty'),
                'email.email'  => Lang::get('error_email_e'),
                '__token__'  => Lang::get('error_no'),
            ];
            $validate   = Validate::make($rule,$msg);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
           
            // 加密
            $rand   = generate_password();
            $data['create_time'] = time();
            $data['last_login_ip'] = ip2long($this->request->ip());
            $data['identity'] = 3; //默认为个人
            $data['state'] = 0; //默认启用
           
            if($this->user_model->allowField(true)->save($data))
            {
                $this->success(Lang::get('success_register'),'',url('index/User/registerVerifivationinfo'));
            }
        }
    }

    //用户登录
    public function login()
    {
        if( Request::instance()->isPost() )
        {
            $data =  Request::instance()->param();
            if($data['zllang'] == '?lang=en')
            {
                Cookie::set('think_var','en');
                Lang::set('success_login','Login Success','cn');
            }
           
            // 验证
            $where['email'] = $data['email'];
            $ishas = $this->user_model->field('id,password,is_email')->where($where)->find();
            if(empty($ishas))
            {
                $token = Request::instance()->token('__token__','sha1');
                $this->error(Lang::get('error_email_no'),"",array('token'=>$token));
            }
            if($ishas['is_email'] == 0)
            {
                $token = Request::instance()->token('__token__','sha1');
                $this->error(Lang::get('remind36'),"",array('token'=>$token));
            }
            // print_r(Session::get('__token__'));die();
            $rule = [
                'password'  => 'require',               
                'email' => 'require|email',
                '__token__' => 'token',
            ];
            $msg = [                
                'password.require'  => Lang::get('error_passwd_empty'),
                'email.email'  => Lang::get('error_email_e'),
                'email.require' => Lang::get('error_email_empty'),
                '__token__'  => Lang::get('error_no'),
            ];
            $validate   = Validate::make($rule,$msg);
            if (!$validate->check($data)) {
                $token = Request::instance()->token('__token__','sha1');
                $this->error($validate->getError(),"",array('token'=>$token));
            }
            
            if($ishas['password']==$data['password'])
            {
                $update['last_login_time'] = time();
                $update['last_login_ip'] = ip2long($this->request->ip());
                
                if($this->user_model->where('id',$ishas['id'])->update($update))
                {
                    Session::set('home_user_login_id', $ishas['id']);
                    Session::set('zllogin_error', 0);
                    $this->loginlog(['uid'=>$ishas['id'],'login_ip'=>$this->request->ip()]);
                    $this->success(Lang::get('success_login'),'',url('index/index/index'));
                }
            }
            else
            {
                $token = Request::instance()->token('__token__','sha1');
                $this->error(Lang::get('error_passwd'),"",array('token'=>$token));
            }
            
            
        }
    }

    //用户退出
    public function logout()
    {
        Session::delete('home_user_login_id');
        $data = $this->request->param();
        if($data['lang'] == '?lang=en')
        {
            Lang::set('logout_success', 'Logout Success');
        }
        
        $this->success(Lang::get('logout_success'), url('index/index/index'));
    }

    // 用户登录日志
    protected function loginlog($data)
    {
        $status = $this->userlog_model->allowField(true)->save($data);
        return $status;
    }

    public function ajaxloginClose()
    {
        Session::set('zllogin_error', 0);
    }
}
