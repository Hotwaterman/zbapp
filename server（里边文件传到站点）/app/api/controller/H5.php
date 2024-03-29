<?php

namespace app\api\controller;

use think\facade\Db;
use Jssdk\jssdk;
use Wxpay\v2\JsApiPay;
use Wxpay\v2\WxPayConfig;

class H5 extends Base
{
    public function checkLogin()
    {
        return successJson();
    }


    /**
     * 微信小程序登录
     */
    public function login()
    {
        $site_id = input('site_id', 1, 'intval');
        $share_id = input('sid', 0, 'intval');
        $code = input('code', '', 'trim');

        $setting = getSystemSetting($site_id, 'wxapp');
        $Wxapp = new \Weixin\Wxapp($setting['appid'], $setting['appsecret']);
        $oauthInfo = $Wxapp->getOauthInfo($code);
        if (empty($oauthInfo['openid'])) {
            $message = '登录失败';
            if (isset($oauthInfo['errmsg'])) {
                $message .= '（' . $oauthInfo['errmsg'] . '）';
            }
            return errorJson($message);
        }

        // 分享点击数+1
        $shareInfo = Db::name('reward_share')
            ->where('id', $share_id)
            ->find();
        if ($shareInfo) {
            Db::name('reward_share')
                ->where('id', $share_id)
                ->inc('views', 1)
                ->update();
        }

        // 存入user表
        $openid = $oauthInfo['openid'];
        $unionid = isset($oauthInfo['unionid']) ? $oauthInfo['unionid'] : '';
        $user = Db::name('user')
            ->where([
                ['site_id', '=', $site_id],
                ['openid', '=', $openid]
            ])
            ->find();

        if (!$user) {
            $user_id = Db::name('user')
                ->insertGetId([
                    'site_id' => $site_id,
                    'openid' => $openid,
                    'unionid' => $unionid,
                    'tuid' => $shareInfo ? $shareInfo['user_id'] : 0,
                    'create_time' => time()
                ]);
            // 送免费条数
            giveFreeNum($site_id, $user_id);
            // 送邀请人次数
            if ($shareInfo) {
                $today = strtotime(date('Y-m-d'));
                $count = Db::name('reward_invite')
                    ->where([
                        ['user_id', '=', $shareInfo['user_id']],
                        ['create_time', '>', $today]
                    ])
                    ->count();
                $setting = getSystemSetting($site_id, 'reward_invite');
                if (!empty($setting['is_open']) && !empty($setting['max']) && $count < intval($setting['max']) && !empty($setting['num'])) {
                    $reward_num = intval($setting['num']);
                    changeUserBalance($shareInfo['user_id'], $reward_num, '邀请朋友奖励');
                } else {
                    $reward_num = 0;
                }
                Db::name('reward_invite')
                    ->insert([
                        'site_id' => $site_id,
                        'user_id' => $shareInfo['user_id'],
                        'share_id' => $shareInfo['id'],
                        'way' => $shareInfo['way'],
                        'newuser_id' => $user_id,
                        'reward_num' => $reward_num,
                        'create_time' => time()
                    ]);
                Db::name('reward_share')
                    ->where('id', $share_id)
                    ->inc('invite_num', 1)
                    ->update();
            }


            $user = Db::name('user')
                ->where('id', $user_id)
                ->find();
        }

        // 存入session
        $token = uniqid() . $user['id'];
        session_id($token);
        session_start();
        $_SESSION['user'] = json_encode($user);
        if (!empty($oauthInfo['session_key'])) {
            $_SESSION['session_key'] = $oauthInfo['session_key'];
        }
        Db::name('user')
            ->where('id', $user['id'])
            ->update([
                'token' => $token
            ]);

        return successJson(['token' => $token]);
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
            'vip_expire_time' => $vip_expire_time
        ]);
    }

    /**
     * 获取小程序分享参数
     */
    public function getWxappInfo()
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
        $ad = getSystemSetting(self::$site_id, 'ad');
        return successJson($ad);
    }

    public function createOrder()
    {
        $goods_id = input('goods_id', 0, 'intval');
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

    public function pcLogin()
    {
        $code = input('code', '', 'trim');
        $info = Db::name('pclogin')
            ->where('code', $code)
            ->find();
        if ($info) {
            Db::name('pclogin')
                ->where('code', $code)
                ->update([
                    'site_id' => self::$site_id,
                    'user_id' => self::$user['id'],
                    'openid' => self::$user['openid'],
                    'create_time' => time()
                ]);
        } else {
            Db::name('pclogin')
                ->insert([
                    'site_id' => self::$site_id,
                    'user_id' => self::$user['id'],
                    'openid' => self::$user['openid'],
                    'code' => $code,
                    'create_time' => time()
                ]);
        }
        return successJson('', '登录成功');
    }

}
