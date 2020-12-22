<?php
require_once __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$exchange = 'Gaming';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'myuser', 'mypass');

$channel = $connection->channel();
$channel->exchange_declare($exchange, 'direct', false, false, false);

$route = ['dota', 'csgo', 'lol'];

for ($i=0; $i<100; ++$i) {

    $key = array_rand($route);
    $arr = [
        'match_id' => $i,
        'status' => random_int(0,3),
        'type' => $key,
        'time' => date('YmdHis'),
    ];
    $data = json_encode($arr);
    $msg = new AMQPMessage($data);

    $channel->basic_publish($msg, $exchange, $route[$key]);
    echo '发送', $route[$key], '消息', $data, PHP_EOL;
}

$channel->close();
$connection->close();