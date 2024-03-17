<?php

namespace app\web\controller;

use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;

class Login
{
    private static $sitecode = '';
    private static $site_id = 1;

    public function __construct()
    {
        $sitecode = Request::header('x-site');
        if (empty($sitecode)) {
            $sitecode = input('sitecode', '', 'trim');
        }
        if ($sitecode) {
            $site_id = Db::name('site')
                ->where('sitecode', $sitecode)
                ->value('id');
        } else {
            $site_id = 1;
            $actionName = Request::action();
            if (in_array($actionName, ['login', 'h5', 'check'])) {
                // 登录方法才用到sitecode
                $sitecode = Db::name('site')
                    ->where('id', 1)
                    ->value('sitecode');
            }
        }
        self::$sitecode = $sitecode;
        self::$site_id = $site_id;
    }

    public function check()
    {
        $code = input('code', '', 'trim');

        $loginInfo = Db::name('pclogin')
            ->where([
                ['site_id', '=', self::$site_id],
                ['code', '=', $code]
            ])
            ->order('id desc')
            ->find();
        if (!$loginInfo || empty($loginInfo['user_id'])) {
            return successJson([
                'login' => 0
            ]);
        }

        // 用一次就过期
        Db::name('pclogin')
            ->where('id', $loginInfo['id'])
            ->delete();

        $user = Db::name('user')
            ->where([
                ['site_id', '=', self::$site_id],
                ['id', '=', $loginInfo['user_id']]
            ])
            ->find();
        if (!$user) {
            return errorJson('登录失败，请重新扫码');
        }

        // 存入session
        $token = uniqid() . $user['id'];
        session_id($token);
        session_start();
        $_SESSION['user'] = json_encode($user);
        $_SESSION['sitecode'] = self::$sitecode;

        return successJson([
            'login' => 1,
            'token' => $token
        ], '登录成功');
    }

    /**
     * 微信小程序登录
     */
    public function getQrcode()
    {
        try {
            $type = input('type', 'login', 'trim');
            if (!in_array($type, ['login', 'bind'])) {
                return errorJson('参数错误');
            }
            $wxmpSetting = getSystemSetting(self::$site_id, 'wxmp');
            if (!isset($wxmpSetting['appid'])) {
                return errorJson('请先配置公众号参数');
            }
            $config = [
                'app_id' => $wxmpSetting['appid'] ?? '',
                'secret' => $wxmpSetting['appsecret'] ?? '',
                'token' => $wxmpSetting['token'] ?? '',
                'aes_key' => $wxmpSetting['aes_key'] ?? '',
                'response_type' => 'array'
            ];

            $code = $type . '_' . getNonceStr(4) . '' . uniqid() . getNonceStr(4);

            $app = \EasyWeChat\Factory::officialAccount($config);
            $result = $app->qrcode->temporary($code, 600);
            if (isset($result['errcode']) && $result['errcode']) {
                return errorJson($result['errmsg']);
            }
            $qrcode = $app->qrcode->url($result['ticket']);

            /*$qrcode = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQEG8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyY20tTzRvczZjTDAxVW81OHhBY2YAAgTAgshkAwRYAgAA';*/

            return successJson([
                'qrcode' => $qrcode,
                'code' => $code
            ]);
        } catch (\Exception $e) {
            return errorJson($e->getMessage());
        }

    }

    /**
     * 图形验证码
     */
    public function getCaptcha()
    {
        try {
            $captcha = \think\captcha\facade\Captcha::create(null, true);
            $image = "data:image/png;base64," . base64_encode($captcha->getData());
            $key = session('captcha.key');
            session_start();
            $token = session_id();
            Cache::set('catpcha_' . $token, $key, 300);
            return successJson([
                'image' => $image,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return errorJson($e->getMessage());
        }
    }

    /**
     * 发送短信验证码
     */
    public function sendSms()
    {
        $now = time();
        $type = input('type', '', 'trim');
        $phone = input('phone', '', 'trim');

        // 验证图片验证码
        $verify_code = input('code', '', 'trim');
        $token = input('token', '', 'trim');
        $key = Cache::get('catpcha_' . $token);
        if (!password_verify(mb_strtolower($verify_code, 'UTF-8'), $key)) {
            return errorJson('验证码输入有误');
        }

        $isExist = Db::name('user')
            ->where([
                ['site_id', '=', self::$site_id],
                ['phone', '=', $phone]
            ])
            ->find();
        if ($type == 'reg' && $isExist) {
            return errorJson('手机号已存在，可直接登录');
        } elseif ($type == 'reset' && !$isExist) {
            return errorJson('手机号不存在，请检查');
        }

        try {
            // 发送验证码
            $code = rand(100000, 999999);
            $result = sendSms(self::$site_id, $type, $phone, [
                'code' => $code
            ]);
        } catch (\Exception $e) {
            return errorJson($e->getMessage());
        }
        if (is_array($result) && $result['errno']) {
            return errorJson($result['message']);
        }
        Db::name('smscode')
            ->insert([
                'site_id' => self::$site_id,
                'type' => $type,
                'phone' => $phone,
                'code' => $code,
                'expire_time' => $now + 300,
                'create_time' => $now
            ]);
        return successJson('', '发送成功');
    }

    /**
     * 账号密码登录
     */
    public function login()
    {
        $phone = input('phone', '', 'trim');
        $password = input('password', '', 'trim');
        if (empty($password)) {
            return errorJson('请输入密码');
        }
        $user = Db::name('user')
            ->where([
                ['site_id', '=', self::$site_id],
                ['phone', '=', $phone],
                ['password', '=', $password]
            ])
            ->find();
        if (!$user) {
            return errorJson('账号或密码错误');
        }

        if ($user['is_delete']) {
            return errorJson('账号已被停用');
        }

        if ($user['is_freeze']) {
            return errorJson('账号已被冻结，请联系客服');
        }

        // 存入session
        $token = uniqid() . $user['id'];
        session_id($token);
        session_start();
        $_SESSION['user'] = json_encode($user);
        $_SESSION['sitecode'] = self::$sitecode;

        return successJson([
            'token' => $token
        ], '登录成功');
    }

    /**
     * 账号注册
     */
    public function reg()
    {
        $now = time();
        $phone = input('phone', '', 'trim');
        $code = input('code', '', 'trim');
        $password = input('password', '', 'trim');
        $tuid = input('tuid', 0, 'intval');
        if (empty($password)) {
            return errorJson('请输入密码');
        }
        if (!verifySmsCode(self::$site_id, 'reg', $phone, $code)) {
            return errorJson('短信验证码错误');
        }
        $isExist = Db::name('user')
            ->where([
                ['site_id', '=', self::$site_id],
                ['phone', '=', $phone]
            ])
            ->find();
        if ($isExist) {
            return errorJson('手机号已存在，可直接登录');
        }

        Db::startTrans();
        try {
            $user_id = Db::name('user')
                ->insertGetId([
                    'site_id' => self::$site_id,
                    'tuid' => $tuid,
                    'phone' => $phone,
                    'password' => $password,
                    'update_time' => $now,
                    'create_time' => $now
                ]);
            // 送免费条数
            giveFreeNum(self::$site_id, $user_id);
            // 送邀请人次数
            if ($tuid) {
                $today = strtotime(date('Y-m-d'));
                $count = Db::name('user')
                    ->where([
                        ['tuid', '=', $tuid],
                        ['create_time', '>', $today]
                    ])
                    ->count();
                $setting = getSystemSetting(self::$site_id, 'reward_invite');
                if (!empty($setting['is_open']) && !empty($setting['max']) && $count < intval($setting['max']) && !empty($setting['num'])) {
                    $reward_num = intval($setting['num']);
                    changeUserBalance($tuid, $reward_num, '邀请朋友奖励');
                }
            }
            Db::commit();
            return successJson('', '注册成功');
        } catch (\Exception $e) {
            Db::rollBack();
            return errorJson('注册失败：' . $e->getMessage());
        }
    }

    /**
     * 找回密码
     */
    public function reset()
    {
        $now = time();
        $phone = input('phone', '', 'trim');
        $code = input('code', '', 'trim');
        $password = input('password', '', 'trim');
        if (empty($password)) {
            return errorJson('请输入密码');
        }
        if (!verifySmsCode(self::$site_id, 'reset', $phone, $code)) {
            return errorJson('短信验证码错误');
        }
        $isExist = Db::name('user')
            ->where([
                ['site_id', '=', self::$site_id],
                ['phone', '=', $phone]
            ])
            ->find();
        if (!$isExist) {
            return errorJson('手机号不存在，请检查');
        }

        Db::startTrans();
        try {
            Db::name('user')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['phone', '=', $phone]
                ])
                ->update([
                    'password' => $password,
                    'update_time' => $now
                ]);
            Db::commit();
            return successJson('', '密码修改成功');
        } catch (\Exception $e) {
            Db::rollBack();
            return errorJson('修改失败：' . $e->getMessage());
        }
    }

    public function system()
    {
        $webSetting = getSystemSetting(self::$site_id, 'web');
        if (empty($webSetting['is_open'])) {
            echo json_encode([
                'errno' => 403,
                'message' => '已暂停服务'
            ]);
            exit;
        }
        $systemSetting = getSystemSetting(0, 'system');
        $loginSetting = getSystemSetting(self::$site_id, 'login');
        return successJson([
            'logo' => $webSetting['logo'] ?? '',
            'logo_mini' => $webSetting['logo_mini'] ?? '',
            'page_title' => $webSetting['page_title'] ?? '',
            'copyright' => $webSetting['copyright'] ?? '',
            'copyright_link' => $webSetting['copyright_link'] ?? '',
            'icp' => $systemSetting['system_icp'] ?? '',
            'gongan' => $systemSetting['system_gongan'] ?? '',
            'login_wechat' => isset($loginSetting['login_wechat']) ? $loginSetting['login_wechat'] : 1,
            'login_phone' => isset($loginSetting['login_phone']) ? $loginSetting['login_phone'] : 0
        ]);
    }

    /**
     * H5公众号登录
     */
    public function h5()
    {
        $fromUrl = input('from', '', 'trim');
        $code = input('code', '', 'trim');
        $tuid = input('tuid', 0, 'intval');
        $wxmpConfig = getSystemSetting(self::$site_id, 'wxmp');
        $config = [
            'app_id' => $wxmpConfig['appid'] ?? '',
            'secret' => $wxmpConfig['appsecret'] ?? '',
            'response_type' => 'array',
            'debug' => false
        ];
        $app = \EasyWeChat\Factory::officialAccount($config);
        $oauth = $app->oauth;
        if (!$code) {

            $webConfig = getSystemSetting(self::$site_id, 'web');
            if (empty($webConfig['is_open'])) {
                return $this->error('此站点已停止服务');
            }
            if (!isset($wxmpConfig['appid'])) {
                return $this->error('请先配置公众号参数');
            }

            $callback = Request::url(true);
            $response = $oauth->scopes(['snsapi_userinfo'])
                ->redirect($callback);
            $response->send();
            exit;
        } else {
            try {
                $user = $oauth->user()->toArray();
                $openid = $user['id'];
                $avatar = $user['avatar'];
                $nickname = $user['nickname'];

                // 登录成功
                $user_id = Db::name('user')
                    ->where([
                        ['site_id', '=', self::$site_id],
                        ['openid_mp', '=', $openid]
                    ])
                    ->value('id');
                if (!$user_id) {

                    $user_id = Db::name('user')
                        ->insertGetId([
                            'site_id' => self::$site_id,
                            'openid_mp' => $openid,
                            'avatar' => $avatar,
                            'nickname' => $nickname,
                            'tuid' => $tuid,
                            'create_time' => time()
                        ]);
                    // 送免费条数
                    giveFreeNum(self::$site_id, $user_id);
                    // 送邀请人次数
                    if ($tuid) {
                        $today = strtotime(date('Y-m-d'));
                        $count = Db::name('user')
                            ->where([
                                ['tuid', '=', $tuid],
                                ['create_time', '>', $today]
                            ])
                            ->count();
                        $setting = getSystemSetting(self::$site_id, 'reward_invite');
                        if (!empty($setting['is_open']) && !empty($setting['max']) && $count < intval($setting['max']) && !empty($setting['num'])) {
                            $reward_num = intval($setting['num']);
                            changeUserBalance($tuid, $reward_num, '邀请朋友奖励');
                        }
                    }
                }
                $user = Db::name('user')
                    ->where([
                        ['site_id', '=', self::$site_id],
                        ['id', '=', $user_id]
                    ])
                    ->find();
                session_start();
                $_SESSION['user'] = json_encode($user);
                $_SESSION['sitecode'] = self::$sitecode;

                header('location:/h5/#/' . $fromUrl);
                exit;
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * @param $msg
     * 在页面上输出错误信息
     */
    private function error($message)
    {
        return view('pay/error', ['message' => $message]);
    }
}
