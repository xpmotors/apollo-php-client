#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
use Org\Xpmotors\Namor\Client\NamorClient;

define('SAVE_DIR', __DIR__); //定义namor配置本地化存储路径

//指定env模板和文件
define('ENV_DIR', __DIR__.DIRECTORY_SEPARATOR.'env');
define('ENV_TPL', ENV_DIR.DIRECTORY_SEPARATOR.'.env_tpl.php');
define('ENV_FILE', ENV_DIR.DIRECTORY_SEPARATOR.'.env');

//定义namor配置变更时的回调函数，动态异步更新.env
$callback = function () {
    $list = glob(SAVE_DIR.DIRECTORY_SEPARATOR.'namorConfig.*');
    $namor = [];
    foreach ($list as $l) {
        $config = require $l;
        if (is_array($config) && isset($config['configurations'])) {
            $namor = array_merge($namor, $config['configurations']);
        }
    }
    if (!$namor) {
        throw new Exception('Load Namor Config Failed, no config available');
    }
    ob_start();
    include ENV_TPL;
    $env_config = ob_get_contents();
    ob_end_clean();
    file_put_contents(ENV_FILE, $env_config);
};

//指定namor的服务地址
$server = 'http://127.0.0.1:8081';

//指定appid
$appId = 'demo';

//指定要拉取哪些namespace的配置
$namespaces = ['application', 'system'];

$namor = new NamorClient($server, $appId, $namespaces);

//如果需要灰度发布，指定clientIp
/*
 * $clientIp = '10.160.2.131';
 * if (isset($clientIp) && filter_var($clientIp, FILTER_VALIDATE_IP)) {
 *    $namor->setClientIp($clientIp);
 * }
 */

//从namor上拉取的配置默认保存在脚本目录，可自行设置保存目录
$namor->save_dir = SAVE_DIR;

ini_set('memory_limit','128M');
$pid = getmypid();
echo "start [$pid]\n";
$restart = false; //失败自动重启
do {
    $error = $namor->start($callback); //此处传入回调
    if ($error) echo('error:'.$error."\n");
}while($error && $restart);
