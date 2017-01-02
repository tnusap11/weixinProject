<?php

class IndexAction extends Action {
    function __construct() {

    }

    public function index() {

//        1.获得参数 signature, nonce, token, timestamp
        $nonce = $_GET['nonce'];
        $token = 'tnusapweixin';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
//        形成数组,然后按字典排序
//        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
//        拼接成字符串,sha1加密,然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
//            第一次接入weixin接口的时候
            echo $echostr;
            exit;
        } else {
            $this->reponseMsg();
        }
    }

    // 接收事件推送并回复
    public function reponseMsg() {
//        1.获取到微信推送过来的post数据(xml格式)
//        获得全部变量的全局组合数组
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $tmpstr = $postArr;
//        2.处理消息类型,并设置回复类型和内容
        /*      <xml>
                    <ToUserName><![CDATA[toUser]]></ToUserName>
                    <FromUserName><![CDATA[FromUser]]></FromUserName>
                    <CreateTime>123456789</CreateTime>
                    <MsgType><![CDATA[event]]></MsgType>
                    <Event><![CDATA[subscribe]]></Event>
                </xml>*/
        $postObj = simplexml_load_string($postArr);
//        $postObj->ToUserName = '';
//        $postObj->FromUserName = '';
//        $postObj->CreateTime = '';
//        $postObj->MsgType = '';
//        $postObj->Event = '';
//        判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
//            如果是关注subscribe事件
            if (strtolower($postObj->Event == 'subscribe')) {

                $arr = array(
                    array('title' => '欢迎欢迎!',
                        'description' => "现在还什么都没有, 微小的工作还在努力, 非常惭愧!",
                        'picUrl' => 'http://www.bz55.com/uploads/allimg/150526/140-150526120501.jpg',
                        'url' => 'http://10pic.kfd.me/',
                    ),
                    array('title' => '飞飞飞飞儿',
                        'description' => " ",
                        'picUrl' => 'http://www.bz55.com/uploads/allimg/150515/140-150515094254-50.jpg',
                        'url' => 'http://www.bz55.com/uploads/allimg/150515/140-150515094254-50.jpg',
                    ),
                );


                $indexModel = new IndexModel();
                $indexModel->responseSubscribe($postObj, $arr);
            }
        }
//        当用户发送的内容非空时回复
        if (!empty($postObj)) {
            $postContent = trim($postObj->Content);
//            用户发送关键字,回复单图文
            if ($postObj->MsgType && strtolower($postContent == '刘园')) {
                $arr = array(
                    array('title' => '比Gabrielle Anwar还要美!',
                        'description' => "美丽如Gabrielle Anwar",
                        'picUrl' => 'https://img1.doubanio.com/view/photo/photo/public/p838010247.jpg',
                        'url' => 'http://music.163.com/m/song?id=31234247&userid=2360069',
                    ),
                    array('title' => '比松隆子还要漂亮!!',
                        'description' => "比松隆子还要漂亮!!",
                        'picUrl' => 'https://img1.doubanio.com/view/photo/photo/public/p642969887.jpg',
                        'url' => 'https://img1.doubanio.com/view/photo/photo/public/p642969887.jpg',
                    ),
                    array('title' => '比新垣结衣还要可爱!!!',
                        'description' => "比新垣结衣还要可爱!!!",
                        'picUrl' => 'http://www.gakky.me/wp-content/uploads/2014/06/Kose-Precious-Beauty-No.44-4.jpg',
                        'url' => 'http://www.gakky.me/wp-content/uploads/2014/06/Kose-Precious-Beauty-No.44-4.jpg',
                    ),

                );
//              实例化模型,多图文类型
                $indexModel = new IndexModel();
                $indexModel->responseNews($postObj, $arr);
            } else {
                switch ($postContent) {
                    case 1:
                        $content = '你输入的数字是1';
                        break;
                    case 2:
                        $content = '你输入的数字是2';
                        break;
                    case 3:
                        $content = '你输入的数字是3';
                        break;
                    case "bing":
                        $content = "<a href='http://cn.bing.com'>bing</a>";
                        break;
                    case '一颗赛艇':
                        $content = '膜法师';
                        break;
                    case '5':
                        $content = '5555';
                        break;
                    default:
//                        接入聊天机器人
//                        接入天气
                        $indexModel = new IndexModel();
                        $content = $indexModel->getWeather($postContent);
                        if (empty($content['status'])) {
                            $content = $content['results'][0]['location']['name'] . ' ' . $content['results'][0]['daily']['0']['text_night'] . ' 当天最高温: ' . $content['results'][0]['daily']['0']['high'] . '度 当天最低温: ' . $content['results'][0]['daily']['0']['low'] . '度';
                        } else {
                            $indexModel = new IndexModel();
                            $content = $indexModel->getRobot($postContent);
                        }
                }


            }
//            实例化模型
//            回复单文本
            $indexModel = new IndexModel();
            $indexModel->responseText($postObj, $content);
        }


        function http_curl() {
            //获取imooc
            //1.初始化curl
            $ch = curl_init();
            $url = 'http://www.imooc.com';
            //2.设置curl的参数
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //3.采集
            $output = curl_exec($ch);
//            curl_exec($ch);
            //4.关闭
            curl_close($ch);
            var_dump($output);
            var_dump('11');
        }

//        获取accessToken
        function getWxAccessToken() {
//            1.请求url地址
            $appid = 'wx99e58534ab67fa9d';
            $appsecret = 'ac8937dbf974be08953e43513d852c26';
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
//            2.初始化
            $ch = curl_init();
//            3.设置参数
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

//            4.调用接口
            $res = curl_exec($ch);

            if (curl_errno($ch)) {
                var_dump(curl_error($ch));
            }
            $arr = json_decode($res, true);

            var_dump($arr);
            return $arr;
//            5.关闭curl
//            curl_close($ch);
        }

//        getWxAccessToken();
//        获取微信服务器IP地址
        function getWxServerIp() {
            $accessToken = 'XOW9eqNn1FV17nVckFXH_rSbcSOsUUIm67u0MabRtkImNqjxiV3GVygsu-l-sRXazWXLShGD3BXTtDQkRvQwAhnt6ZbB5O-chzpOhtiJdi2IDsD8-TFd6Z-uwHHt1XazMVPeAJAYHB';
            $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=" . $accessToken;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $res = curl_exec($ch);
            curl_close($ch);
            if (curl_errno($ch)) {
                var_dump(curl_error($ch));
            }
            $arr = json_decode($res, true);
            echo "<pre>";
            var_dump($arr);
            echo "</pre>";
        }

        function getWeather() {
//        $info = utf8_encode($postContent);
            $apiURL = "http://wthrcdn.etouch.cn/weather_mini?city=北京";
//            2.初始化
            $ch = curl_init();
//            3.设置参数
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//            4.调用接口
            $res = curl_exec($ch);
            if (curl_errno($ch)) {
                var_dump(curl_error($ch));
            }
            $arr = json_decode($res, true);
            $content = $arr['city'];
            var_dump($arr);
//        return $content;
        }

        getWeather();

//        getJiqiren('你好');
    }
}