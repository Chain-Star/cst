<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

class Synct extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->url = "";
        $this->api="";
        // $this->api    = "http://api-ropsten.etherscan.io/api";
        $this->apikey = "";
    }
    public function re_arr($arr, $from, $num)
    {
        foreach ($arr as $k1 => $v1)
        {
            $form1 = strtolower($v1['form']);
            $num1  = $v1['num'];
            if (strcmp($from, $form1) == 0 && $num > 0.0005)
            {
                return array('uid' => $v1['user_id'], 'pid' => $v1['project_id']);
            }
        }
        return false;
    }
    public function index()
    {
        $af       = gmp_pow('10', 18);
        $last_num = '0';
        if (file_exists("sync_number.txt"))
        {
            $last_num = file_get_contents("sync_number.txt");
        }
        $num_hex      = $this->api_blockNumber();
        $statr_num    = $last_num + 1;
        $num_hex      = hexdec($num_hex) + 0;
        $trlist       = Db::name('transaction_log')->field('start_path,end_path,amount,id,user_id,project_id')->where(['state' => 2])->select();
        $project_list = Db::name('project_audit')->select();
        $endpath      = array();
        foreach ($trlist as $k => $v)
        {
            $a             = $v['end_path'];
            $endpath[$a][] = ['form' => $v['start_path'], 'num' => $v['amount'], 'id' => $v['id'], 'user_id' => $v['user_id'], 'project_id' => $v['project_id']];
        }
        foreach ($project_list as $key => $value)
        {
            $a = $value['purse_address'];
            if (!array_key_exists($a, $endpath))
            {
                $endpath[$a] = array();
            }

        }
        foreach ($endpath as $key => $value)
        {
            $address = strtolower($key);
            $result  = $this->api_txlist($address, $statr_num, $num_hex);
            if (!is_array($result))
            {
                $this->save_log("the $address fail ,result::$result");
                print_r($result);
                continue;
            }
            foreach ($result as $k => $v)
            {
                // var_dump($v['to']==$address);
                if ($v['to'] == $address)
                {
                    $arr                 = [];
                    $arr['from']         = $v['from'];
                    $arr['block_number'] = $v['blockNumber'];
                    $arr['tx_hash']      = $v['hash'];
                    $arr['to']           = $v['to'];
                    $arr['date']         = $v['timeStamp'];
                    $arr['value']        = bcdiv($v['value'], $af, 10);
                    if (Db::name('transaction_log')->where('order', $arr['tx_hash'])->count())
                    {
                        $this->save_log("the $address have tx_hash with " . $arr['tx_hash']);
                        continue;
                    }
                    if ($uid = $this->re_arr($value, $arr['from'], $arr['value']))
                    {
                        $vid = Db::name('transaction_log')->field('id')->where(['user_id' => $uid['uid'], 'state' => 2])->find();
                        // print_r($vid);
                        if (empty($vid))
                        {
                            Db::name('transaction_log')->insert(["amount" => $arr['value'],
                                'order'                                       => $arr['tx_hash'], 'state' => 1, 'log_time' => $arr['date'], 'start_path' => $arr['from'],
                                "end_path"                                    => $address, 'currency'     => 2, 'user_id'  => $uid['uid'], 'project_id'  => $uid['pid']]);
                            $this->change_raise($uid['pid'], $arr['value']);
                            $this->save_log("the $address insert tx_hash with " . $arr['tx_hash'] . " the user_id is " . $uid['uid'] . " pid is " . $uid['pid'], $arr);
                            continue;
                        }
                        // print_r($value);
                        Db::name('transaction_log')->update(["amount" => $arr['value'],
                            'order'                                       => $arr['tx_hash'], 'state' => 1, 'id' => $vid['id']]);
                        $this->change_raise($uid['pid'], $arr['value']);
                        $this->save_log("the $address update tx_hash with " . $arr['tx_hash'] . " the id is " . $vid['id'], $arr);
                        continue;
                    }
                    else
                    {
			$pid=Db::name('project_audit')->field('project_id')->where('purse_address',$address)->find();
                        if ($oe = Db::name('transaction_log')->where("start_path", $arr['from'])->find())
                        {
                        	if ($arr['value']>0.0005) 
                        	{
                        		 Db::name('transaction_log')->insert([
                                "amount"   => $arr['value'],
                                'order'    => $arr['tx_hash'], 'state'     => 1,
                                'log_time' => $arr['date'], 'start_path'   => $arr['from'],
                                "end_path" => $address, 'currency'         => 2,
                                'user_id'  => $oe['user_id'], 'project_id' => $pid['project_id']]);
                        		$this->change_raise($pid['project_id'], $arr['value']);
                        		$this->save_log("the from is in transaction_log start_path:" . $arr['from'] . " pid is " . $pid['project_id'], $arr);
                            	continue;
                        	}
                        }
                        $count = Db::name('unknown_tx')->where(['tx_hash' => $arr['tx_hash']])->count();
                        if ($count)
                        {
                            $this->save_log("unknown_tx have the tx_hash " . $arr['tx_hash']);
                            continue;
                        }
                        Db::name('unknown_tx')->insert($arr);
                        $this->save_log("the tx_hash " . $arr['tx_hash'] . " is insert unknown_tx", $arr);
                    }
                    // print_r($arr);
                }
            }
            sleep(2);

        }
        $this->save_log("the sync_number is " . $num_hex . "\r\n");
        file_put_contents("sync_number.txt", $num_hex);
        die;
    }
    public function save_log($str, $arr = [])
    {
        $logpath = APP_PATH . "../log";
        if (!file_exists($logpath))
        {
            mkdir($logpath);
        }
        $chpath = $logpath . "/" . "tx_log";
        if (!file_exists($chpath))
        {
            mkdir($chpath);
        }
        if (!empty($arr))
        {
            $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
            file_put_contents($chpath . "/" . date("Y-m-d") . ".log", "[" . date("H:i:s") . "]::" . $str . "\t" . $arr . "\r\n", FILE_APPEND);
        }
        else
        {
            file_put_contents($chpath . "/" . date("Y-m-d") . ".log", "[" . date("H:i:s") . "]::" . $str . "\r\n", FILE_APPEND);
        }
    }
    public function change_raise($pid, $value)
    {
        // if ($one = Db::name('project_raise')->where('project_id', $pid)->find())
        if ($one = Db::name('project_raise')->where('project_id', $pid)->find())
        {
            // $value = bcadd($one, $value, 10);
            // Db::name('project_raise')->update(['raise' => $value, 'id' => $one['id']]);
            Db::name('project_raise')->where('project_id', $pid)->setInc('raise', $value);
        }
        else
        {
            Db::name('project_raise')->insert(['project_id' => $pid, 'raise' => $value]);
        }
    }
    public function api_blockNumber()
    {
        $data = ['module' => 'proxy', 'action' => 'eth_blockNumber', 'apikey' => $this->apikey];
        return $this->get_api($this->api, $data);
    }
    public function api_txlist($address, $startblock, $endblock)
    {
        $data = ['module' => 'account', 'action' => 'txlist', "address" => $address, "startblock" => $startblock, "endblock" => $endblock, 'apikey' => $this->apikey];
        return $this->get_api($this->api, $data);
    }

    public function get_blockNumber($url)
    {
        $data = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'eth_blockNumber', 'params' => []];
        return $this->get_json($url, $data);
    }
    public function get_logs($url, $params)
    {
        $data           = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'eth_getLogs', 'params' => []];
        $data['params'] = $params;
        return $this->get_json($url, $data);
    }
    public function get_blockByNumber($url, $params)
    {
        $data           = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'eth_getBlockByNumber', 'params' => []];
        $data['params'] = $params;
        return $this->get_json($url, $data);
    }
    public function get_transactionByHash($url, $params)
    {
        $data           = ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'eth_getTransactionByHash', 'params' => []];
        $data['params'] = $params;
        return $this->get_json($url, $data);
    }
    public function get_api($url, $data)
    {
        $arr = json_decode($this->post_api($url, $data), true);
        if (empty($arr))
        {
            return false;
        }
        if (isset($arr['message']) && $arr['message'] == "NOTOK")
        {
            return false;
        }
        if ($arr['result'] == 'Error!')
        {
            return false;
        }
        return $arr['result'];
    }
    public function get_json($url, $data)
    {
        $data = json_encode($data);
        $arr  = json_decode($this->jsonrpc($url, $data), true);
        if (empty($arr))
        {
            return false;
        }
        if (isset($arr['error']))
        {
            return false;
        }
        return $arr['result'];
    }
    public function post_api($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 60 * 1000);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        // $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $return_content;
    }
    public function jsonrpc($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=utf-8",
            "Content-Length: " . strlen($data))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        // $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $return_content;
    }
}
