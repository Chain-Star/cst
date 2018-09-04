<?php
namespace app\index\controller;

use app\common\controller\HomeBase;
use app\common\model\Country as CountryModel;
use app\common\model\OrganizationProjectList as OrganizationProjectListModel;
use app\common\model\Project as ProjectModel;
use app\common\model\ProjectAudit as ProjectAuditModel;
use app\common\model\ProjectField as ProjectFieldModel;
use app\common\model\ProjectPlan as ProjectPlanModel;
use app\common\model\ProjectRoute as ProjectRouteModel;
use app\common\model\ProjectTeam as ProjectTeamModel;
use app\common\model\ProjectWhiteBook as WhiteBookModel;
use app\common\model\Review as ReviewModel;
use app\common\model\TransactionLog as TransactionLogModel;
use app\common\model\User as UserModel;
use think\Cookie;
use think\Db;
use think\Lang;
use think\Session;

class Invest extends HomeBase
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->user_model           = new UserModel();
        $this->project_model        = new ProjectModel();
        $this->project_field_model  = new ProjectFieldModel();
        $this->country_model        = new CountryModel();
        $this->white_book_model     = new WhiteBookModel();
        $this->route_model          = new ProjectRouteModel();
        $this->project_team_model   = new ProjectTeamModel();
        $this->project_plan_model   = new ProjectPlanModel();
        $this->project_audit_model  = new ProjectAuditModel();
        $this->Review_model         = new ReviewModel();
        $this->transaction_model    = new TransactionLogModel();
        $this->organization_p_model = new OrganizationProjectListModel();
        $country_list               = $this->country_model->select();
        $field_list                 = $this->project_field_model->select();
        set_time_limit(60);
        if ($this->lang_status() == 'en')
        {
            foreach ($field_list as $k => $v)
            {
                $field_list[$k]['cn'] = $v['en'];
            }
        }
        foreach ($field_list as $value)
        {
            $menu[] = $value->toArray();
        }
        $menu = !empty($menu) ? array2level($menu) : [];
        // print_r($menu);
        $this->assign('country_list', $country_list);
        $this->assign('field_list', $menu);
    }

    public function index($seachtest = 0, $field_id = 0, $seachstate = 0)
    {
        $where = [];
        $order = 'id';
        $des   = 'DESC';

        if (!empty($seachtest))
        {
            $where['name'] = ['like', '%' . $seachtest . '%'];
        }
        if (!empty($field_id))
        {
            // 判断是否有子类 -- Zhanglu
            $son_id = $this->project_field_model->field('id')->where('pid', $field_id)->select();
            if (!empty($son_id))
            {
                $id_tmp = array();
                foreach ($son_id as $k => $v)
                {
                    array_push($id_tmp, $v['id']);
                }
                $where['field_id'] = ['in', $id_tmp];

            }
            else
            {
                $where['field_id'] = ['=', $field_id];
            }
        
        }
        if (!empty($seachstate))
        {
            if ($seachstate == 'count')
            {
                $order = 'click_count';
            }
            else if ($seachstate == 'bg_time')
            {
                $order = 'a.audit_time';
            }
            else if ($seachstate == 'en_time')
            {
                $order = 'p.end_time';
                $des   = 'ASE';
            }
            else if ($seachstate == 'hot')
            {
                $order = 'raise';
            }
        }
        $data = $this->request->param();

        $page = 1;
        if (!empty($data['page']))
        {
            $page = $data['page'];
        }

        $limit = 12;
        if (!empty($data['project_id']))
        {
            $count        = 1;
            $project_list = $this->project_model->alias('u')
                ->field('u.*,f.cn,f.en,
                p.token,p.currency,p.price,p.plan,p.wallet_path,p.locked,p.start_time,p.end_time,p.sum,p.actual,
                c.cn as country,a.audit_time,sum(t.amount) as raise,
                s.basic_star,s.team_star,s.book_star,s.plan_star,s.route_star')
                ->join('country c', "u.country_id=c.id", 'left')
                ->join('project_field f', "u.field_id=f.id", 'left')
                ->join('project_audit a', "u.id=a.project_id", 'left')
                ->join('project_score s', "u.id=s.project_id", 'left')
                ->join('transaction_log t',["t.project_id=u.id","t.state=1"], 'left')
                // ->join('project_raise r', "u.id=r.project_id", 'left')
                ->join('project_plan p', "u.id=p.project_id", 'left')->where('u.audit_state', 1)->where('u.id', $data['project_id'])
                ->where('u.is_organization', 0)
                ->group('u.id')
                ->select();
        }
        else
        {
            $count        = $this->project_model->where($where)->where('audit_state', 1)->count();
            $project_list = $this->project_model->alias('u')
                ->field('u.*,f.cn,f.en,
                p.token,p.currency,p.price,p.plan,p.wallet_path,p.locked,p.start_time,p.end_time,p.sum,p.actual,
                c.cn as country,a.audit_time,sum(t.amount) as raise,
                s.basic_star,s.team_star,s.book_star,s.plan_star,s.route_star')
                ->join('country c', "u.country_id=c.id", 'left')
                ->join('project_field f', "u.field_id=f.id", 'left')
                ->join('project_audit a', "u.id=a.project_id", 'left')
                ->join('project_score s', "u.id=s.project_id", 'left')
                ->join('transaction_log t',["t.project_id=u.id","t.state=1"], 'left')
                ->join('project_plan p', "u.id=p.project_id", 'left')->where('u.audit_state', 1)->where($where)
                ->where('u.is_organization', 0)
                ->group('u.id')
                ->limit(($page - 1) * $limit, $limit)->order($order, $des)->select();
        }
        $this->assign('page', $page);
        $this->assign('limit', $limit);
        $this->assign('count', $count);
        $this->assign('count1', ceil($count / $limit));
        foreach ($project_list as $key => $value)
        {
            $project_list[$key]['sum']    = number_format($value['sum']);
            $project_list[$key]['actual'] = number_format($value['actual']);
            // $project_list[$key]['plan'] = number_format($value['plan']);
        }
        $this->assign('project_list', $project_list);

        return $this->fetch('invest_index');
    }

    public function get_pl()
    {
        if ($this->request->isPost())
        {
            
            $this->success(json_encode($review));
        }
    }
    public function push_pl()
    {
        // Session::set('home_user_login_id', 2);
        if (!Session::get('home_user_login_id'))
        {
            $this->error(Lang::get('remind11'));
        }
        if ($this->request->isPost())
        {
            $data = $this->request->param();
            if ($this->Review_model->allowField(true)->save($data))
            {
                $this->success(Lang::get('remind14'));
            }
            else
            {
                $this->error(Lang::get('remind12'));
            }

        }
        $this->error(Lang::get('remind13'));
    }
   
}
