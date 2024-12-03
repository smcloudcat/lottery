<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $prize_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM prizes WHERE id = ?");
    $stmt->bind_param("i", $prize_id);

    if ($stmt->execute()) {
        $delete_message = "奖品删除成功！";
    } else {
        $delete_message = "删除奖品时出错，请稍后重试。";
    }
    $stmt->close();
}

$prizes = $conn->query("SELECT * FROM prizes");
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

    <!-- 奖品列表 -->
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>现有奖品</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($delete_message)): ?>
                        <div class="alert alert-info"><?php echo $delete_message; ?></div>
                    <?php endif; ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>奖品ID</th>
                                <th>奖品名称</th>
                                <th>中奖概率</th>
                                <th>剩余数量</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($prize = $prizes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $prize['id']; ?></td>
                                <td><?php echo $prize['name']; ?></td>
                                <td><?php echo $prize['probability']; ?></td>
                                <td><?php echo $prize['remaining']; ?></td>
                                <td>
                                    <a href="xiao_prize.php?id=<?php echo $prize['id']; ?>" class="btn btn-info btn-sm">编辑</a>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $prize['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('确定删除该奖品吗？')">删除</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript" src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/main.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>