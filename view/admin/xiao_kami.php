<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

$current_admin_query = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$current_admin_query->bind_param("s", $_SESSION['admin_username']);
$current_admin_query->execute();
$current_admin = $current_admin_query->get_result()->fetch_assoc();
$current_admin_query->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update1 = $_POST['update1'] ?? 0; // 卡密抽奖开关

    // 更新卡密抽奖开关
    $update_query = $conn->prepare(
        "UPDATE admins 
        SET update1 = ? 
        WHERE username = ?"
    );
    $update_query->bind_param("ss", $update1, $_SESSION['admin_username']);

    if ($update_query->execute()) {
        echo "<script>alert('配置修改成功！'); window.location.href = 'xiao_kami.php';</script>";
    } else {
        echo "<script>alert('更新失败，请稍后再试。');</script>";
    }
    $update_query->close();
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>卡密抽奖开关</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
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
                    <h4>卡密抽奖开关</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <!-- 卡密抽奖开关 -->
                        <div class="form-group">
                            <label>卡密抽奖开关</label>
                            <div class="controls-box">
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="update1" value="1" <?php if ($current_admin['update1'] == 1) echo 'checked'; ?>><span>开启</span>
                                </label>
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="update1" value="0" <?php if ($current_admin['update1'] != 1) echo 'checked'; ?>><span>关闭</span>
                                </label>
                                <small class="help-block">开启后需要使用卡密才能抽奖</small>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">保存修改</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/jquery.min.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script src="../../js/main.min.js"></script>
</body>
</html>