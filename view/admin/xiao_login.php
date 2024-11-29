<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>登录页面 - 小猫咪抽奖系统</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="LightYear,光年,后台模板,后台管理系统,光年HTML模板">
    <meta name="description" content="LightYear是一个基于Bootstrap v3.3.7的后台管理系统的HTML模板。">
    <meta name="author" content="yinqi">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../js/jconfirm/jquery-confirm.min.css">
    <link href="../../css/style.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background-image: url(../../images/login-bg-2.jpg);
            background-size: cover;
        }
        .login-box {
            display: table;
            max-width: 700px;
            overflow: hidden;
            table-layout: fixed;
            background: #ffffff;
        }
        .login-left {
            display: table-cell;
            padding: 45px;
        }
        .login-right {
            display: table-cell;
            width: 50%;
            background: linear-gradient(45deg, #67b26f 0, #4ca2cd 100%);
            color: white;
            padding: 45px;
        }
        @media (max-width: 576px) {
            .login-right { display: none; }
        }
    </style>
</head>
<body>
<div class="login-box bg-white clearfix">
    <div class="login-left">
        <form action="xiao_login.php" method="post">
            <div class="form-group has-feedback feedback-left">
                <input type="text" placeholder="请输入您的用户名" class="form-control" name="username" id="username" required />
                <span class="mdi mdi-account form-control-feedback" aria-hidden="true"></span>
            </div>
            <div class="form-group has-feedback feedback-left">
                <input type="password" placeholder="请输入密码" class="form-control" id="password" name="password" required />
                <span class="mdi mdi-lock form-control-feedback" aria-hidden="true"></span>
            </div>
            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit">立即登录</button>
            </div>
        </form>
    </div>
    <div class="login-right">
        <p><img src="https://lwcat.cn/usr/uploads/2024/11/162820287.png" class="m-b-md m-t-xs" alt="logo"></p>
        <p>小猫咪抽奖系统</p>
        <p>&copy; 2024 <a href="http://lwcat.cn" class="text-white">云猫</a>. All rights reserved.</p>
    </div>
</div>
<script src="../../js/jquery.min.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script src="../../js/jconfirm/jquery-confirm.min.js"></script>
<script type="text/javascript" src="../../js/main.min.js"></script>
<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    
    $password =  md5(md5($_POST['password'])); 

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: xiao_main.php");
        exit();
    } else {
        echo "<script type='text/javascript'>
                    $.alert({
        title: '密码错误    ',
        content: '如果忘记了密码，可以去数据库重置密码',
        type: 'red',
         btnClass: 'btn-red',
    });
              </script>";
    }
    $stmt->close();
    $conn->close();
}
?>
</body>
</html>