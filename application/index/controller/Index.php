<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use think\Db;
use app\common\model\Team as TeamModel;
use app\common\model\Article as ArticleModel;
use app\common\model\NoncoreBanner as BannerModel;
use app\common\model\Project as ProjectModel;
use think\Session;

class Index extends HomeBase
{
    protected function _initialize()
	{
		parent::_initialize();
        $this->banner_model = new BannerModel();
        $this->project_model = new ProjectModel();
    }
    
    public function index()
    {
    // 轮播图
        $where['position_id'] = 20;
        $where['status'] = 1;
        $banner_list = $this->banner_model->where($where)->limit(5)->select();
        $banner_count= $this->banner_model->where($where)->count();
        if($this->lang_status() == 'en')
        {
            foreach($banner_list as $k=>$v)
            {
                $banner_list[$k]['img_path'] = $v['img_path_en'];
            }
        }
        $this->assign('banner_c', $banner_count);
        $this->assign('banner_list', $banner_list);
        // 中间4大块
        $this->assign('tcategory', $this->getPCategory(4));

        // 热门资讯
        $this->assign('news_last',$this->getNews(3,0,'new'));
        // 最新项目
        $this->assign('new_project', $this->getPorject());
        // 热门项目
        $this->assign('hot_project', $this->getPorject(9,'hot'));
        return $this->fetch();
    }

    /**
     * 获取项目
     */
    private function getPorject($limit=9,$type='new')
    {
       $field = 'u.*,f.cn,f.en,
       p.token,p.currency,p.price,p.plan,p.wallet_path,p.locked,p.start_time,p.end_time,p.sum,p.actual,
       c.cn as country,
       s.basic_star,s.team_star,s.book_star,s.plan_star,s.route_star,sum(tl.raise) as amount';
        $join = [
            ['country c', "u.country_id=c.id", 'left'],
            ['project_field f', "u.field_id=f.id", 'left'],
            ['project_score s', "u.id=s.project_id", 'left'],
            ['project_plan p', "u.id=p.project_id", 'left']
        ];
        array_push($join, ['project_raise tl','u.id=tl.project_id','left']);
        $order['id'] = 'DESC';
        if($type == 'hot')
        {
            $order = ['amount'=>'DESC'];
        }
        $where['audit_state'] = 1;
        $project_list = $this->project_model->alias('u')
        ->field($field)
        ->join($join)
        ->where($where)
        ->group('u.id')
        ->limit($limit)->order($order)->select();
        foreach($project_list as $k=>$v)
        {
            $project_list[$k]['scale'] = (int)(($v['amount'] / $v['plan'])*100) . "%";
            $project_list[$k]['scale1'] = ($v['amount'] / $v['plan'])*100;
        }
        return $project_list;
    }
}
