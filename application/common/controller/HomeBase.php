<?php
namespace app\common\controller;

use think\Cache;
use think\Controller;
use think\Db;
use think\Request;
use think\Cookie;
use app\common\model\Category as CateModel;
use app\common\model\ProjectField as projectCateModel;
use think\Session;
//新闻资讯
use app\common\model\Article as ArticleModel;
use app\common\model\Classify as CategoryModel;
use app\common\model\ArticleEn as ArticleEnModel;
//用户
use app\common\model\User as UserModel;
//国家
use app\common\model\Country as CountryModel;
// 轮播图
use app\common\model\NoncoreBanner as BannerModel;
// 实名认证
use app\common\model\Authentication as AuthenticationModel;

class HomeBase extends Controller
{

    protected function _initialize()
    {  
        // 前台首页发现分类
        $this->category_model = new CateModel();
        $this->getDiscovery();
        // 项目分类
        $this->projectCate_model = new projectCateModel();
        $this->getProjectCate();
        // 多语言列表
        $this->language();
        //注册证书
        $this->indexRegister();
        // footer 栏目
        $this->getCategory();
        // 登录用户信息
        $this->isUserLogin();
        // 国家
        $this->country_model =  new CountryModel();
        $this->audit_model =  new AuthenticationModel();
        //设置标题关键字描述
        $this->setSeo();
        // // url后缀
        $this->zllang();
        if($this->lang_status() == 'en')
        {
            $langid = 23;
        }
        elseif($this->lang_status() == 'cn')
        {
            $langid = 24;
        }
        else
        {
            $langid = 24;
        }
        $this->assign('langid',$langid);

        // 国家区号
        $this->getCountryCode();
    }
    
    /**
     * 发现栏目
     */
    protected function getDiscovery()
    {
        $category_list = $this->getPCategory(5);
        $news = $this->category_model->where('id',13)->find();
        if(Request::instance()->has('lang','get'))
        {
            $param = $this->request->param();
            if($param['lang'] == 'en')
            {
                $news['category_name'] = $news['category_name_en'];
            }
        }
        array_push($category_list,$news);
        $this->assign('discovery', $category_list);
    }


    /**
     * 获取栏目
     */
    protected function getPCategory($limit=5)
    {
        $where['pid'] = 2;
        $where['status'] = 1; 
        $category_list = $this->category_model->where($where)->order('id','ASC')->limit($limit)->select();
        if(Request::instance()->has('lang','get'))
        {
            $param = $this->request->param();
            if($param['lang'] == 'en')
            {
                foreach($category_list as $k => $v)
                {
                    $category_list[$k]['content'] = $v['content_en'];
                    $category_list[$k]['category_name'] = $v['category_name_en'];
                }
            }
        }
        
        return $category_list;
    }

    /**
     * 项目分类
     */

     protected function getProjectCate()
     {
        $where['a.pid']=0;
        $pcate = $this->projectCate_model->alias('a')
            ->field('b.*,a.id as id1,a.pid')
            ->join('project_field b', 'b.pid=a.id')
            ->where($where)
            ->select();
        if($this->lang_status() == 'en')
        {
            foreach($pcate as $k => $v)
            {
                $pcate[$k]['cn'] = $v['en'];
            }
        }
        if(!empty($pcate))
        {
            $this->assign('plist', $pcate);
        }
     }

     /**
      * 语言列表
      */
    protected function language()
    {
        $where['pid'] = 19;
        $where['status'] = 1;
        $list = $this->category_model->where($where)->select();
        if($this->lang_status()=='en')
        {
            foreach($list as $k=>$v)
            {
                $list[$k]['category_name'] = $v['category_name_en'];
            }
        }
        $this->assign('language', $list);
    }

    // 底部栏目
    protected function getCategory()
    {
        $plist = $toplist = $this->category_model
            ->where('pid', 0)
            ->order('id','ASC')->select();
        
        $list = $this->category_model
            ->where('pid','>', 0)
            ->where('status',1)
            ->order('id','ASC')->select();
        if(Request::instance()->has('lang','get'))
        {
            $param = $this->request->param();
            if($param['lang'] == 'en')
            {
                foreach($plist as $k => $v)
                {
                    $plist[$k]['category_name'] = $v['category_name_en'];
                }
                foreach($list as $kk => $vv)
                {
                    $list[$kk]['category_name'] = $vv['category_name_en'];
                }
            }
        }
        foreach($plist as $k => $v)
        {
            $tmp = array();
            foreach($list as $kk => $vv)
            {
                if($vv['pid'] == $v['id'])
                {
                    array_push($tmp, $vv);
                }
            }
            $plist[$k]['son'] = $tmp;
        }   

        
        $this->assign('fplist', $plist);
        $this->assign('toplist', $toplist);
    }

    // 会员注册登录证书
    protected function indexRegister()
    {
        $time   = time();
        Session::set('logintime', $time);
        $this->assign('pubkey', $pubkey);
        $this->assign('time', $time);
    }

    /**
     * 新闻资讯
     * @param $limit 限制数量
     * @param $cid 分类id
     * @param $type new 最新 hot 热门 
     * @return Array
     */
    protected function getNews($limit=3,$cid=0,$type='new')
    {
        $news_model = new ArticleModel();
        $news_cat_model = new CategoryModel();
        $news_en_model = new ArticleEnModel();

        $where['state'] = 1;
        if($cid != 0)
        {
            $where['class_id'] = $cid;
        }
        if($type== 'new')
        {
            $order['create_time'] = 'DESC';
        }
        elseif($type == 'hot')
        {
            $order['click'] = 'DESC';
        }
        
       $news_data = $news_model->alias('n')
                ->field('n.*,e.title as title_en, e.content as content_en, e.keysword as keysword_en, c.name, c.name_en')
                ->join('article_en e','n.id=e.article_id')
                ->join('classify c', 'n.class_id=c.id')
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->select();
        foreach($news_data as $k => $v)
        {
            $news_data[$k]['url'] = url('index/news/details', ['id'=>$v['id']]);
            if(Request::instance()->has('lang','get'))
            {
                $param = $this->request->param();
                if($param['lang'] == 'en')
                {
                    $news_data[$k]['title'] = $v['title_en'];
                    $news_data[$k]['content'] = $v['content_en'];
                    $news_data[$k]['name'] = $v['name_en'];
                    $news_data[$k]['keysword'] = $v['keysword_en'];
                }
            }
        }
        return $news_data;
    }

    /**
     * 验证用户是否已登录
     * 
     */
    protected function isUserLogin()
    {
        if(Session::has('home_user_login_id'))
        {
            $user_id = Session::get('home_user_login_id');
            $user_model = new UserModel();
            $user_info = $user_model->where('id',$user_id)->find();
        }
        else
        {
            $user_info = '';
        }
        $this->assign('user_infox', $user_info); 
        
    }

    protected function isUserLoginb()
    {
        if(Session::has('home_user_login_id'))
        {
            $user_id = Session::get('home_user_login_id');
            $user_model = new UserModel();
            $user_info = $user_model->where('id',$user_id)->find();
        }
        else
        {
            $user_info = '';
        }
        return $user_info;
        
    }
/**
 * 获取国家列表
*/
    protected function getCountryList()
	{
        $lang = $this->lang_status()?:'cn';
        $country_list = $this->country_model
            ->field('id,short,'.$lang.' as cn')
            ->order('id', 'ASC')->select();
        
        $this->assign('country_list', $country_list);
    }

    /**
     * 获取轮播图
    */
    protected function getBanneri($cate_id = 20)
    {
        $this->banner_model = new BannerModel();
        // 轮播图
        $where['position_id'] = $cate_id;
        $where['status'] = 1;
        $banner_list = $this->banner_model->where($where)->limit(5)->select();
        if($this->lang_status()=='en')
        {
            foreach($banner_list as $k => $v)
            {
                $banner_list[$k]['banner_title'] = $v['banner_title_en'];
                $banner_list[$k]['banner_description'] = $v['banner_description_en'];
                $banner_list[$k]['banner_tag'] = $v['banner_tag_en'];
            }
        }
        $banner_count= $this->banner_model->where($where)->count();
        return ['bann' => $banner_list, 'count'=>$banner_count];
    }
    /**
     * 设置title keywords description
     */
    protected function setSeo($seo=array('title'=>'','keywords'=>'','description'=>''))
    {
        //获取当前控制器
        $routez = request()->module().'/'.request()->controller().'/'.request()->action();
        //查询栏目名称
        $where['status'] = 1;
        $catename = $this->category_model->select();
        if(empty($seo['title']))
        {
            foreach($catename as $k => $v)
            {
                if($routez == trim($v['url']))
                {
                    if($this->lang_status() == 'en')
                    {
                        $seo['title'] = $v['category_name_en'].'-ChainStar';
                    }
                    else
                    {
                        $seo['title'] = $v['category_name'].'-ChainStar';
                    }
                   
                }            
            }
            if($routez == 'index/Index/index')
            {
                if($this->lang_status() == 'en')
                {
                    $seo['title'] = 'ChainStar Index';
                }
                else
                {
                    $seo['title'] = 'ChainStar 首页';
                }                
            }
        }
        else
        {
            $seo['title'] .='-ChainStar';
        }
        
        $this->assign('seoz',$seo);
    }

//     // 判断语言状态
    public function lang_status()
    {
        if(Request::instance()->has('lang','get'))
        {
            $param = $this->request->param();
            if($param['lang'] == 'en')
            {
                return 'en';
            }
            elseif($param['lang'] == 'cn')
            {
                return 'cn';
            }
            else
            {
                return 'cn';
            }
        }
    }

    public function zllang()
    {
        $lang = $this->lang_status();
        if($lang == 'cn' || empty($lang))
        {
            $lang = '';
        }
        else
        {
            $lang = '?lang='.$lang;
        }
        $this->assign('zllang', $lang);
    }

    public function zllang_controller()
    {
        $lang = $this->lang_status();
        if($lang == 'cn' || empty($lang))
        {
            $lang = '';
        }
        else
        {
            $lang = '?lang='.$lang;
        }
        return $lang;
    }
    //实名认证状态
    protected function getUserAudit($userid)
    {
        $user_audit = $this->audit_model->field('audit_state, user_id')->where('user_id', $userid)->find();
        return $user_audit['audit_state'];
    }

    // 跳转地址转换
    protected function redirect_add($url)
    {
        if($this->lang_status() == 'en')
        {
            $url = url($url).'?lang=en';
        }
        else
        {
            $url = url($url);
        }
        header('Location:'.$url);
        exit();
    }
    /**
     * 获取国家区号
     */
    protected function getCountryCode()
	{
        $lang = $this->lang_status()?:'cn';
        $country_codes = $this->country_model
            ->field('id,short,'.$lang.' as cn, area_code')
            ->where('area_code','>',0)
            ->order('area_code', 'ASC')->select();
        foreach($country_codes as $k=>$code)
        {
            $country_codes[$k]['area_code'] = (int)$code['area_code'];
        }
        $this->assign('country_codes', $country_codes);
    }
}