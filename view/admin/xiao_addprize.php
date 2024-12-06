<?php
$codeuse = 0; 
$emailuse = 0;
$directoryPath = '../../';
include("../../core/xiaocore.php");

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $probability = floatval($_POST['probability']);
    $total = intval($_POST['total']);

    if ($probability < 0 || $probability > 1) {
        $error_message = "中奖概率必须在0到1之间！";
    } elseif ($total <= 0) {
        $error_message = "奖品数量必须大于0！";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO prizes (name, probability, total, update41, update42, update43) 
                                    VALUES (?, ?, ?, '', '', '')");
            $stmt->bind_param("sdi", $name, $probability, $total);

            if ($stmt->execute()) {
                $success_message = "添加奖品成功！";
            } else {
                $error_message = "添加奖品失败！";
            }

            $stmt->close();
        } catch (Exception $e) {
            $error_message = "发生错误：" . $e->getMessage();
        }
    }

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
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
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
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-info"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
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