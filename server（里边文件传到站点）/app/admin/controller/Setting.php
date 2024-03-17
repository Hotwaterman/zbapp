<?php

namespace app\admin\controller;

use think\facade\Db;

class Setting extends Base
{
    public function getSetting()
    {
        $name = input('name', 'system', 'trim');

        $setting = getSystemSetting(self::$site_id, $name);

        if (!$setting || count($setting) == 0) {
            if ($name == 'system') {
                $setting = [
                    'system_title' => '',
                    'system_logo' => '',
                    'system_icp' => ''
                ];
            }
            if ($name == 'wxapp') {
                $setting = [
                    'title' => '',
                    'qrcode' => '',
                    'appid' => '',
                    'appsecret' => '',
                    'page_title' => '',
                    'welcome' => '',
                    'share_title' => '',
                    'share_image' => '',
                    'is_check' => 0,
                    'is_ios_pay' => 1
                ];
            }
            if ($name == 'wxapp_upload') {
                $setting = [
                    'upload_secret' => '',
                    'host' => ''
                ];
            }
            if ($name == 'wxapp_index') {
                $setting = [
                    'type' => 'chat',
                    'content' => ''
                ];
            }
            if ($name == 'chatgpt') {
                $setting = [
                    'channel' => 'openai',
                    'diyhost' => '',
                    'temperature' => 0.9,
                    'max_tokens' => 1500,
                    'model' => 'gpt-3.5-turbo',
                    'free_num' => 0,
                    'free_num_draw' => 0
                ];
            }
            if ($name == 'gpt4') {
                $setting = [
                    'is_open' => 0,
                    'channel' => 'openai',
                    'temperature' => 0.9,
                    'max_tokens' => 4000,
                    'model' => 'gpt-4',
                    'alias_wxapp' => '',
                    'alias_web' => ''
                ];
            }
            if ($name == 'pay') {
                $setting = [
                    'mch_id' => '',
                    'key' => '',
                    'apiclient_cert' => '',
                    'apiclient_key' => ''
                ];
            }
            if ($name == 'ad') {
                $setting = [
                    'inter_is_open' => 0,
                    'inter_unit_id' => '',
                    'banner_is_open' => 0,
                    'banner_unit_id' => ''
                ];
            }
            if ($name == 'reward_share') {
                $setting = [
                    'is_open' => 0,
                    'num' => '',
                    'max' => ''
                ];
            }
            if ($name == 'reward_invite') {
                $setting = [
                    'is_open' => 0,
                    'num' => '',
                    'max' => ''
                ];
            }
            if ($name == 'reward_ad') {
                $setting = [
                    'is_open' => 0,
                    'num' => '',
                    'max' => '',
                    'ad_unit_id' => ''
                ];
            }
            if ($name == 'about') {
                $setting = [
                    'content' => ''
                ];
            }
            if ($name == 'kefu') {
                $setting = [
                    'phone' => '',
                    'email' => '',
                    'wxno' => '',
                    'wx_qrcode' => '',
                    'qun_qrcode' => ''
                ];
            }
            if ($name == 'commission') {
                $setting = [
                    'is_open' => 0,
                    'auto_audit' => 0,
                    'deep' => 2,
                    'bili_1' => 0,
                    'bili_2' => 0
                ];
            }
            if ($name == 'web') {
                $setting = [
                    'is_open' => 0,
                    'bind_wxapp_user' => 0,
                    'page_title' => '',
                    'copyright' => '',
                    'copyright_link' => '',
                    'keywords' => '',
                    'description' => '',
                    'tongji' => ''
                ];
            }
            if ($name == 'h5') {
                $setting = [
                    'page_title' => '',
                    'share_title' => '',
                    'share_desc' => '',
                    'share_image' => ''
                ];
            }
            if ($name == 'wxmp') {
                $setting = [
                    'title' => '',
                    'appid' => '',
                    'appsecret' => '',
                    'token' => getNonceStr(32),
                    'aes_key' => getNonceStr(43),
                    'server_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/web.php/wxmp/server/site/' . self::$site_id
                ];
            }
            if ($name == 'chat') {
                $setting = [
                    'prompt_type' => 'system',
                    'prompt' => ''
                ];
            }
            if ($name == 'draw') {
                $setting = [
                    'platform' => 'openai',
                    'channel' => 'openai'
                ];
            }
            if ($name == 'login') {
                $setting = [
                    'login_wechat' => 1,
                    'login_phone' => 0
                ];
            }
            if ($name == 'sms') {
                $setting = [
                    'channel' => '',
                    'aliyun' => [],
                    'tencent' => []
                ];
            }
        }

        return successJson($setting);
    }

    public function setSetting()
    {
        $name = input('name', '', 'trim');
        $data = input('data', '', 'trim');
        $res = setSystemSetting(self::$site_id, $name, $data);
        if ($res) {
            if ($name == 'web' && self::$site_id == 1) {
                $data = json_decode($data, true);
                $page_title = $data['page_title'] ?? '';
                $keywords = $data['keywords'] ?? '';
                $description = $data['description'] ?? '';
                $tongji = $data['tongji'] ?? '';
                $this->replaceSEO($page_title, $keywords, $description, $tongji);
            }
            return successJson('', '保存成功');
        } else {
            return errorJson('保存失败，请重试！');
        }
    }

    /**
     * @param $title
     * @param $keywords
     * @param $description
     * 替换seo信息
     */
    private function replaceSEO($title, $keywords, $description, $tongji)
    {
        // 替换index的关键词
        $html = file_get_contents('./index.html');
        $html = str_replace("\n", "", $html);
        // 替换标题
        $html = preg_replace('/(<title>)(.*?)(<\/title>)/i', '${1}' . $title . '${3}', $html);
        // 替换关键词
        $html = preg_replace('/(<meta name="keywords" content=")(.*?)(">)/i', '${1}' . $keywords . '${3}', $html);
        // 替换描述
        $html = preg_replace('/(<meta name="description" content=")(.*?)(">)/i', '${1}' . $description . '${3}', $html);
        file_put_contents('./index.html', $html);

        // 替换web目录的关键词
        $html = file_get_contents('./web/index.html');
        $html = str_replace("\n", "", $html);
        // 替换标题
        $html = preg_replace('/(<title>)(.*?)(<\/title>)/i', '${1}' . $title . '${3}', $html);
        // 替换关键词
        $html = preg_replace('/(<meta name=keywords content=")(.*?)(">)/i', '${1}' . $keywords . '${3}', $html);
        // 替换描述
        $html = preg_replace('/(<meta name=description content=")(.*?)(">)/i', '${1}' . $description . '${3}', $html);
        // 替换统计代码
        $html = preg_replace('/(<\/body>)(.*?)(<\/html>)/i', '${1}' . $tongji . '${3}', $html);
        file_put_contents('./web/index.html', $html);
    }

    /**
     * 获取可选模型
     */
    public function getEngines()
    {
        $type = input('type', 'GPT3.5', 'trim');
        $list = Db::name('engine')
            ->where([
                ['state', '=', 1],
                ['type', '=', $type]
            ])
            ->field('title,name')
            ->select()->toArray();

        return successJson($list);
    }

    /**
     * 获取openai余额
     */
    public function getBalance()
    {
        $apikey = input('apikey', '', 'trim');
        if (empty($apikey)) {
            return errorJson('请输入apikey');
        }
        try {
            $ChatGPT = new \ChatGPT\openai($apikey);
            // 使用自定义接口
            $apiSetting = getSystemSetting(0, 'api');
            if ($apiSetting['channel'] == 'agent' && $apiSetting['agent_host']) {
                $ChatGPT->setApiHost(rtrim($apiSetting['agent_host'], '/'));
            }

            $result = $ChatGPT->getBalance();
            if (!isset($result['total_granted'])) {
                return errorJson('查询余额失败');
            }
            return successJson([
                'total_granted' => $result['total_granted'],
                'total_used' => $result['total_used'],
                'total_available' => $result['total_available']
            ]);
        } catch (\Exception $e) {
            return errorJson('查询余额失败');
        }
    }

    /**
     * 获取灵犀ai余额
     */
    public function getLxaiBalance()
    {
        $apikey = input('apikey', '', 'trim');
        if (empty($apikey)) {
            return errorJson('请输入apikey');
        }
        try {
            $lxaiSDK = new \ChatGPT\lxai($apikey);
            $result = $lxaiSDK->getBalance();
            if (!isset($result['total_granted'])) {
                return errorJson('查询余额失败');
            }
            return successJson([
                'total_granted' => $result['total_granted'],
                'total_used' => $result['total_used'],
                'total_available' => $result['total_available']
            ]);
        } catch (\Exception $e) {
            return errorJson('查询余额失败');
        }
    }
}
