<?php

namespace App\Library\Pay\PayjsWX;
require_once __DIR__ . '/vendor/autoload.php';

use App\Library\Pay\ApiInterface;
use Xhat\Payjs\Payjs;

class Api implements ApiInterface
{
    private $url_notify = '';
    private $url_return = '';

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
    }

    private function CreatePayjsObject($config){
        $payconfig = [
            'mchid' => $config['mchid'],
            'key' => $config['key']
        ];
        $Payjs = new Payjs($payconfig);
        return $Payjs;
    }

    /**
     * @param array $config 配置信息
     * @param string $out_trade_no 发卡系统订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 支付金额, 单位:分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $payway = strtolower($config['payway']);
        // PayJS 微信支付
        $payconfig = [
            'mchid' => $config['mchid'],
            'key' => $config['key']
        ];
        $Payjs = new Payjs($payconfig);
        $data = [
            'body' => $body,
            'total_fee' => $amount_cent,
            'out_trade_no' => $out_trade_no,
            'notify_url' => $this->url_notify,
        ];
        $url_pay = $Payjs->native($data);
 
        // 跳转支付页面
        header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($url_pay['code_url']));
        exit;
    }

    /**
     * @param $config
     * @param callable $successCallback
     * @return bool|string
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $_REQUEST = $_POST;
        if ($isNotify) {
            if ($_REQUEST['return_code'] != 1) { // 一些校验, 如签名校验等
                \Log::error('支付失败！');
                echo 'error';
                return false;
            } else {
                echo 'success';
            }

            $order_no = $_REQUEST['out_trade_no'];  // 发卡系统内交易单号
            $total_fee = $_REQUEST['total_fee']; // 实际支付金额, 单位, 分
            $pay_trade_no = $_REQUEST['payjs_order_id']; // 支付系统内订单号/流水号
            $successCallback($order_no, $total_fee, $pay_trade_no);
            return true;

        } else {
            $order_no = @$config['out_trade_no']; // 发卡系统内交易单号
            $_REQUEST = file_get_contents('php://input');
            $_REQUEST = json_decode($_REQUEST, true);
            if (strlen($order_no) < 5) {
                throw new \Exception('交易单号未传入');
            }

            if ($_REQUEST['out_trade_no'] === $order_no) { // 一些校验, 如签名校验等
                $order_no = $_REQUEST['out_trade_no'];  // 发卡系统内交易单号
                $total_fee = $_REQUEST['total_fee']; // 实际支付金额, 单位, 分
                $pay_trade_no = $_REQUEST['payjs_order_id']; // 支付系统内订单号/流水号
                $successCallback($order_no, $total_fee, $pay_trade_no);
                return true;
            } else {
                \Log::error('这里可以记录一些出错信息, 内容保存在 /storage/logs 内');
                return false;
            }
        }
    }
}