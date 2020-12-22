<?php
require_once __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$exchange = 'Gaming';
$routerKey = 'lol'; //限制订阅类型

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'myuser', 'mypass');
$channel = $connection->channel();
$channel->exchange_declare($exchange, 'direct', false, false, false);
list($queueName, , ) = $channel->queue_declare('', false, false, true, false);
$channel->queue_bind($queueName, $exchange, $routerKey);

echo '等待消息中......', PHP_EOL;
$callback = function ($msg){
    echo  '接收到消息：', $msg->dekivery_info['routing_key'], ':', $msg->body, PHP_EOL;
    sleep(1);
};

$channel->basic_consume($queueName, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();