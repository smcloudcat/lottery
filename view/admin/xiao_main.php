<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

// 获取奖品列表
$prizes = $conn->query("SELECT * FROM prizes");
if (!$prizes) {
    die("数据库查询奖品列表失败: " . $conn->error);
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $daily_limit = isset($_POST['daily_limit']) ? (int)$_POST['daily_limit'] : 0;
    $total_limit = isset($_POST['total_limit']) ? (int)$_POST['total_limit'] : 0;

    // 如果没有提供必要的限制值，提前终止
    if ($daily_limit === 0 && $total_limit === 0) {
        echo "请输入有效的抽奖限制值。";
        exit;
    }

    // 使用预处理语句更新或插入数据
    $limit_check = $conn->query("SELECT * FROM lottery_limits WHERE id = 1");

    if (!$limit_check) {
        die("查询 lottery_limits 表失败: " . $conn->error);
    }

    if ($limit_check->num_rows > 0) {
        // 更新现有记录
        $stmt = $conn->prepare("UPDATE lottery_limits SET daily_limit = ?, total_limit = ?, draw_count = 0 WHERE id = 1");
        if (!$stmt) {
            die("准备更新语句失败: " . $conn->error);
        }
        $stmt->bind_param("ii", $daily_limit, $total_limit);
    } else {
        // 插入新记录
        $stmt = $conn->prepare("INSERT INTO lottery_limits (daily_limit, total_limit, draw_count) VALUES (?, ?, 0)");
        if (!$stmt) {
            die("准备插入语句失败: " . $conn->error);
        }
        $stmt->bind_param("ii", $daily_limit, $total_limit);
    }

    if ($stmt->execute()) {
        header("Location: xiao_main.php");
        exit;
    } else {
        echo "设置抽奖限制时出错，请稍后重试。";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>小猫咪抽奖系统 - 后台管理</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid p-t-15">
    <!-- 页面标题 -->
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>后台管理</h4>
                </div>
                <div class="card-body">
                    <p>欢迎使用小猫咪抽奖系统后台管理</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 设置抽奖限制 -->
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>设置抽奖限制</h4>
                </div>
                <div class="card-body">
                    <form action="xiao_main.php" method="POST">
                        <div class="form-group">
                            <input type="number" name="daily_limit" placeholder="每日限制次数" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="number" name="total_limit" placeholder="总限制次数" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">设置</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript" src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/main.min.js"></script>
</body>
</html>
