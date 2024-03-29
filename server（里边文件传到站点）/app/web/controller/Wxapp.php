<?php

namespace app\web\controller;

use think\facade\Db;
use think\facade\Request;
use Wxpay\v2\JsApiPay;
use Wxpay\v2\lib\WxPayApi;
use Wxpay\v2\lib\WxPayUnifiedOrder;
use Wxpay\v2\WxPayConfig;

class Wxapp
{
    /**
     * 微信小程序登录
     */
    public function login()
    {
        $token = Request::header('x-token');
        $token = empty($token) ? '' : $token;

        $code = input('code', '', 'trim');
        $site_id = input('site_id', 1, 'intval');
        $sitecode = Db::name('site')
            ->where('id', $site_id)
            ->value('sitecode');
        $setting = getSystemSetting($site_id, 'wxapp');
        $Wxapp = new \Weixin\Wxapp($setting['appid'], $setting['appsecret']);
        $oauth = $Wxapp->getOauthInfo($code);
        if (empty($oauth['openid'])) {
            $message = '登录失败';
            if (isset($oauth['errmsg'])) {
                $message .= '（' . $oauth['errmsg'] . '）';
            }
            return errorJson($message);
        }

        $user_id = 0;
        $openid = $oauth['openid'];
        // $unionid = isset($oauth['unionid']) ? $oauth['unionid'] : '';
        $user = Db::name('user')
            ->where([
                ['site_id', '=', $site_id],
                ['openid', '=', $openid]
            ])
            ->find();

        if ($user) {
            $user_id = $user['id'];
            // 存入session，自动登录
            if (empty($token)) {
                $token = uniqid() . $user_id;
            }
            session_id($token);
            session_start();
            $_SESSION['user'] = json_encode($user);
            $_SESSION['sitecode'] = $sitecode;
        } else {
            // todo 判断要不要绑定账号

        }

        return successJson([
            'token' => $token,
            'sitecode' => $sitecode,
            'openid' => $openid,
            'user_id' => $user_id
        ]);
    }

    /**
     * 获取小程序配置参数
     */
    public function getSetting()
    {
        $site_id = input('site_id', 1, 'intval');
        $wxapp = getSystemSetting($site_id, 'wxapp');
        $page_title = $wxapp['page_title'] ?? 'AI创作助手';
        $welcome = $wxapp['welcome'] ?? '你好，我是AI创作助手！你现在可以向我提问了！';
        $share_title = $wxapp['share_title'] ?? '';
        $share_image = $wxapp['share_image'] ?? '';
        $is_check = empty($wxapp['is_check']) ? 0 : 1;
        $is_ios_pay = empty($wxapp['is_ios_pay']) ? 0 : 1;
        $apiSetting = getSystemSetting(0, 'api');
        $outtype = $apiSetting['outtype'] ?? 'nostream';
        $wxappIndex = getSystemSetting($site_id, 'wxapp_index');
        $indexType = $wxappIndex['type'] ?? 'chat';

        return successJson([
            'page_title' => $page_title,
            'welcome' => $welcome,
            'share_title' => $share_title,
            'share_image' => $share_image,
            'is_check' => $is_check,
            'is_ios_pay' => $is_ios_pay,
            'outtype' => $outtype,
            'index_type' => $indexType,
            'content' => $indexType == 'diy' ? $wxappIndex['content'] : ''
        ]);
    }

    /**
     * 获取账户余额
     */
    public function getBalance()
    {
        $user = Db::name('user')
            ->where('id', self::$user['id'])
            ->find();
        $now = time();
        if ($user['vip_expire_time'] > $now) {
            $vip_expire_time = date('Y-m-d', $user['vip_expire_time']);
        } else {
            $vip_expire_time = '';
        }
        return successJson([
            'balance' => $user['balance'] ?? 0,
            'balance_draw' => $user['balance_draw'] ?? 0,
            'balance_model4' => floor($user['balance_gpt4'] / 100) / 100,
            'vip_expire_time' => $vip_expire_time
        ]);
    }

    /**
     * 获取小程序分享参数
     */
    public function getWxappDiyIndex()
    {
        $site_id = input('site_id', 1, 'intval');
        $wxappIndex = getSystemSetting($site_id, 'wxapp_index');

        return successJson([
            'type' => $wxappIndex['type'] ?? 'chat',
            'content' => $wxappIndex['content']
        ]);
    }

    /**
     * 获取小程序分享参数
     */
    public function getShareInfo()
    {
        $site_id = input('site_id', 1, 'intval');
        $wxapp = getSystemSetting($site_id, 'wxapp');
        $share_title = $wxapp['share_title'] ?? '';
        $share_image = $wxapp['share_image'] ?? '';
        return successJson([
            'share_title' => $share_title,
            'share_image' => $share_image
        ]);
    }

    /**
     * 获取充值套餐
     */
    public function getGoodsList()
    {
        $list = Db::name('goods')
            ->where([
                ['site_id', '=', self::$site_id],
                ['status', '=', 1]
            ])
            ->field('id,title,price,market_price,num,is_default')
            ->order('weight desc, id asc')
            ->select()->toArray();

        return successJson($list);
    }

    /**
     * 获取绘画套餐
     */
    public function getDrawList()
    {
        $list = Db::name('draw')
            ->where([
                ['site_id', '=', self::$site_id],
                ['status', '=', 1]
            ])
            ->field('id,title,price,market_price,num,is_default')
            ->order('weight desc, id asc')
            ->select()->toArray();

        return successJson($list);
    }

    /**
     * 获取GPT4套餐
     */
    public function getModel4List()
    {
        $list = Db::name('gpt4')
            ->where([
                ['site_id', '=', self::$site_id],
                ['status', '=', 1]
            ])
            ->field('id,title,price,market_price,num,is_default')
            ->order('weight desc, id asc')
            ->select()->toArray();

        return successJson($list);
    }

    /**
     * 获取包月套餐
     */
    public function getVipList()
    {
        $list = Db::name('vip')
            ->where([
                ['site_id', '=', self::$site_id],
                ['status', '=', 1]
            ])
            ->field('id,title,price,market_price,num,is_default')
            ->order('weight desc, id asc')
            ->select()->toArray();

        return successJson($list);
    }

    /**
     * 获取任务配置
     */
    public function getTasks()
    {
        $share = getSystemSetting(self::$site_id, 'reward_share');
        $invite = getSystemSetting(self::$site_id, 'reward_invite');
        $ad = getSystemSetting(self::$site_id, 'reward_ad');

        $tasks = [];
        $today = strtotime(date('Y-m-d'));
        if (!empty($share['is_open']) && !empty($share['max']) && !empty($share['num'])) {
            // 获取今日已分享次数
            $count = Db::name('reward_share')
                ->where([
                    ['user_id', '=', self::$user['id']],
                    ['create_time', '>', $today]
                ])
                ->count();
            $share['count'] = intval($count);
            $tasks['share'] = $share;
        }
        if (!empty($invite['is_open']) && !empty($invite['max']) && !empty($invite['num'])) {
            // 获取今日已邀请人数
            $count = Db::name('reward_invite')
                ->where([
                    ['user_id', '=', self::$user['id']],
                    ['create_time', '>', $today]
                ])
                ->count();
            $invite['count'] = intval($count);
            $tasks['invite'] = $invite;
        }
        if (!empty($ad['is_open']) && !empty($ad['max']) && !empty($ad['num']) && !empty($ad['ad_unit_id'])) {
            // 获取今日已观看广告次数
            $count = Db::name('reward_ad')
                ->where([
                    ['user_id', '=', self::$user['id']],
                    ['create_time', '>', $today]
                ])
                ->count();
            $ad['count'] = intval($count);
            $tasks['ad'] = $ad;
        }

        $tasks = count($tasks) > 0 ? $tasks : null;

        return successJson($tasks);
    }

    /**
     * 获取首页广告配置
     */
    public function getIndexAd()
    {
        $site_id = input('site_id', 0, 'intval');
        $ad = getSystemSetting($site_id, 'ad');
        return successJson($ad);
    }

    /**
     * 是否打开GPT4开关
     */
    public function hasModel4()
    {
        $gpt4Setting = getSystemSetting(self::$site_id, 'gpt4');
        if (!empty($gpt4Setting['is_open'])) {
            $hasGpt4 = 1;
        } else {
            $hasGpt4 = 0;
        }

        return successJson([
            'hasModel4' => $hasGpt4,
            'model4Name' => !empty($gpt4Setting['alias_wxapp']) ? $gpt4Setting['alias_wxapp'] : 'GPT-4'
        ]);
    }

    public function createOrder()
    {
        $goods_id = input('goods_id', 0, 'intval');
        $draw_id = input('draw_id', 0, 'intval');
        $gpt4_id = input('model4_id', 0, 'intval');
        $vip_id = input('vip_id', 0, 'intval');
        $payConfig = getSystemSetting(self::$site_id, 'pay');
        $wxapp = getSystemSetting(self::$site_id, 'wxapp');
        $out_trade_no = $this->createOrderNo();

        if ($goods_id) {
            $goods = Db::name('goods')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $goods_id]
                ])
                ->find();
            if ($goods['status'] != 1) {
                return errorJson('该套餐已下架，请刷新页面重新提交');
            }
            $total_fee = $goods['price'];
            $num = $goods['num'];
        } else if ($draw_id) {
            $draw = Db::name('draw')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $draw_id]
                ])
                ->find();
            if ($draw['status'] != 1) {
                return errorJson('该套餐已下架，请刷新页面重新提交');
            }
            $total_fee = $draw['price'];
            $num = $draw['num'];
        } else if ($gpt4_id) {
            $gpt4 = Db::name('gpt4')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $gpt4_id]
                ])
                ->find();
            if ($gpt4['status'] != 1) {
                return errorJson('该套餐已下架，请刷新页面重新提交');
            }
            $total_fee = $gpt4['price'];
            $num = $gpt4['num'];
        } else if ($vip_id) {
            $vip = Db::name('vip')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $vip_id]
                ])
                ->find();
            if ($vip['status'] != 1) {
                return errorJson('该套餐已下架，请刷新页面重新提交');
            }
            $total_fee = $vip['price'];
            $num = $vip['num'];
        } else {
            return errorJson('参数错误');
        }
        $openid = self::$user['openid'];

        // 推荐人信息
        $commission1 = 0;
        $commission1_fee = 0;
        $commission2 = 0;
        $commission2_fee = 0;
        $commissionSetting = getSystemSetting(self::$site_id, 'commission');
        if (!empty($commissionSetting['is_open'])) {
            $bili_1 = floatval($commissionSetting['bili_1']);
            $bili_2 = floatval($commissionSetting['bili_2']);

            $tuid = Db::name('user')
                ->where('id', self::$user['id'])
                ->value('tuid');
            if (!empty($tuid)) {
                $tuser = Db::name('user')
                    ->where('id', $tuid)
                    ->find();
                if ($tuser && $tuser['commission_level'] > 0) {
                    $commission1 = $tuid;
                    $commission1_fee = intval($total_fee * $bili_1 / 100);
                    if ($tuser['commission_pid']) {
                        $commission2 = $tuser['commission_pid'];
                        $commission2_fee = intval($total_fee * $bili_2 / 100);
                    }
                }
            }
        }

        Db::name('order')->insertGetId([
            'site_id' => self::$site_id,
            'goods_id' => $goods_id,
            'draw_id' => $draw_id,
            'gpt4_id' => $gpt4_id,
            'vip_id' => $vip_id,
            'user_id' => self::$user['id'],
            'openid' => $openid,
            'out_trade_no' => $out_trade_no,
            'transaction_id' => '',
            'total_fee' => $total_fee,
            'pay_type' => 'wxpay',
            'status' => 0, // 0-待付款；1-成功；2-失败
            'num' => $num,
            'commission1' => $commission1,
            'commission2' => $commission2,
            'commission1_fee' => $commission1_fee,
            'commission2_fee' => $commission2_fee,
            'create_time' => time()
        ]);

        try {
            $notifyUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/api.php/notify/wxpay';
            $input = new WxPayUnifiedOrder();
            $input->SetBody('订购商品');
            $input->SetOut_trade_no($out_trade_no);
            $input->SetTotal_fee($total_fee);
            $input->SetTime_start(date('YmdHis'));
            $input->SetTime_expire(date('YmdHis', time() + 600));
            $input->SetNotify_url($notifyUrl);
            $input->SetTrade_type('JSAPI');
            $input->SetMch_id($payConfig['mch_id']);
            $input->SetAppid($wxapp['appid']);
            $input->SetOpenid($openid);

            $WxPayApi = new WxPayApi();
            $config = new WxPayConfig();
            $config->SetAppId($wxapp['appid']);
            $config->SetMerchantId($payConfig['mch_id']);
            $config->SetKey($payConfig['key']);
            $config->SetSignType('MD5');
            $config->SetNotifyUrl($notifyUrl);

            $unifiedOrder = $WxPayApi->unifiedOrder($config, $input);
            if (isset($unifiedOrder['return_code']) && $unifiedOrder['return_code'] == 'FAIL') {
                return errorJson($unifiedOrder['return_msg']);
            } elseif (isset($unifiedOrder['err_code']) && !empty($unifiedOrder['err_code_des'])) {
                return errorJson($unifiedOrder['err_code_des']);
            }
        } catch (\Exception $e) {
            return errorJson('发起支付失败：' . $e->getMessage());
        }

        // 生成调起jsapi-pay的js参数
        $JsApiPay = new JsApiPay();
        if (isset($unifiedOrder['sub_appid'])) {
            $unifiedOrder['appid'] = $unifiedOrder['sub_appid'];
        }
        $jsApiParameters = $JsApiPay->GetJsApiParameters($config, $unifiedOrder);

        $jsApiParameters = json_decode($jsApiParameters, true);
        $jsApiParameters['out_trade_no'] = $out_trade_no;

        return successJson($jsApiParameters);
    }

    /**
     * 创建订单号
     */
    private function createOrderNo()
    {
        return 'Ch' . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 分享动作
     */
    public function doShare()
    {
        $way = input('way', 'wechat', 'trim');
        $today = strtotime(date('Y-m-d'));
        $count = Db::name('reward_share')
            ->where([
                ['user_id', '=', self::$user['id']],
                ['create_time', '>', $today]
            ])
            ->count();

        Db::startTrans();
        try {
            $setting = getSystemSetting(self::$site_id, 'reward_share');
            if (!empty($setting['is_open']) && !empty($setting['max']) && $count < intval($setting['max']) && !empty($setting['num'])) {
                $reward_num = intval($setting['num']);
                changeUserBalance(self::$user['id'], $reward_num, '分享奖励');
            } else {
                $reward_num = 0;
            }
            $share_id = Db::name('reward_share')
                ->insertGetId([
                    'site_id' => self::$site_id,
                    'user_id' => self::$user['id'],
                    'way' => $way,
                    'reward_num' => $reward_num,
                    'create_time' => time()
                ]);
            Db::commit();
            return successJson([
                'share_id' => $share_id
            ]);
        } catch (\Exception $e) {
            Db::rollback();
            return errorJson('获取分享参数失败：' . $e->getMessage());
        }
    }

    /**
     * 观看广告视频
     */
    public function doAd()
    {
        $ad_unit_id = input('ad_unit_id', '', 'trim');
        if (!$ad_unit_id) {
            return errorJson('参数错误');
        }
        $today = strtotime(date('Y-m-d'));
        $count = Db::name('reward_ad')
            ->where([
                ['user_id', '=', self::$user['id']],
                ['create_time', '>', $today]
            ])
            ->count();

        Db::startTrans();
        try {
            $setting = getSystemSetting(self::$site_id, 'reward_ad');
            if (!empty($setting['is_open']) && !empty($setting['max']) && $count < intval($setting['max']) && !empty($setting['num']) && !empty($setting['ad_unit_id'])) {
                if ($setting['ad_unit_id'] != $ad_unit_id) {
                    return errorJson('参数出错，请刷新页面重试！');
                }
                $reward_num = intval($setting['num']);
                changeUserBalance(self::$user['id'], $reward_num, '观看广告奖励');
            } else {
                $reward_num = 0;
            }
            Db::name('reward_ad')
                ->insert([
                    'site_id' => self::$site_id,
                    'user_id' => self::$user['id'],
                    'reward_num' => $reward_num,
                    'ad_unit_id' => $ad_unit_id,
                    'create_time' => time()
                ]);
            Db::commit();
            if ($reward_num > 0) {
                $msg = '完成任务，余额 +' . $reward_num;
            } else {
                $msg = '今日已达观看上限，无法获得奖励';
            }
            return successJson('', $msg);
        } catch (\Exception $e) {
            Db::rollback();
            return errorJson('任务同步失败：' . $e->getMessage());
        }
    }

}
