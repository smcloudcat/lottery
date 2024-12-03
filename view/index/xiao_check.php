<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
session_start();
include("../../core/xiaocore.php");

$userEmail = isset($_POST['email']) ? trim($_POST['email']) : '';
$results = [];

if ($userEmail) {
    $stmt = $conn->prepare("
        SELECT logs.id, logs.account, logs.date, logs.message, prizes.name AS prize_name 
        FROM lottery_logs AS logs 
        LEFT JOIN prizes ON logs.prize_id = prizes.id 
        WHERE logs.account = ? AND logs.prize_id IS NOT NULL 
        ORDER BY logs.date DESC, logs.message DESC , logs.id DESC
    ");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $results = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>查询用户中奖记录</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../js/jconfirm/jquery-confirm.min.css">
</head>

<body>
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>查询用户中奖记录</h4>
                </div>
                <div class="card-body">
                    <!-- 查询表单 -->
                    <form method="POST">
                        <div class="form-group has-feedback feedback-left">
                            <input type="email" name="email" placeholder="用户邮箱" class="form-control" required value="<?php echo htmlspecialchars($userEmail); ?>">
                            <span class="mdi mdi-email form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">查询</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row p-t-15">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>查询结果（仅显示中奖记录）</h4>
                </div>
                <div class="card-body">
                    <?php if ($userEmail): ?>
                        <?php if ($results->num_rows > 0): ?>
                            <h5>用户 <?php echo htmlspecialchars($userEmail); ?> 的中奖记录：</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>抽奖邮箱</th>
                                        <th>奖品名称</th>
                                        <th>留言记录</th>
                                        <th>抽奖日期</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $results->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['account']); ?></td>
                                            <td><?php echo htmlspecialchars($row['prize_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                                            <td><?php echo $row['date']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-warning">用户 <?php echo htmlspecialchars($userEmail); ?> 没有中奖记录。</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 确保 jQuery 加载顺序 -->
<script type="text/javascript" src="https://cdn.lwcat.cn/jquery/jquery.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script src="../../js/jconfirm/jquery-confirm.min.js"></script>
<script src="../../js/main.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if ($userEmail): ?>
            <?php if ($results->num_rows > 0): ?>
                $.alert({
                    title: '查询成功',
                    content: '查询成功',
                    type: 'green',
                });
            <?php else: ?>
                $.alert({
                    title: '该邮箱无中奖记录',
                    content: '该邮箱无中奖记录',
                    type: 'orange',
                });
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
