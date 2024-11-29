<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

// 处理表单提交请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $probability = $_POST['probability'];
    $total = $_POST['total'];

    // 插入奖品数据
    $conn->query("INSERT INTO prizes (name, probability, total, remaining) VALUES ('$name', $probability, $total, $total)");

    // 重定向到管理页面
    $delete_message = "添加奖品成功！";
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>添加奖品</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="LightYear,光年,后台模板,后台管理系统,光年HTML模板">
    <meta name="description" content="LightYear是一个基于Bootstrap v3.3.7的后台管理系统的HTML模板。">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>添加奖品</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($delete_message)): ?>
                        <div class="alert alert-info"><?php echo $delete_message; ?></div>
                    <?php endif; ?>
                    <form action="xiao_addprize.php" method="POST">
                        <div class="form-group has-feedback feedback-left">
                            <input type="text" name="name" placeholder="奖品名称" class="form-control" required>
                            <span class="mdi mdi-gift form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <input type="number" name="probability" step="0.01" placeholder="中奖概率 (0~1)" class="form-control" required>
                            <span class="mdi mdi-percent form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <input type="number" name="total" placeholder="奖品数量" class="form-control" required>
                            <span class="mdi mdi-counter form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">添加奖品</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/main.min.js"></script>
</body>
</html>
