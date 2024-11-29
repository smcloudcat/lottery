<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

$delete_message = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_logs'])) {
    if ($conn->query("DELETE FROM lottery_logs") === TRUE) {
        $delete_message = "记录已经清空";
    } else {
        $delete_message = "清空记录失败: " . $conn->error;
    }
}


$sql = "SELECT logs.id, logs.account, logs.date, logs.message, prizes.name AS prize_name 
        FROM lottery_logs AS logs 
        LEFT JOIN prizes ON logs.prize_id = prizes.id 
        ORDER BY logs.date DESC, logs.message DESC, logs.id DESC";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>抽奖记录</title>
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
                    <h4>抽奖记录</h4>
                </div>
                <div class="card-body">
                    <?php if ($delete_message !== null): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($delete_message); ?></div>
                    <?php endif; ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>记录ID</th>
                                <th>账号</th>
                                <th>奖品名称</th>
                                <th>用户留言</th>
                                <th>抽奖日期</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['account']); ?></td>
                                        <td><?php echo $row['prize_name'] ? htmlspecialchars($row['prize_name']) : '未中奖'; ?></td>
                                        <td><?php echo $row['message']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">暂无记录</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <a href="xiao_main.php" class="btn btn-default">返回后台管理</a>
                    <form method="POST" onsubmit="return confirm('确定要清空所有抽奖记录吗？');" style="display: inline;">
                        <button type="submit" name="clear_logs" class="btn btn-danger">清空抽奖记录</button>
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

<?php
$conn->close();
?>