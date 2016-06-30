<?php
require('Wtt.class.php');
$WTT = new Wtt();

set_time_limit(0);

$server_ip   = '127.0.0.1';
$server_port = 2016;
$msg_max_len = 1024;
$is_connect  = false;

$socket      = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or exit();
$binder      = socket_bind($socket, $server_ip, $server_port) or exit();;
$listener    = socket_listen($socket, 4) or exit();

echo "[1] 服务器已经成功启动......\n";

do {
    $msgsock = socket_accept($socket) or exit();
    if (! $is_connect) {
        echo "[2] 客户端连接成功！\n";
        $is_connect = true;
    }
    $input = socket_read($msgsock, $msg_max_len);
    $msg   = $WTT->unpack($input);

    echo " - 收到客户端消息：" . $msg . "\n";

    $data   = strrev(trim($msg));
    $output = $WTT->pack($data);

    socket_write($msgsock, $output, strlen($output)) or exit();
    socket_close($msgsock);
} while (true);

socket_close($socket);
