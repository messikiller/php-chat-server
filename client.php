<?php
set_time_limit(0);

$server_ip   = "127.0.0.1";
$server_port = 2016;
$msg_max_len = 1024;
$is_connect  = false;

echo "[1] 客户端启动成功......\n";

do {
    $socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or exit();
    $connect = socket_connect($socket, $server_ip, $server_port) or exit();

    if (! $is_connect) {
        echo "[2] 连接服务器成功！\n";
        $is_connect = true;
    }

    echo '客户端：';
    $input = trim(fgets(STDIN));

    socket_write($socket, $input, strlen($input)) or exit();
    if ($output = socket_read($socket, $msg_max_len)) {
        echo "服务端：" . $output . "\n";
    }
    socket_close($socket);
} while (true);
?>
