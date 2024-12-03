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
    // 获取输入数据
    $title = trim($_POST['title'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $announcement = trim($_POST['announcement'] ?? '');
    $foot = trim($_POST['foot'] ?? '');
    $logo = trim($_POST['logo'] ?? '');
    $avatar = trim($_POST['avatar'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $allowemail = trim($_POST['allowemail'] ?? '');

    // 验证输入是否为空
    if (empty($title) || empty($keywords) || empty($description) || empty($foot) || empty($logo) || empty($avatar) || empty($name) || empty($allowemail)) {
        $delete_message = "所有字段都是必填的！";
    } else {
        // 更新站点信息
        $update_query = $conn->prepare(
            "UPDATE admins SET title = ?, keywords = ?, description = ?, announcement = ?, foot = ?, logo = ?, avatar = ?, name = ?, allowemail = ? WHERE id = 1"
        );
        $update_query->bind_param(
            "sssssssss",
            $title,
            $keywords,
            $description,
            $announcement,
            $foot,
            $logo,
            $avatar,
            $name,
            $allowemail
        );

        if ($update_query->execute()) {
            $delete_message = "更新成功！请刷新网页";
        } else {
            $delete_message = "更新失败，请稍后再试。";
        }
        $update_query->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>站点信息修改</title>
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
                    <h4>站点信息修改</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($delete_message)): ?>
                        <div class="alert alert-info"><?php echo $delete_message; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>网站标题</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($current_admin['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>网站关键词（多个用英文,隔开）</label>
                            <input type="text" name="keywords" class="form-control" value="<?php echo htmlspecialchars($current_admin['keywords']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">站点描述</label>
                            <textarea class="form-control" id="description" rows="5" name="description" required><?php echo htmlspecialchars($current_admin['description']); ?></textarea>
                            <small class="help-block">网站描述，有利于搜索引擎抓取相关信息</small>
                        </div>
                        <div class="form-group">
                            <label for="announcement">站点公告</label>
                            <textarea class="form-control" id="announcement" rows="5" name="announcement" required><?php echo htmlspecialchars($current_admin['announcement']); ?></textarea>
                            <small class="help-block">首页公告，可用来介绍活动等</small>
                        </div>
                        <div class="form-group">
                            <label for="foot">网站底部信息</label>
                            <textarea class="form-control" id="foot" rows="5" name="foot" required><?php echo htmlspecialchars($current_admin['foot']); ?></textarea>
                            <small class="help-block">如备案信息等</small>
                        </div>
                        <div class="form-group">
                            <label>网站logo</label>
                            <input type="text" name="logo" class="form-control" value="<?php echo htmlspecialchars($current_admin['logo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>站长头像</label>
                            <input type="text" name="avatar" class="form-control" value="<?php echo htmlspecialchars($current_admin['avatar']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>站长名称</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($current_admin['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>允许邮箱后缀</label>
                            <input type="text" name="allowemail" class="form-control" value="<?php echo htmlspecialchars($current_admin['allowemail']); ?>" required>
                            <small class="help-block">请填写允许抽奖的邮箱后缀，多个请用英文“,”分开，不限制请填 *</small>
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