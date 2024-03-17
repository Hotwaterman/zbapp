<?php

namespace Baidu;

class wenxin
{
    protected static $model = 'ERNIE-Bot-turbo';
    protected static $apikey = '';
    protected static $secretkey = '';
    protected static $temperature = 0.9;

    /**
     * sdk constructor.
     * @param string $apikey
     * @param string $secretkey
     */
    public function __construct($apikey, $secretkey, $temperature = '')
    {
        self::$apikey = $apikey;
        self::$secretkey = $secretkey;
        if ($temperature) {
            self::$temperature = $temperature;
        }
    }

    public function getAccessToken()
    {
        // 读取存储的 access_token
        $now = time();
        $accessTokenFile = __DIR__ . '/access_token_' . self::$apikey . '.php';
        if (file_exists($accessTokenFile)) {
            $content = trim(substr(file_get_contents($accessTokenFile), 15));
            $data = json_decode($content);
        }

        $access_token = '';
        if (empty($data) || $data->expire_time < $now) {
            // 获取新token
            $url = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=' . self::$apikey . '&client_secret=' . self::$secretkey;
            $res = json_decode(file_get_contents($url));
            if (!empty($res->access_token)) {
                $access_token = $res->access_token;
                $data = [
                    'access_token' => $access_token,
                    'expire_time' => $now + $res->expires_in
                ];
                file_put_contents($accessTokenFile, '<?php exit();?>' . json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }

        return $access_token;
    }

    /**
     * @param $message
     * @param $callback
     * @return array|mixed
     * ERNIE-Bot
     */
    public function sendText($message, $callback = null)
    {
        $url = 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions?access_token=' . $this->getAccessToken();
        $post = [
            'messages' => $message,
            'temperature' => self::$temperature,
            'stream' => true
        ];
        $result = $this->httpRequest($url, $post, $callback);

        return $this->handleError($result);
    }

    /**
     * @param $result
     * @return array|mixed
     * 格式化接口报错
     */
    protected function handleError($result)
    {
        if (isset($result['errno'])) {
            return [
                'errno' => 1,
                'message' => $result['message']
            ];
        }
        if (isset($result['error'])) {
            return [
                'errno' => 1,
                'message' => $result['error']['message']
            ];
        }
        // api2d的错误
        if (isset($result['object']) && $result['object'] == 'error') {
            return [
                'errno' => 1,
                'message' => $result['message']
            ];
        }

        return $result;
    }

    /**
     * http请求
     */
    protected function httpRequest($url, $post = null, $callback = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }
        if ($callback) {
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        }
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return [
                'errno' => 1,
                'message' => 'curl 错误信息: ' . curl_error($ch)
            ];
        }
        curl_close($ch);
        return json_decode($result, true);
    }
}
