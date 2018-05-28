<?php
/**
 * @examlpe
 *
 * $ding = new Dingtalk($accesstoken);
 * $ding->setText($text,[$atMobiles,$isAtAll])->send();
 * $ding->setLink($text,$title,$messageUrl,$picUrl)->send();
 * $ding->setMarkdown($text,$title)->send();
 *
 * author: heromark
 * email: heromark.myy@gmail.com
**/

namespace DingtalkChatbot;

class Dingtalk
{
    const DING_API = "https://oapi.dingtalk.com/robot/send";
    const MSG_TYPES = ['text', 'link', 'markdown'];

    private $_message;  // 消息体
    private $_web_hook; // webhook
    

    public function __construct($access_token)
    {
        $this->_web_hook = self::DING_API .'?access_token='. $access_token;
    }

    // 设置消息类型
    private function setMsgType($type)
    {
        if (in_array($type, self::MSG_TYPES)) {
            $this->_message['msgtype'] = $type; 
        }else{
            $this->_message['msgtype'] = 'text';
        }     
    }

    // 文本类型
    public function setText($text, $atMobiles = [], $isAtAll = false)
    {
        $this->setMsgType('text');
        if (empty($text)) {
            self::showMsg(null, 0, '内容不能为空');exit;
        }
        $this->_message['text'] = ['content' => $text];
        if (!empty($atMobiles)) {
            $this->_message['at']['atMobiles'] = $atMobiles;
        }
        if ($isAtAll) {
            $this->_message['at']['isAtAll'] = true;
        }

        return $this;
    }

    // link类型
    public function setLink($text, $title, $messageUrl, $picUrl = '')
    {
        $this->setMsgType('link');

        if (empty($text)) {
            self::showMsg(null, 0, '内容不能为空');exit;
        }else{
            $this->_message['link']['text'] = $text;
        }

        if (empty($title)) {
            self::showMsg(null, 0, '标题不能为空');exit;
        }else{
            $this->_message['link']['title'] = $title;
        }

        if (empty($messageUrl)) {
            self::showMsg(null, 0, '跳转链接不能为空');exit;
        }else{
            $this->_message['link']['messageUrl'] = $messageUrl;
        }

        if (!empty($picUrl)) {
            $this->_message['link']['picUrl'] = $text;
        }

        return $this;
    }

    // markdown类型
    public function setMarkdown($text, $title)
    {
        $this->setMsgType('markdown');

        if (empty($text)) {
            self::showMsg(null, 0, '内容不能为空');exit;
        }else{
            $this->_message['markdown']['text'] = $text;
        }

        if (empty($title)) {
            self::showMsg(null, 0, '标题不能为空');exit;
        }else{
            $this->_message['markdown']['title'] = $title;
        }

        return $this;
    }

    // 发送
    public function send()
    {
        $post_string = json_encode($this->_message);

        // $client = new GuzzleHttpClient(['headers' => ['Content-Type' => 'application/json;charset=utf-8']]);
        // $response = $client->request('POST', $this->_web_hook, ['body' => $post_string]);
        // $res = $response->getBody()->getContents();
        // $ret = json_decode($res,true);

        // if($ret['errmsg'] != 'ok'){
        //     return false;
        // }

        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $this->_web_hook);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        $res = curl_exec($ch);
        curl_close($ch);  
               
        $ret = json_decode($res,true);
        if($ret['errmsg'] != 'ok'){
            self::showMsg($ret, 0, '发送失败');exit;
        }

        self::showMsg($ret, 1, '发送成功');
    }

    private function showMsg($res, $state = 0, $msg = '')
    {
        // 构造数据
        $item = ['msg' => $msg, 'res' => null, 'state' =>(int)$state];
        is_array($res) && $item['res'] = $res;

        //编码
        $item = json_encode($item);
        echo "{$item}";
    }
}
