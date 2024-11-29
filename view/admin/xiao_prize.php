<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

$prize_id = $_GET['id'];
$prize = $conn->query("SELECT * FROM prizes WHERE id = $prize_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $probability = $_POST['probability'];
    $total = $_POST['total'];
    $remaining = max(0, $total - ($prize['total'] - $prize['remaining'])); 

    $conn->query("UPDATE prizes SET name='$name', probability=$probability, total=$total, remaining=$remaining WHERE id=$prize_id");
    header("Location: xiao_manage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>编辑奖品</title>
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
                    <h4>编辑奖品</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group has-feedback feedback-left">
                            <label>奖品名称</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($prize['name']); ?>" required>
                            <span class="mdi mdi-gift form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <label>中奖概率 (0~1)</label>
                            <input type="number" name="probability" step="0.01" class="form-control" value="<?php echo $prize['probability']; ?>" required>
                            <span class="mdi mdi-percent form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <div class="form-group has-feedback feedback-left">
                            <label>奖品总数量</label>
                            <input type="number" name="total" class="form-control" value="<?php echo $prize['total']; ?>" required>
                            <span class="mdi mdi-counter form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">保存修改</button>
                    </form>
                    <br>
                    <a href="xiao_manage.php" class="btn btn-default btn-block">返回奖品管理</a>
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

<?php $conn->close(); ?>
