<?php
session_start();

// 记录登出活动
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $logEntry = "logout|{$username} 退出系统|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
}

// 销毁会话
session_destroy();

// 重定向到登录页
header('Location: login.php');
exit;