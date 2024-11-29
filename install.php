<?php
/**
 * BY:云猫
 * CC的小窝
 * 这是一个十分好看并且简介的数据库导入页面QAQ
 */
if (empty($_POST['db'])) { ?>
    <!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>小猫咪程序安装</title>
<meta name="Keywords" content="小猫咪程序安装">
<meta name="description" content="小猫咪程序安装">
<meta name="applicable-device" content="mobile" />
<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Cache-Control" content="no-transform" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="stylesheet" href="https://cdn.lwcat.cn/layui/css/layui.css">
<style>
    body {
        background: url('https://img.czzu.cn/u/install/qvhZeRv0.png') no-repeat center center fixed;
        background-size: cover;
    }
    .nav-box {
        background: rgba(255, 255, 255, 0.3);
        padding: 10px;
        border-radius: 10px;
        margin: 20px;
    }
    .content-box {
        background: rgba(255, 255, 255, 0.6);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 20px;
    }
    .content-box h2 {
        text-align: center;
    }
    .transparent-input {
        background: rgba(255, 255, 255, 0.3);
        border: none;
        padding: 10px;
        border-radius: 5px;
    }
    .transparent-table {
        background: rgba(255, 255, 255, 0.2);
        padding-left: 20px;
    }
    .footer {
        text-align: center;
        margin: 20px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
    }
</style>
</head>
<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="content-box">
            <h2>小猫咪程序安装</h2>
            <h3>注意：请务必使用空的数据库来安装，点击安装会清空你以前的数据库！！</h3>
            <form class="layui-form" method="post" action="install.php">
                <div class="layui-form-item">
                    <label class="layui-form-label">Host</label>
                    <div class="layui-input-block">
                        <input type="text" name="host" value="localhost" required lay-verify="required" placeholder="Enter Database Host" class="layui-input transparent-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">数据库名</label>
                    <div class="layui-input-block">
                        <input type="text" name="db" required lay-verify="required" placeholder="Enter Database Name" class="layui-input transparent-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" name="user" required lay-verify="required" placeholder="Enter Database User" class="layui-input transparent-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="pass" required lay-verify="required" placeholder="Enter Database Password" class="layui-input transparent-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">Port</label>
                    <div class="layui-input-block">
                        <input type="text" name="port" value="3306" required lay-verify="required" placeholder="Enter Database Port" class="layui-input transparent-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn">安装导入</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="footer">
        &copy; 2024 云猫. All rights reserved.
    </div>
</div>
</body>
</html>
<?php
} else {
    $host = $_POST['host'] ?? 'localhost';
    $db = $_POST['db'] ?? '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $port = $_POST['port'] ?? '3306';
    $charset = 'utf8mb4';
    if (file_exists('install.lock')) {
        die('<script>function autoPopup() {if (confirm("安装失败，请删除insatll.lock文件再来安装")) {window.location.href = "install.php";}}window.onload = autoPopup;</script>');
    }
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = file_get_contents('core/install.sql');
        preg_match_all('/CREATE TABLE `([^`]+)`/', $sql, $matches);
        $tables = $matches[1];
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
        }
        $pdo->exec($sql);
        $configContent = file_get_contents('core/config.php');
        $configContent = preg_replace('/\$host = \'localhost\';/', "\$host = '$host';", $configContent);
        $configContent = preg_replace('/\$db = \'\';/', "\$db = '$db';", $configContent);
        $configContent = preg_replace('/\$user = \'\';/', "\$user = '$user';", $configContent);
        $configContent = preg_replace('/\$pass = \'\';/', "\$pass = '$pass';", $configContent);
        file_put_contents('core/config.php', $configContent);
        file_put_contents('core/install.lock', '');
        echo ' <script>function autoPopup() {if (confirm("安装完成啦~正在前往后台")) {window.location.href = "admin.html";}}window.onload = autoPopup;</script>';
    } catch (PDOException $e) {
        echo ' <script>function autoPopup() {if (confirm("' . $e->getMessage() . '")) {window.location.href = "install.php";}}window.onload = autoPopup;</script>';
    }
}
?>