<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

// 检查当前管理员账号信息
$current_admin_query = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$current_admin_query->bind_param("s", $_SESSION['admin_username']);
$current_admin_query->execute();
$current_admin = $current_admin_query->get_result()->fetch_assoc();
$current_admin_query->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // 验证输入是否为空
    if (empty($new_username) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('所有字段都是必填的！');</script>";
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo "<script>alert('两次密码不一致'); window.location.href = '';</script>";
    }

    $encrypted_password = md5(md5($new_password));

    $update_query = $conn->prepare("UPDATE admins SET username = ?, password = ? WHERE username = 'admin'");
    $update_query->bind_param("ss", $new_username, $encrypted_password);
    if ($update_query->execute()) {
        echo "<script>alert('密码修改成功！'); window.location.href = '';</script>";
    } else {
        echo "<script>alert('密码修改失败！'); window.location.href = '';</script>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>修改管理员账号密码</title>
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
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
                    <h4>修改管理员账号密码</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group has-feedback feedback-left">
                            <label>当前用户名</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($current_admin['username']); ?>" required readonly>
                            <span class="mdi mdi-account form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <label>新密码</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <span class="mdi mdi-lock form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <label>确认新密码</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                            <span class="mdi mdi-lock-check form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">保存修改</button>
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
