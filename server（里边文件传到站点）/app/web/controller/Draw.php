<?php

namespace app\web\controller;

use ChatGPT\replicate;
use think\facade\Db;
use think\facade\Log;

class Draw extends Base
{
    /**
     * 获取消息历史记录
     */
    public function getHistoryMsg()
    {
        $list = Db::name('msg_draw')
            ->where([
                ['site_id', '=', self::$site_id],
                ['user_id', '=', self::$user['id']],
                ['is_delete', '=', 0]
            ])
            ->field('id,channel,message,images,state,errmsg')
            ->order('id desc')
            ->limit(10)
            ->select()->toArray();
        $msgList = [];
        $list = array_reverse($list);
        foreach ($list as $v) {
            $msgList[] = [
                'draw_id' => $v['id'],
                'channel' => $v['channel'],
                'state' => $v['state'],
                'errmsg' => $v['errmsg'],
                'message' => $v['message'],
                'response' => explode('|', $v['images'])
            ];
        }

        return successJson($msgList);
    }

    /**
     * 提交绘画接口
     */
    public function draw()
    {
        $message = input('message', '', 'trim');
        $draw_id = input('draw_id', '0', 'intval');
        $options = input('options', '', 'trim');
        if (empty($message) && !$draw_id) {
            return errorJson('请输入画面描述');
        }

        $user = Db::name('user')
            ->where('id', self::$user['id'])
            ->find();
        if (!$user) {
            $_SESSION['user'] = null;
            return errorJson('请登录');
        }

        // 禁言用户
        if ($user['is_freeze']) {
            return errorJson('系统繁忙，请稍后再试');
        }

        if (intval($user['balance_draw']) <= 0) {
            return errorJson('绘图次数用完了，请充值！');
        }

        $now = time();
        $taskNum = Db::name('msg_draw')
            ->where([
                ['site_id', '=', self::$site_id],
                ['user_id', '=', self::$user['id']],
                ['state', '=', 0],
                ['is_delete', '=', 0],
                ['create_time', '>', $now - 180]
            ])
            ->count();
        if ($taskNum >= 5) {
            return errorJson('最多同时进行5个任务，请稍后再试');
        }

        // 插入一条绘画记录
        if ($draw_id) {
            $draw = Db::name('msg_draw')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $draw_id],
                    ['is_delete', '=', 0]
                ])
                ->field('id,message_input,options')
                ->find();
            if (!$draw) {
                return errorJson('没有找到此绘画记录');
            }
            Db::name('msg_draw')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['id', '=', $draw['id']]
                ])
                ->update([
                    'state' => 0,
                    'images' => '',
                    'errmsg' => '',
                    'finish_time' => 0,
                    'create_time' => time()
                ]);
            $message = $draw['message_input'];
            $options = $draw['options'];
        } else {
            if (empty($message)) {
                $this->setDrawFail($draw_id, '请输入画面描述');
            }
            $clearMessage = wordFilter($message);
            $draw_id = Db::name('msg_draw')
                ->insertGetId([
                    'site_id' => self::$site_id,
                    'user_id' => self::$user['id'],
                    'message' => $clearMessage,
                    'message_input' => $message,
                    'state' => 0, // 0生成中 1已生成 2生成失败
                    'options' => $options,
                    'create_time' => time()
                ]);
        }

        // 异步执行绘画任务
        $drawSetting = getSystemSetting(self::$site_id, 'draw');
        $platform = $drawSetting['platform'] ?? 'openai';
        $channel = $drawSetting['channel'] ?? 'openai';
        if (empty($options)) {
            $options = json_encode([
                'channel' => $drawSetting['channel']
            ]);
        }
        $drawTaskUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/web.php/draw/submitDrawTask';
        $this->httpRequest($drawTaskUrl, [
            'platform' => $platform,
            'channel' => $channel,
            'draw_id' => $draw_id,
            'message' => $message,
            'options' => $options
        ]);

        return successJson([
            'draw_id' => $draw_id
        ]);
    }

    /**
     * 异步提交绘画任务
     * 仅供系统内部调用
     * 前端请勿使用此接口
     */
    public function submitDrawTask($draw_id = 0)
    {
        ignore_user_abort();
        set_time_limit(300);

        $platform = input('platform', 'openai', 'trim');
        $channel = input('channel', 'openai', 'trim');
        $message = input('message', '', 'trim');
        $options = input('options', '');
        if (!$draw_id) {
            $draw_id = input('draw_id', '0', 'intval');
        }
        if (!$draw_id) {
            exit;
        }

        $user = Db::name('user')
            ->where('id', self::$user['id'])
            ->find();
        if (!$user) {
            $this->setDrawFail($draw_id, '请重新登录');
        }

        // 禁言用户
        if ($user['is_freeze']) {
            $this->setDrawFail($draw_id, '系统繁忙，请稍后再试');
        }

        $clearMessage = wordFilter($message);
        $options = $options ? json_decode($options, true) : [];
        $apikey = $this->getApiKey($channel);
        if (empty($apikey)) {
            $this->setDrawFail($draw_id, 'key已用尽，请等待平台处理');
        }

        if (in_array($channel, ['openai', 'api2d', 'replicate'])) {
            // 同步绘画方式
            $urls = [];
            if ($channel == 'openai' || $channel == 'api2d') {
                $ChatGPT = new \ChatGPT\openai($apikey);
                if ($channel == 'openai') {
                    $apiSetting = getSystemSetting(0, 'api');
                    if ($apiSetting['channel'] == 'agent' && $apiSetting['agent_host']) {
                        $ChatGPT->setApiHost(rtrim($apiSetting['agent_host'], '/'));
                    }
                } elseif ($channel == 'api2d') {
                    $ChatGPT->setApiHost('https://openai.api2d.net');
                }

                $result = $ChatGPT->draw([
                    'prompt' => $clearMessage,
                    'size' => $options['size'] ?? '512x512',
                    'n' => $options['num'] ?? 1,
                    'response_format' => 'b64_json'
                ]);
            } elseif ($channel == 'replicate') {
                $replicateSDK = new \ChatGPT\replicate($apikey);
                $result = $replicateSDK->draw([
                    'prompt' => $clearMessage
                ]);
            }
            if (empty($result)) {
                $this->setDrawFail($draw_id, '未知错误');
            }

            if ($result['errno']) {
                $errLevel = 'warning';
                $errMsg = $result['message'];
                if ($channel == 'openai') {
                    if (strpos($errMsg, 'Billing hard limit has been reached') !== false) {
                        $errLevel = 'error';
                        $errMsg = '接口余额不足';
                    }
                } elseif ($channel == 'api2d') {
                    if (strpos($errMsg, 'Not enough point') !== false) {
                        $errLevel = 'error';
                        $errMsg = '接口余额不足';
                    }
                }
                if ($errLevel == 'error') {
                    $this->setKeyStop($channel, $apikey, $errMsg);
                    $this->submitDrawTask($draw_id);
                } else {
                    $this->setDrawFail($draw_id, $errMsg);
                }
                exit;
            }

            foreach ($result['data'] as $img) {
                if ($channel == 'openai') {
                    $url = $this->saveToFile($img['b64_json']);
                } elseif ($channel == 'api2d') {
                    $url = $this->saveToFile($img['url']);
                } elseif ($channel == 'replicate') {
                    $url = $this->saveToFile($img);
                }
                if (!empty($url)) {
                    $urls[] = $url;
                }
            }
            if (empty($urls)) {
                $this->setDrawFail($draw_id, '生成图片失败');
                exit;
            }

            // 扣费
            changeUserDrawBalance(self::$user['id'], -1, '绘画消费');

            // 生成成功，更新状态
            Db::name('msg_draw')
                ->where('id', $draw_id)
                ->update([
                    'platform' => $platform,
                    'channel' => $channel,
                    'images' => implode('|', $urls),
                    'state' => 1,
                    'finish_time' => time()
                ]);

        } elseif (in_array($channel, ['lxai', 'yijian'])) {
            // 异步绘画方式
            if ($channel == 'lxai') {
                $notifyUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/api.php/notify/lxai';
                $lxaiSDK = new \ChatGPT\lxai($apikey);
                if ($platform == 'mj') {
                    $result = $lxaiSDK->drawMJ([
                        'prompt' => $clearMessage,
                        'imageurl' => $options['image'] ?? '',
                        'notifyHook' => $notifyUrl
                    ]);
                    if ($result['errno']) {
                        $this->setDrawFail($draw_id, $result['message']);
                    }

                    // 扣费
                    changeUserDrawBalance(self::$user['id'], -1, '绘画消费');

                    $task_id = $result['data'] ?? '';
                }
            } elseif ($channel == 'yijian') {
                $notifyUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/api.php/notify/yijian';

                $temp = explode('|', $apikey);
                $apikey = $temp[0];
                $apisecret = $temp[1] ?? '';
                $yijianSDK = new \ChatGPT\yijian($apikey, $apisecret);
                if (!empty($options['sub_style'])) {
                    $style = $options['sub_style'];
                } elseif (!empty($options['style'])) {
                    $style = $options['style'];
                } else {
                    $style = '';
                }
                $result = $yijianSDK->submitDrawTask([
                    'prompt' => $clearMessage,
                    'ratio' => $options['size'] ?? 0,
                    'style' => $style,
                    'guidence_scale' => 7.5,
                    'engine' => $options['engine'] ?? 'default_dreamer_diffusion',
                    'callback_url' => $notifyUrl,
                    'init_image' => $options['image'] ?? ''
                ]);
                if ($result['errno']) {
                    $this->setDrawFail($draw_id, $result['message']);
                }

                // 扣费
                changeUserDrawBalance(self::$user['id'], -1, '绘画消费');

                $task_id = $result['data']['Uuid'] ?? '';
            }

            Db::name('msg_draw')
                ->where('id', $draw_id)
                ->update([
                    'platform' => $platform,
                    'channel' => $channel,
                    'task_id' => $task_id
                ]);
        }
    }

    public function mjCtrl()
    {
        $draw_id = input('draw_id', 0, 'intval');
        $type = input('type', 'upscale', 'trim');
        $index = input('index', 1, 'intval');
        $draw = Db::name('msg_draw')
            ->where([
                ['site_id', '=', self::$site_id],
                ['id', '=', $draw_id]
            ])
            ->find();
        if (!$draw) {
            return errorJson('参数错误');
        }
        if ($draw['state'] != 1) {
            return errorJson('请在绘图成功操作');
        }
        if ($draw['platform'] != 'mj' || $draw['channel'] != 'lxai') {
            return errorJson('不支持的操作');
        }

        $draw_id = Db::name('msg_draw')
            ->insertGetId([
                'site_id' => self::$site_id,
                'user_id' => self::$user['id'],
                'platform' => $draw['platform'],
                'channel' => $draw['channel'],
                'message' => $draw['message'] . ' ' . $type . ' ' . $index,
                'message_input' => $draw['message_input'] . ' ' . $type . ' ' . $index,
                'state' => 0, // 0生成中 1已生成 2生成失败
                'options' => $draw['options'],
                'create_time' => time()
            ]);

        // 扣费
        changeUserDrawBalance(self::$user['id'], -1, '绘画消费');

        $apikey = $this->getApiKey($draw['channel']);
        $notifyUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/api.php/notify/lxai';
        $lxaiSDK = new \ChatGPT\lxai($apikey);
        $result = $lxaiSDK->mjCtrl([
            'id' => $draw['task_id'],
            'type' => $type,
            'index' => $index,
            'notifyHook' => $notifyUrl
        ]);
        if ($result['errno']) {
            $this->setDrawFail($draw_id, $result['message']);
        }

        $task_id = $result['data'] ?? '';
        Db::name('msg_draw')
            ->where('id', $draw_id)
            ->update([
                'task_id' => $task_id
            ]);

        return successJson([
            'draw_id' => $draw_id
        ]);
    }

    private function setKeyStop($channel, $key, $errorMsg)
    {
        if ($errorMsg) {
            Db::name('keys')
                ->where([
                    ['site_id', '=', self::$site_id],
                    ['type', '=', $channel],
                    ['key', '=', $key]
                ])
                ->update([
                    'state' => 0,
                    'stop_reason' => $errorMsg
                ]);
        }
    }

    /**
     * 供前端轮询绘画结果
     */
    public function getDrawResult()
    {
        $draw_id = input('draw_id', 0, 'intval');
        $draw = Db::name('msg_draw')
            ->where([
                ['site_id', '=', self::$site_id],
                ['id', '=', $draw_id],
                ['is_delete', '=', 0]
            ])
            ->find();
        if (!$draw) {
            // 未找到任务
            return successJson([
                'state' => -1
            ]);
        }
        if ($draw['state'] == 0) {
            $now = time();
            if ($now - $draw['create_time'] > 300) {
                $errMsg = '图片生成失败';
                // 生成超时退费
                changeUserDrawBalance(self::$user['id'], 1, '绘画失败退费');
                $this->setDrawFail($draw_id, $errMsg);
                return successJson([
                    'state' => 2,
                    'message' => $errMsg
                ]);
            }
            return successJson([
                'state' => 0
            ]);
        } elseif ($draw['state'] == 1) {
            return successJson([
                'state' => 1,
                'images' => explode('|', $draw['images'])
            ]);
        } elseif ($draw['state'] == 2) {
            return successJson([
                'state' => 2,
                'message' => $draw['errmsg']
            ]);
        } else {
            // 未知状态
            return successJson([
                'state' => -1
            ]);
        }
    }

    private function setDrawFail($draw_id, $errMsg)
    {
        Db::name('msg_draw')
            ->where('id', $draw_id)
            ->update([
                'state' => 2,
                'errmsg' => $errMsg
            ]);
    }

    /**
     * 保存图片文件内容到本地或者云存储
     */
    private function saveToFile($content)
    {
        if (strpos($content, 'https://') !== false || strpos($content, 'http://') !== false) {
            $context = stream_context_create([
                'http' => ['method' => 'GET', 'timeout' => 30],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
            ]);
            $content = file_get_contents($content, false, $context);
        } else {
            $content = base64_decode($content);
        }
        if (empty($content)) {
            return '';
        }
        // 保存到本地
        $date = date('Ymd');
        $dir = './upload/draw/' . $date . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filepath = $dir . self::$user['id'] . uniqid() . '.png';
        file_put_contents($filepath, $content);
        if (!file_exists($filepath)) {
            return '';
        }
        $url = saveToOss($filepath);

        return $url;
    }

    /**
     * 从key池里取回一个key
     */
    private function getApiKey($type = 'openai')
    {
        $rs = Db::name('keys')
            ->where([
                ['site_id', '=', self::$site_id],
                ['type', '=', $type],
                ['state', '=', 1]
            ])
            ->order('last_time asc, id asc')
            ->find();
        if (!$rs) {
            return '';
        }
        Db::name('keys')
            ->where('id', $rs['id'])
            ->update([
                'last_time' => time()
            ]);
        return $rs['key'];
    }

    private function httpRequest($url, $post = '')
    {
        $token = session_id();
        $header = [
            'x-token: ' . $token ?? ''
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 15000);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_exec($ch);
        curl_close($ch);
    }

    public function getDrawSetting()
    {
        $drawSetting = getSystemSetting(self::$site_id, 'draw');
        $setting = [
            'channel' => $drawSetting['channel'] ?? 'openai'
        ];
        if ($drawSetting['channel'] == 'openai') {
            $options = [];
        } elseif ($drawSetting['channel'] == 'api2d') {
            $options = [];
        }  elseif ($drawSetting['channel'] == 'lxai') {
            $options = [];
        }   elseif ($drawSetting['channel'] == 'replicate') {
            $options = [];
        } elseif ($drawSetting['channel'] == 'yijian') {
            $options = json_decode('{"imageSizes":[{"text":"4:3","value":0,"desc":"1200x900"},{"text":"3:4","value":1,"desc":"900x1200"},{"text":"1:1","value":2,"desc":"1024x1024"},{"text":"16:9","value":3,"desc":"1280x720"},{"text":"9:16","value":4,"desc":"720x1280"}],"stableArtists":[{"id":"0","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"不限定","value":""},{"id":"1","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/msr_xjr.png","text":"马塞尔·夏加尔","value":"MarcelChagall"},{"id":"2","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/csl.png","text":"村上隆","value":"TakashiMurakami"},{"id":"3","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/mx.png","text":"穆夏","value":"AlphonseMucha"},{"id":"4","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bjs.png","text":"毕加索","value":"PabloPicasso"},{"id":"5","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/fg.png","text":"梵高","value":"VincentvanGogh"},{"id":"6","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/nm.png","text":"莫奈","value":"ClaudeMonet"},{"id":"7","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bl_ss.png","text":"保罗·塞尚","value":"PaulCezanne"},{"id":"8","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/tms_kr.png","text":"托马斯·科尔","value":"ThomasCole"},{"id":"9","img_words":"","poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/mbws.png","text":"莫比乌斯","value":"Moebius"}],"styleDetails":[{"GroupName":"通用设计","Styles":[{"default_guide_scale":7.5,"engine":"default_dreamer_diffusion","group_name":"通用设计","id":"103","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/ZN.png","sub_styles":[],"text":"智能","value":""},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"0","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","sub_styles":[],"text":"不限定","value":""},{"default_guide_scale":7.5,"engine":"vinteprotogenmixV10_diffusion","group_name":"通用设计","id":"24","img_words":"新","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/lbxzs.png","sub_styles":[],"text":"六边形战士","value":""},{"default_guide_scale":7.5,"engine":"mid_stable_diffusion","group_name":"通用设计","id":"3","img_words":"热","is_color_enhance":true,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/ysgqh.png","sub_styles":[],"text":"艺术感强化","value":""},{"default_guide_scale":7.5,"engine":"redshift_novelai_sd_merge_diffusion","group_name":"通用设计","id":"4","img_words":"新","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/3d_jmfg.png","sub_styles":[],"text":"3D建模风格","value":""},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"7","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/yh.png","sub_styles":[],"text":"油画","value":" very detailed oil painting, oil on canvas"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"8","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/xhyq.png","sub_styles":[],"text":"虚幻引擎渲染","value":" unreal engine render, 8k"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"9","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/qbsm.png","sub_styles":[],"text":"铅笔素描","value":" milt kahl pencil sketch"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"10","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/schh.png","sub_styles":[],"text":"水彩绘画","value":" in watercolor gouache detailed paintings, insanely detail"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"11","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/1950nd.png","sub_styles":[],"text":"1950年代纸浆科幻封面","value":" style of 1950s pulp sci-fi cover"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"12","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/xhc.png","sub_styles":[],"text":"新海诚","value":" by makoto shinkai"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"13","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/Victo_Ngai.png","sub_styles":[],"text":"倪传婧","value":" by victo ngai"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"14","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/xsh.png","sub_styles":[],"text":"像素画","value":" 64-bit pixel art"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"15","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/zgf.png","sub_styles":[],"text":"中国画","value":" chinese ink-wash painting style"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"16","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/sbpk.png","sub_styles":[],"text":"赛博朋克","value":" hyper realistic cyberpunk style"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"17","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/jpl.png","sub_styles":[],"text":"吉卜力","value":" artwork by studio ghibli, lighting, clear focus, very coherent, high detailed"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"18","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/zqb.png","sub_styles":[],"text":"蒸汽波","value":" vaporwave arcade, 4k, ultra realistic, award winning photograph"},{"default_guide_scale":7.5,"engine":"stable_diffusion","group_name":"通用设计","id":"19","img_words":"","is_color_enhance":false,"is_need_artists":true,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/yxzy.png","sub_styles":[],"text":"印象主义","value":" in claude monet style"}],"ShowImage":"https://app.yjai.art:30108/ai-painting-control/type_style1.jpg"},{"GroupName":"动漫设计","Styles":[{"default_guide_scale":11,"engine":"anything4_diffusion","group_name":"动漫设计","id":"20","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/newdmbeta.png","sub_styles":[{"group_name":"动漫设计","id":4,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"动漫设计","id":5,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/dmxg.png","text":"动漫线稿","value":"<lora:animeLineartStyle_v20Offset:1>"},{"group_name":"动漫设计","id":6,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/gachanh.png","text":"gacha女孩","value":"<lora:gachaSplashLORA_gachaSplashFarShot:0.9>"},{"group_name":"动漫设计","id":7,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/xhc.png","text":"新海诚","value":"<lora:makotoShinkaiSubstyles_offset:1>"},{"group_name":"动漫设计","id":11,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/fc.png","text":"沁彩","value":"<lora:Colorwater_v4:0.9>"},{"group_name":"动漫设计","id":12,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/jpl.png","text":"吉卜力","value":"<lora:studioGhibliStyle_offset:1>"},{"group_name":"动漫设计","id":13,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/Qban.png","text":"Q版","value":"<lora:maplestoryStyle_v20:0.8>"},{"group_name":"动漫设计","id":14,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/kap.png","text":"可爱屁","value":"<lora:cutescrap05v_cutescrap3:0.8>"},{"group_name":"动漫设计","id":19,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/tlp.png","text":"塔罗牌","value":"<lora:animeTarotCardArtStyleLora_v20Offset:0.8>"},{"group_name":"动漫设计","id":20,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/nygy.png","text":"暗夜光影","value":"<lora:lightAndShadow_v10:0.8>"},{"group_name":"动漫设计","id":21,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/sygy.png","text":"实用光影","value":"<lora:sunAndShadow_v10:0.8>"},{"group_name":"动漫设计","id":22,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/90ndf.png","text":"90年代风","value":"<lora:1990sAnimeStyleLora_1:0.6>"},{"group_name":"动漫设计","id":23,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/cxtmh.png","text":"粗线条漫画","value":"<lora:thickerLinesAnimeStyle_loraVersion:1>"},{"group_name":"动漫设计","id":28,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/xrs.png","text":"小人书","value":"<lora:Xiaorenshu_v10:0.9>"},{"group_name":"动漫设计","id":29,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/zgf.png","text":"中国风","value":"<lora:loraGuofeng2Lora_v20Lora:0.8>"},{"group_name":"动漫设计","id":50,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/jjdm.png","text":"极简动漫","value":"<lora:minimalistAnimeStyle_v10:0.9>"},{"group_name":"动漫设计","id":53,"parent_id":20,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/cmsn.png","text":"彩墨少女","value":"<lora:quicksketch_v1:1>"}],"text":"新动漫beta","value":""},{"default_guide_scale":11,"engine":"counterfeit_diffusion","group_name":"动漫设计","id":"28","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/qxdm.png","sub_styles":[{"group_name":"动漫设计","id":31,"parent_id":28,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"动漫设计","id":32,"parent_id":28,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/shouban.png","text":"手办","value":"<lora:figmaAnimeFigures_figma:1>"},{"group_name":"动漫设计","id":42,"parent_id":28,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/qxt.png","text":"轻线条","value":"<lora:lightlineArtLora_v10:0.6>"},{"group_name":"动漫设计","id":49,"parent_id":28,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/gachanh.png","text":"gacha女孩","value":"<lora:gachaSplashLORA_gachaSplashFarShot:0.9>"},{"group_name":"动漫设计","id":58,"parent_id":28,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/gcgx.png","text":"古色古香","value":"<lora:miaobishenghua_v10:1>"}],"text":"清新动漫","value":""},{"default_guide_scale":7.5,"engine":"best_colorful_diffusion","group_name":"动漫设计","id":"100","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/fcdm2.png","sub_styles":[{"group_name":"动漫设计","id":106,"parent_id":100,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"动漫设计","id":107,"parent_id":100,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/fzmh.png","text":"纷争盲盒","value":"<lora:blindbox_V1Mix:1>"},{"group_name":"动漫设计","id":110,"parent_id":100,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/minisj.png","text":"迷你世界","value":"<lora:miniatureV1:0.8>"},{"group_name":"动漫设计","id":113,"parent_id":100,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/gdmh.png","text":"高达盲盒","value":"<lora:blindbox_V1Mix:1><lora:gundamRX782OutfitStyle_v10:0.4>"}],"text":"瑞士军刀","value":""},{"default_guide_scale":11,"engine":"acgn_diffusion","group_name":"动漫设计","id":"1","img_words":"热","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/jddm.png","sub_styles":[],"text":"经典动漫","value":""},{"default_guide_scale":7.5,"engine":"anygen_diffusion","group_name":"动漫设计","id":"21","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/2_5Ddm.png","sub_styles":[],"text":"2.5D动漫","value":""},{"default_guide_scale":7.5,"engine":"protothing_diffusion","group_name":"动漫设计","id":"22","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/ecych.png","sub_styles":[],"text":"二次元插画","value":""},{"default_guide_scale":7.5,"engine":"colorfulcocktail_diffusion","group_name":"动漫设计","id":"23","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/ecysc.png","sub_styles":[],"text":"二次元色彩","value":""},{"default_guide_scale":7.5,"engine":"dalcefo_diffusion","group_name":"动漫设计","id":"27","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/ptdm.png","sub_styles":[],"text":"平涂动漫","value":""},{"default_guide_scale":7.5,"engine":"flat_anime_diffusion","group_name":"动漫设计","id":"101","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/ptdm2.png","sub_styles":[{"group_name":"动漫设计","id":111,"parent_id":101,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"动漫设计","id":112,"parent_id":101,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/dancai.png","text":"淡彩","value":"<lora:LohaWhiteDewStyle_lohaV10:1>"}],"text":"平涂动漫2","value":""},{"default_guide_scale":7.5,"engine":"old_cos_diffusion","group_name":"动漫设计","id":"29","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/gzdm.png","sub_styles":[],"text":"古早动漫","value":""},{"default_guide_scale":7.5,"engine":"colorful_diffusion","group_name":"动漫设计","id":"30","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/fcdm.png","sub_styles":[],"text":"彩粉动漫","value":""},{"default_guide_scale":7.5,"engine":"meina_diffusion","group_name":"动漫设计","id":"32","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/2Dxs.png","sub_styles":[],"text":"2D写实","value":""},{"default_guide_scale":7.5,"engine":"meiman_diffusion","group_name":"动漫设计","id":"102","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type2/omdm.png","sub_styles":[],"text":"欧美动漫","value":""}],"ShowImage":"https://app.yjai.art:30108/ai-painting-control/type_style2.jpg"},{"GroupName":"现实设计","Styles":[{"default_guide_scale":7.5,"engine":"Gf_style2_diffusion","group_name":"现实设计","id":"26","img_words":"新","is_color_enhance":false,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/guofeng.png","sub_styles":[{"group_name":"现实设计","id":15,"parent_id":26,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"现实设计","id":16,"parent_id":26,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/csmb.png","text":"彩色水墨","value":"<lora:Moxin_10:0.8>"},{"group_name":"现实设计","id":17,"parent_id":26,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/lbsm.png","text":"留白水墨","value":"<lora:Moxin_Shukezouma11:0.7><lora:Moxin_10:0.8>"},{"group_name":"现实设计","id":24,"parent_id":26,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/sgf.png","text":"三国风","value":"<lora:Sanguo_v099:1>"}],"text":"国风","value":""},{"default_guide_scale":7.5,"engine":"lora_cod_diffusion","group_name":"现实设计","id":"6","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/Doll.png","sub_styles":[{"group_name":"现实设计","id":0,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"现实设计","id":1,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanxi1.png","text":"韩系女生","value":"<lora:koreanDollLikeness_v10:0.66>"},{"group_name":"现实设计","id":2,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/xiaoriben.png","text":"日系女生","value":"<lora:japaneseDollLikeness_v10:0.66>"},{"group_name":"现实设计","id":8,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanfu.png","text":"汉服风","value":"<lora:hanfu_v28:1><lora:shojovibe_v11:0.6>"},{"group_name":"现实设计","id":54,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanfu_s.png","text":"汉服风(宋)","value":"<lora:taiwanDollLikeness_v10:0.3><lora:shojovibe_v11:0.3><lora:hanfu_v29:0.8>hanfu, song style outfits, song hanfu, "},{"group_name":"现实设计","id":55,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanfu_t.png","text":"汉服风(唐)","value":"<lora:taiwanDollLikeness_v10:0.3><lora:shojovibe_v11:0.3><lora:hanfu_v29:0.8>hanfu, tang style outfits, tang hanfu, "},{"group_name":"现实设计","id":56,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanfu_m.png","text":"汉服风(明)","value":"<lora:taiwanDollLikeness_v10:0.3><lora:shojovibe_v11:0.3><lora:hanfu_v29:0.8>hanfu, ming style outfits, ming hanfu, "},{"group_name":"现实设计","id":57,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanfu_h.png","text":"汉服风(汉)","value":"<lora:taiwanDollLikeness_v10:0.3><lora:shojovibe_v11:0.3><lora:hanfu_v29:0.8>hanfu, han style outfits, han hanfu, "},{"group_name":"现实设计","id":9,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/ssns.png","text":"时尚女生","value":"<lora:fashionGirl_v50:0.6>"},{"group_name":"现实设计","id":10,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/kanh.png","text":"可爱女生","value":"<lora:cuteGirlMix4_v10:0.5>"},{"group_name":"现实设计","id":51,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/mhsn.png","text":"梦幻少女","value":"<lora:dreamyGirlsFace_dreamyFace:0.6>"},{"group_name":"现实设计","id":26,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/qbq.png","text":"铅笔裙","value":"<lora:hauteCouturePencil_v10:0.7><lora:koreanDollLikeness_v15:0.3>"},{"group_name":"现实设计","id":27,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/qipao.png","text":"旗袍","value":"<lora:qipao_8:0.6><lora:zhouzhou_zsyV10:0.4><lora:koreanDollLikeness_v15:0.4>"},{"group_name":"现实设计","id":30,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/guofengnh.png","text":"国风","value":"<lora:loraGuofeng2Lora_v20Lora:1.6>"},{"group_name":"现实设计","id":47,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/zzsn.png","text":"正装少女","value":"<lora:recruitSuit_recsuitVer:1><lora:cuteGirlMix4_v10:0.4>"},{"group_name":"现实设计","id":59,"parent_id":6,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/lfxn.png","text":"礼服新娘","value":"<lora:chineseGirlsInWeddingDressOrHakamaOrHanfuInMarvellousScene_v01:0.9>"}],"text":"Doll模型","value":""},{"default_guide_scale":7.5,"engine":"lucky_real_diffusion","group_name":"现实设计","id":"31","img_words":"新","is_color_enhance":false,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/xDoll.png","sub_styles":[{"group_name":"现实设计","id":33,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type1/bxd.png","text":"默认","value":""},{"group_name":"现实设计","id":43,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/hanxi1.png","text":"韩系女生","value":"<lora:koreanDollLikeness_v10:0.66>"},{"group_name":"现实设计","id":45,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/xiaoriben.png","text":"日系女生","value":"<lora:japaneseDollLikeness_v10:0.66>"},{"group_name":"现实设计","id":35,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/ssns.png","text":"时尚女生","value":"<lora:fashionGirl_v50:0.5>"},{"group_name":"现实设计","id":36,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/xcns.png","text":"乡村女生","value":"<lora:villageGirlCungu_v12:0.6>"},{"group_name":"现实设计","id":37,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/qznh.png","text":"气质女生","value":"<lora:aiBeautyIthlinni_ithlinniV1:0.5>"},{"group_name":"现实设计","id":38,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/kanh.png","text":"可爱女生","value":"<lora:cuteGirlMix4_v10:0.5>"},{"group_name":"现实设计","id":39,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/jznh.png","text":"精致女生","value":"<lora:tifosemix_v1064s:0.6>"},{"group_name":"现实设计","id":40,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/mlsn.png","text":"美丽少女","value":"<lora:shojovibe_v11:0.6>"},{"group_name":"现实设计","id":46,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/zzsn.png","text":"正装少女","value":"<lora:recruitSuit_recsuitVer:1><lora:shojovibe_v11:0.7>"},{"group_name":"现实设计","id":52,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/mhsn.png","text":"梦幻少女","value":"<lora:dreamyGirlsFace_dreamyFace:0.6>"},{"group_name":"现实设计","id":48,"parent_id":31,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/qbq.png","text":"铅笔裙","value":"<lora:hauteCouturePencil_v10:0.7><lora:koreanDollLikeness_v15:0.3>"}],"text":"新Doll模型","value":""},{"default_guide_scale":7.5,"engine":"goodAsianGirlFaceV10_diffusion","group_name":"现实设计","id":"25","img_words":"新","is_color_enhance":true,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/zsrx.png","sub_styles":[],"text":"真实人像","value":""},{"default_guide_scale":7.5,"engine":"photoreal_engine","group_name":"现实设计","id":"5","img_words":"新","is_color_enhance":false,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/zsgzp.png","sub_styles":[],"text":"真实感照片","value":""},{"default_guide_scale":7.5,"engine":"pvc_diffusion","group_name":"现实设计","id":"104","img_words":"新","is_color_enhance":false,"is_need_artists":false,"poster":"https://yijian-painting-prod.cdn.bcebos.com/static/styles/style_type3/shouban.png","sub_styles":[],"text":"手办","value":""}],"ShowImage":"https://app.yjai.art:30108/ai-painting-control/type_style3.jpg"}],"systemPrompts":[{"artist_id":"0","engine_id":"3","text":"水晶球城堡","value":"水晶球里的彩色魔法城堡"},{"artist_id":"0","engine_id":"2","text":"美貌少年","value":"顶级打光, 仙境, 云朵, 飞鸟, boy, 少年, 正太, 可爱的脸, 金色瞳孔, 白色短发, 华丽的耳钉, 面无表情, 最高画质, 大师之作, 杰作, 惊艳, 美貌, 凌乱的发丝, 大师之作, 细致的脸部描写, 扑克牌, 美人痣, 异色瞳"},{"artist_id":"0","engine_id":"3","text":"壮丽的树","value":"Magnificent tree,seen from a distance,science fiction,Holy terror,mystery,fantasy,sense of technology,unreal engine,metallic texture,Volume light,Look up,colorful,Super wide angle,magnificent,great spectacle,by Raphael Lacoste"},{"artist_id":"0","engine_id":"3","text":"半机械马","value":"Realistic portrait beautiful detailed matte painting of  cinematic movie scene jet li mutate into cyborg  horse. horror, created by gustave dore and greg  rutkowski, high detailed, smooth draw, synthwave  neon retro, intricate, realistic proportions, dramatic  lighting, trending on artstation."},{"artist_id":"5","engine_id":"0","text":"星空","value":"星空"},{"artist_id":"0","engine_id":"4","text":"蓝色人种","value":"Portrait of a blue skin genasi with a square jaw from d & d by greg rutkowski, dreadlocks and small beard, tempest priest, runic rings, d & d character, blue, black background, highly detailed portrait, digital painting, artstation, concept art, smooth, sharp foccus ilustration, artstation hq"},{"artist_id":"0","engine_id":"4","text":"小兔子","value":"Portrait of a super cute bunny, a carrot, pixar, zootopia, cgi, blade runner. trending on artstation"},{"artist_id":"0","engine_id":"4","text":"星际狐狸","value":"Underwater steampunk biopunk portrait of fox mccloud from star fox ( 1 9 9 3 ), hyper detailed, digital art, trending in artstation, cinematic lighting, studio quality, smooth render, unreal engine 5 rendered, octane rendered, art style by klimt and nixeu and ian sprigger and wlop and krenz cushart."},{"artist_id":"0","engine_id":"3","text":"雄狮","value":"A male anthro muscular albino lion\'s face breaching through a wall of water, headshot, water sprites, splashing, deep blue ocean, highly detailed, realistic digital art, trending on artstation, character design by charlie bowater, ross tran, artgerm, and makoto shinkai, detailed, inked, western comic book art, 2021 award winning painting"},{"artist_id":"0","engine_id":"16","text":"月下独舞","value":"An emotional concept painting of a cyberpunk  android dancing in the moonlight, neon signs, empty  city, large detailed moon, concept painting by  raymond swanland and ruan jia and greg rutkowski"},{"artist_id":"0","engine_id":"0","text":"寺庙&云彩","value":"Overlooking brilliant temples,journey to the west,lakes,clouds and sun,fairy tales,light effects,fantasy,SAMUEL BEAL,abhimanyu bhadauria,artstation,colorful"},{"artist_id":"0","engine_id":"3","text":"老骑士","value":"Portrait of an old knight with a large moustache, male, detailed face, fantasy, highly detailed, cinematic lighting, digital art painting by greg rutkowski"},{"artist_id":"0","engine_id":"0","text":"中国城堡&桃花树","value":"A beautiful painting of Chinese fairyland full of peach blossom trees and Chinese castles, cloudy and foggy by Makoto Shinkai"},{"artist_id":"0","engine_id":"2","text":"未来古城","value":"Evil robot attacking feudal japan city, moody sky, dramatic lighting, painted by james jean and wayne barlowe and moebius, high details, cinematic, denoised, octane render, fog, spooky, cgsociety 8k"},{"artist_id":"0","engine_id":"3","text":"动画都市","value":"A japanese city near the sea, lofi, dreamy, moody, very colorful, anime inspiration, makoto shinkai, ghibli vibe"},{"artist_id":"0","engine_id":"16","text":"赛博朋克都市","value":"Very detailed masterpiece painting of a busy  cyberpunk city street, portrait, artstation, concept art  by greg rutkowski"},{"artist_id":"0","engine_id":"3","text":"灰狼肖像","value":"Portrait of a gray wolf, wolf face, intricate, elegant,  highly detailed, digital painting, artstation, concept  art, smooth, sharp focus, illustration, art by krenz  cushart and artem demura and alphonse mucha"},{"artist_id":"0","engine_id":"2","text":"大教堂","value":"arch,architecture,book_stack,bookshelf,building,candle,chandelier,city,city_lights,cityscape,copyright_name,fire,indoors,library,lantern,skyscraper,standing,sunset,clock,gears,butterfly,window,cinematic, epic composition,no_humans,scenery,detailed, atmospheric, artstation trending"},{"artist_id":"0","engine_id":"2","text":"海底世界","value":"A beautiful matte digital painting of a light-green sea turtle swimming over an red-orange coral reef through blue-violet waters, triadic color palette, painted in the style of national geographic, trending on artstation hq"},{"artist_id":"0","engine_id":"2","text":"故乡的原野","value":"Anime screenshot wide-shot landscape with house in the apple garden, beautiful ambiance, golden hour, studio ghibli style, by hayao miyazaki, highly detailed"}]}');
        }

        $setting['options'] = $options;
        return successJson($setting);
    }

}
