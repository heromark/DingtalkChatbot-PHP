# composer-test
集成钉钉群机器人消息发送

# Usage
安裝：使用 composer 安裝，通过 autoload 引用

``` 
composer require "heromark/dingtalk-chatbot-php"

```

示例：

```
require "/path/to/your/vendor/autoload.php";

use DingtalkChatbot\Dingtalk;

$ding = new DingtalkChatbot\Dingtalk($accesstoken);
//发送 text 类型消息
$ding->setText($text,[$atMobiles,$isAtAll])->send();
//发送 link 类型消息
$ding->setLink($text,$title,$messageUrl,$picUrl)->send();
//发送 markdown 类型消息
$ding->setMarkdown($text,$title)->send();

```