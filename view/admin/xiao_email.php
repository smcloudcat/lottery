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
    $emailsend = trim($_POST['emailsend'] ?? '');
    $emailtype = trim($_POST['emailtype'] ?? '');
    $stmp = trim($_POST['stmp'] ?? '');
    $port = trim($_POST['port'] ?? '');
    $sec = trim($_POST['sec'] ?? '');
    $emailname = trim($_POST['emailname'] ?? '');
    $emailpass = trim($_POST['emailpass'] ?? '');
    $cfcode = trim($_POST['cfcode'] ?? '');
    $sitekey = trim($_POST['sitekey'] ?? '');
    $secretKey = trim($_POST['secretKey'] ?? '');

    if (empty($stmp) || empty($port) || empty($sec) || empty($emailname)) {
        echo "<script>alert('所有字段都是必填的！');window.location.href = 'xiao_email.php';</script>";
        exit;
    }

    $update_query = $conn->prepare(
        "UPDATE admins 
        SET emailsend = ?, emailtype = ?, stmp = ?, port = ?, sec = ?, emailname = ?, emailpass = ?, 
            cfcode = ?, sitekey = ?, secretKey = ? 
        WHERE username = ?"
    );
    $update_query->bind_param(
        "sssssssssss", 
        $emailsend, $emailtype, $stmp, $port, $sec, $emailname, $emailpass, $cfcode, $sitekey, $secretKey, $_SESSION['admin_username']
    );

    if ($update_query->execute()) {
        echo "<script>alert('配置修改成功！'); window.location.href = 'xiao_email.php';</script>";
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
    <title>邮件配置修改</title>
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
                    <h4>邮件配置修改</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <!-- 邮件相关设置 -->
                        <div class="form-group">
                            <label>邮件开关</label>
                            <div class="controls-box">
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="emailsend" value="1" <?php if ($current_admin['emailsend'] == 1) echo 'checked'; ?>><span>开启</span>
                                </label>
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="emailsend" value="0" <?php if ($current_admin['emailsend'] == 0) echo 'checked'; ?>><span>关闭</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>发件方式</label>
                            <div class="controls-box">
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="emailtype" value="1" <?php if ($current_admin['emailtype'] == 1) echo 'checked'; ?>><span>本地发件</span>
                                </label>
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="emailtype" value="1" <?php if ($current_admin['emailtype'] == 0) echo 'checked'; ?>><span>云端发件（没写完，别选）</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>SMTP地址</label>
                            <input type="text" name="stmp" class="form-control" value="<?php echo htmlspecialchars($current_admin['stmp']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>端口</label>
                            <input type="number" name="port" class="form-control" value="<?php echo htmlspecialchars($current_admin['port']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>加密方式</label>
                            <input type="text" name="sec" class="form-control" value="<?php echo htmlspecialchars($current_admin['sec']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>邮箱帐号</label>
                            <input type="email" name="emailname" class="form-control" value="<?php echo htmlspecialchars($current_admin['emailname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>邮箱密码</label>
                            <input type="password" name="emailpass" class="form-control" value="<?php echo htmlspecialchars($current_admin['emailpass']); ?>" required>
                        </div>

                        <!-- cf-turnstile 配置 -->
                        <div class="form-group">
                            <label>cf-turnstile</label>
                            <div class="controls-box">
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="cfcode" value="1" <?php if ($current_admin['cfcode'] == 1) echo 'checked'; ?>><span>开启</span>
                                </label>
                                <label class="lyear-radio radio-inline radio-primary">
                                    <input type="radio" name="cfcode" value="0" <?php if ($current_admin['cfcode'] == 0) echo 'checked'; ?>><span>关闭</span>
                                </label>
                                <small class="help-block">这个是发件验证，可以防止刷邮件和刷抽奖，需要对接cloudflare的turnstile<br>密匙获取方法请自行百度哦</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>cf-turnstile——sitekey（站点密匙）</label>
                            <input type="text" name="sitekey" class="form-control" value="<?php echo htmlspecialchars($current_admin['sitekey']); ?>" >
                            <small class="help-block">就是短的那个</small>
                        </div>
                        <div class="form-group">
                            <label>cf-turnstile——secretKey（密匙）</label>
                            <input type="text" name="secretKey" class="form-control" value="<?php echo htmlspecialchars($current_admin['secretKey']); ?>" >
                            <small class="help-block">就是长的那个</small>
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