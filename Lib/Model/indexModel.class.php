<?php


class IndexModel extends Model {
//    回复多图文类型的微信消息
    public function responseNews($postObj, $arr) {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "
            <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <ArticleCount>" . count($arr) . "</ArticleCount>
                <Articles>";
        foreach ($arr as $k => $v) {
            $template .= "
                <item>
                    <Title><![CDATA[" . $v['title'] . "]]></Title> 
                    <Description><![CDATA[" . $v['description'] . "]]></Description>
                    <PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
                    <Url><![CDATA[" . $v['url'] . "]]></Url>
                </item>";
        }
        $template .= "
                </Articles>
             </xml>";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
    }

//    回复单文本
    public function responseText($postObj, $content) {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time = time();
        $msgType = 'text';
        $template = "
            <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
            </xml>
         ";
        printf($template, $toUser, $fromUser, $time, $msgType, $content);
    }

//    回复微信用户的关注事件
    public function responseSubscribe($postObj, $arr) {
        $this->responseNews($postObj, $arr);
    }

//        接入聊天机器人
    function getRobot($postContent) {
        $apiKey = '6f47fbed96cb424fb7f10d6a7c9fabbd';
        $info = urlencode($postContent);
        $apiURL = "http://www.tuling123.com/openapi/api?key=" . $apiKey . "&info=" . $info;
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

        if ($arr['url']) {

            $content = $arr['text'] . ': ' . $arr['url'];
        } else {
            $content = $arr['text'];
        }
//            var_dump($arr);
        return $content;
    }

//    接入天气接口
    function getWeather($cityName) {
//        将字符串编码并将其用于 URL 的请求部分
        $cityName = urlencode($cityName);
        $apiURL = "https://api.thinkpage.cn/v3/weather/daily.json?key=udrlv9ue3qj4hnij&location=$cityName&language=zh-Hans&unit=c&start=0&days=5";
//    2.初始化
        $ch = curl_init();
//    3.设置参数
        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//    4.调用接口
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump(curl_error($ch));
        }
        $arr = json_decode($res, true);
        if (!empty($arr)) {
//            echo $apiURL;
//            print_r($arr);
//            return ($arr['results'][0]['location']['name'] . ' ' . $arr['results'][0]['daily']['0']['text_night'] . ' 最高温: ' . $arr['results'][0]['daily']['0']['high'] . '度 最低温: ' . $arr['results'][0]['daily']['0']['low'] . '度');
            return $arr;
        } else {
            echo '空值';
        }
    }
}