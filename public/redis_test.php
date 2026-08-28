<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->set('test', 'ok');
echo $redis->get('test');