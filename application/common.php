<?php

use PHPMailer\PHPMailer\PHPMailer;
use think\Config;
use think\Cookie;
use think\Db;
use think\Lang;
use think\Request;

Lang::setAllowLangList(['cn', 'en']);
// 解决默认语言不起作用
if (!Request::instance()->has('lang', 'get'))
{
    Cookie::set('think_var', 'cn');
}
else
{
    Cookie::set('think_var', '');
}

/**
 * 获取分类所有子分类
 * @param int $cid 分类ID
 * @return array|bool
 */
function get_category_children($cid)
{
    if (empty($cid))
    {
        return false;
    }

    $children = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->select();

    return array2tree($children);
}

/**
 * 根据分类ID获取文章列表（包括子分类）
 * @param int   $cid   分类ID
 * @param int   $limit 显示条数
 * @param array $where 查询条件
 * @param array $order 排序
 * @param array $filed 查询字段
 * @return bool|false|PDOStatement|string|\think\Collection
 */
function get_articles_by_cid($cid, $limit = 10, $where = [], $order = [], $filed = [])
{
    if (empty($cid))
    {
        return false;
    }

    $ids = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->column('id');
    $ids = (!empty($ids) && is_array($ids)) ? implode(',', $ids) . ',' . $cid : $cid;

    $fileds = array_merge(['id', 'cid', 'title', 'introduction', 'thumb', 'reading', 'publish_time'], (array) $filed);
    $map    = array_merge(['cid' => ['IN', $ids], 'status' => 1, 'publish_time' => ['<= time', date('Y-m-d H:i:s')]], (array) $where);
    $sort   = array_merge(['is_top' => 'DESC', 'sort' => 'DESC', 'publish_time' => 'DESC'], (array) $order);

    $article_list = Db::name('article')->where($map)->field($fileds)->order($sort)->limit($limit)->select();

    return $article_list;
}

/**
 * 根据分类ID获取文章列表，带分页（包括子分类）
 * @param int   $cid       分类ID
 * @param int   $page_size 每页显示条数
 * @param array $where     查询条件
 * @param array $order     排序
 * @param array $filed     查询字段
 * @return bool|\think\paginator\Collection
 */
function get_articles_by_cid_paged($cid, $page_size = 15, $where = [], $order = [], $filed = [])
{
    if (empty($cid))
    {
        return false;
    }

    $ids = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->column('id');
    $ids = (!empty($ids) && is_array($ids)) ? implode(',', $ids) . ',' . $cid : $cid;

    $fileds = array_merge(['id', 'cid', 'title', 'introduction', 'thumb', 'reading', 'publish_time'], (array) $filed);
    $map    = array_merge(['cid' => ['IN', $ids], 'status' => 1, 'publish_time' => ['<= time', date('Y-m-d H:i:s')]], (array) $where);
    $sort   = array_merge(['is_top' => 'DESC', 'sort' => 'DESC', 'publish_time' => 'DESC'], (array) $order);

    $article_list = Db::name('article')->where($map)->field($fileds)->order($sort)->paginate($page_size);

    return $article_list;
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v)
    {
        if ($v['pid'] == $pid)
        {
            $v['level'] = $level;
            $list[]     = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }

    return $list;
}

/**
 * 构建层级（树状）数组
 * @param array  $array          要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name       父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'pid', $child_key_name = 'children')
{
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0)
    {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0)
    {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0)
        {
            array_push($array, $temp);
        }
        else
        {
            if ($temp[$pid_name] == 0)
            {
                $tree[] = $temp;
            }
            else
            {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }

    return $tree;
}

/**
 * 子元素计数器
 * @param array $array
 * @param int   $pid
 * @return array
 */
function array_children_count($array, $pid)
{
    $counter = [];
    foreach ($array as $item)
    {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }

    return $counter;
}

/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name)
{
    foreach ($parent as &$item)
    {
        if ($item['id'] == $pid)
        {
            if (!isset($item[$child_key_name]))
            {
                $item[$child_key_name] = [];
            }

            $item[$child_key_name][] = $child;
        }
    }

    return $parent;
}
function my_log($str, $arr = [])
{
    $logpath = APP_PATH . "../log";
    if (!file_exists($logpath))
    {
        mkdir($logpath);
    }
    $chpath = $logpath . "/" . "chainstar";
    if (!file_exists($chpath))
    {
        mkdir($chpath);
    }
    if (!empty($arr))
    {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        file_put_contents($chpath . "/" . date("Y-m-d") . ".log", $str . "\t" . $arr . "\r\n", FILE_APPEND);
    }
    else
    {
        file_put_contents($chpath . "/" . date("Y-m-d") . ".log", $str . "\r\n", FILE_APPEND);
    }
}
function audit_log($str, $arr = [])
{
    $logpath = APP_PATH . "../log";
    if (!file_exists($logpath))
    {
        mkdir($logpath);
    }
    $chpath = $logpath . "/" . "audit_log";
    if (!file_exists($chpath))
    {
        mkdir($chpath);
    }
    if (!empty($arr))
    {
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        file_put_contents($chpath . "/" . date("Y-m-d") . ".log", $str . "\t" . $arr . "\r\n", FILE_APPEND);
    }
    else
    {
        file_put_contents($chpath . "/" . date("Y-m-d") . ".log", $str . "\r\n", FILE_APPEND);
    }
}

function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false)
{
    if (strlen($str) < $length + 3)
    {
        return $str;
    }
    if (function_exists("mb_substr"))
    {
        if ($suffix)
        {
            return mb_substr($str, $start, $length, $charset) . "...";
        }
        else
        {
            return mb_substr($str, $start, $length, $charset);
        }
    }
    elseif (function_exists('iconv_substr'))
    {
        if ($suffix)
        {
            return iconv_substr($str, $start, $length, $charset) . "...";
        }
        else
        {
            return iconv_substr($str, $start, $length, $charset);
        }
    }
    $re['utf-8']  = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
    $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
    $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
    $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
    {
        return $slice . "…";
    }
    return $slice;

}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name)
{
    $result = false;
    if (is_dir($dir_name))
    {
        if ($handle = opendir($dir_name))
        {
            while (false !== ($item = readdir($handle)))
            {
                if ($item != '.' && $item != '..')
                {
                    if (is_dir($dir_name . DS . $item))
                    {
                        delete_dir_file($dir_name . DS . $item);
                    }
                    else
                    {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name))
            {
                $result = true;
            }
        }
    }

    return $result;
}

/**
 * 判断是否为手机访问
 * @return  boolean
 */
function is_mobile()
{
    static $is_mobile;

    if (isset($is_mobile))
    {
        return $is_mobile;
    }

    if (empty($_SERVER['HTTP_USER_AGENT']))
    {
        $is_mobile = false;
    }
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
    )
    {
        $is_mobile = true;
    }
    else
    {
        $is_mobile = false;
    }

    return $is_mobile;
}

/**
 * 手机号格式检查
 * @param string $mobile
 * @return bool
 */
function check_mobile_number($mobile)
{
    if (!is_numeric($mobile))
    {
        return false;
    }
    $reg = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#';

    return preg_match($reg, $mobile) ? true : false;
}
/**
 * 生成随机字符
 * @param int $length
 * @return string
 */
function generate_password($length = 15)
{
    // 密码字符集，可任意添加你需要的字符
    $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = "";
    for ($i = 0; $i < $length; $i++)
    {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}
/**
 * 发送邮件
 * @param string $inboxmail,$emailsub,$emailbody
 * @return bool
 */
function sendemail($inboxmail, $emailsub, $emailbody)
{
    return true;
}
function send_to_msg($user_id, $send_state, $pid = 0)
{
    $msg     = Db::name('message')->where('send_state', $send_state)->select();
    $msgdata = [];
    foreach ($msg as $k => $v)
    {
        if ($v['send_to'] >= 4)
        {
            $str = '';
            replace_user_tpl($user_id, $v['content'], $str);
            replace_authentication_tpl($user_id, $str, $str);
            if ($pid != 0)
            {
                replace_project_tpl($pid, $str, $str);
                replace_project_plan_tpl($pid, $str, $str);
                replace_pauth_tpl($pid, $str, $str);
            }
            $arr['content']     = $str;
            $arr['user_id']     = $user_id;
            $arr['title']       = $v['title'];
            $arr['create_time'] = time();
            $msgdata[]          = $arr;
        }
        else
        {
            $arr['user_id']     = $user_id;
            $arr['create_time'] = time();
            $arr['msg_id']      = $mid;
            $msgdata[]          = $arr;
        }
    }
    if (!empty($msgdata))
    {
        $save = Db::name('user_msg')->insertAll($msgdata);
        if ($save)
        {
            return 1;
        }
        return 0;
    }
    // $str="";
    // replace_user_tpl($v['user_id'],$message['content'],$str);
}
function replace_user_tpl($user_id, $intpl, &$outtpl)
{
    $upaddress = Db::name('user')->find($user_id);
    $outtpl    = $intpl;
    if (preg_match_all("/\[\#(.*?)\#\]/", $outtpl, $matchs))
    {
        foreach ($matchs[1] as $matchval)
        {
            $key = strtolower($matchval);
            if (!isset($upaddress[$key]))
            {
                $outtpl = str_replace("[#$matchval#]", "", $outtpl);
                continue;
                // return 0;
            }
            $outtpl = str_replace("[#$matchval#]", $upaddress[$key], $outtpl);
        }
    }
    return 1;
}

function replace_project_tpl($p_id, $intpl, &$outtpl)
{
    $upaddress = Db::name('project')->find($p_id);
    $outtpl    = $intpl;
    if (preg_match_all("/\[\%(.*?)\%\]/", $outtpl, $matchs))
    {
        foreach ($matchs[1] as $matchval)
        {
            $key = strtolower($matchval);
            if (!isset($upaddress[$key]))
            {
                $outtpl = str_replace("[%$matchval%]", "", $outtpl);
                continue;
                // return 0;
            }
            $outtpl = str_replace("[%$matchval%]", $upaddress[$key], $outtpl);
        }
    }
    return 1;
}
function replace_project_plan_tpl($p_id, $intpl, &$outtpl)
{
    $upaddress = Db::name('project_plan')->where(['project_id' => $p_id])->find();
    $outtpl    = $intpl;
    if (preg_match_all("/\[\!(.*?)\!\]/", $outtpl, $matchs))
    {
        foreach ($matchs[1] as $matchval)
        {
            $key = strtolower($matchval);
            if (!isset($upaddress[$key]))
            {
                $outtpl = str_replace("[!$matchval!]", "", $outtpl);
                continue;
                // return 0;
            }
            $outtpl = str_replace("[!$matchval!]", $upaddress[$key], $outtpl);
        }
    }
    return 1;
}
function replace_authentication_tpl($user_id, $intpl, &$outtpl)
{
    $upaddress = Db::name('authentication')->where(['user_id' => $user_id])->find();
    $outtpl    = $intpl;
    if (preg_match_all("/\[\*(.*?)\*\]/", $outtpl, $matchs))
    {
        foreach ($matchs[1] as $matchval)
        {
            $key = strtolower($matchval);
            if (!isset($upaddress[$key]))
            {
                $outtpl = str_replace("[*$matchval*]", "", $outtpl);
                continue;
                // return 0;
            }
            $outtpl = str_replace("[*$matchval*]", $upaddress[$key], $outtpl);
        }
    }
    return 1;
}
function replace_pauth_tpl($p_id, $intpl, &$outtpl)
{
    $upaddress = Db::name('project_audit')->where(['project_id' => $p_id])->find();
    $outtpl    = $intpl;
    if (preg_match_all("/\[\@(.*?)\@\]/", $outtpl, $matchs))
    {
        foreach ($matchs[1] as $matchval)
        {
            $key = strtolower($matchval);
            if (!isset($upaddress[$key]))
            {
                $outtpl = str_replace("[@$matchval@]", "", $outtpl);
                continue;
                // return 0;
            }
            $outtpl = str_replace("[@$matchval@]", $upaddress[$key], $outtpl);
        }
    }
    return 1;
}

/**
 * 上次浏览的地址
 */
function backurl()
{
    return $_SERVER['HTTP_REFERER'];
}
