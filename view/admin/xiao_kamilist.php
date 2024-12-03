<?php
$codeuse=0; $emailuse=0;$directoryPath = '../../';
include("../../core/xiaocore.php");

// 确保管理员登录
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: xiao_login.php');
    exit;
}

// 获取当前管理员信息
function getCurrentAdmin($conn, $username) {
    $query = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

$current_admin = getCurrentAdmin($conn, $_SESSION['admin_username']);

// 获取卡密数据
function getCardsData() {
    global $current_admin;  // 访问全局变量
    if (empty($current_admin['update1'])) {
        return [];
    }
    // 解压并解析卡密数据
    return json_decode(gzuncompress(base64_decode($current_admin['update2'])), true);
}

// 显示卡密
function displayCards($cards, $type = 'unused') {
    $filteredCards = array_filter($cards, function ($card) use ($type) {
        return ($type === 'unused' && !$card['used']) || ($type === 'used' && $card['used']);
    });

    if (count($filteredCards) > 0) {
        foreach ($filteredCards as $card) {
            $time = $type === 'unused' ? $card['generated_at'] : $card['used_at'];
            echo trim($card['code']) . " | " . ($type === 'unused' ? '生成时间' : '使用时间') . ": " . trim($time) . "\n";
        }
    } else {
        echo "没有{$type}的卡密。";
    }
}

// 处理POST请求，生成卡密
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num'])) {
    $num = intval($_POST['num']);
    $length = 16;
    $prefix = 'KM';
    $codes = [];

    // 生成卡密
    for ($i = 0; $i < $num; $i++) {
        $codes[] = [
            'code' => $prefix . strtoupper(bin2hex(random_bytes($length / 2))),
            'generated_at' => date('Y-m-d H:i:s'),
            'used' => false,
            'used_at' => null
        ];
    }

    // 更新卡密数据
    $update_query = $conn->prepare("UPDATE admins SET update2 = ? WHERE username = ?");
    $compressed_data = base64_encode(gzcompress(json_encode($codes)));  // 压缩并编码生成的卡密数据
    $update_query->bind_param("ss", $compressed_data, $_SESSION['admin_username']);

    if ($update_query->execute()) {
        echo "<script>alert('生成卡密成功！'); window.location.href = 'xiao_kamilist.php';</script>";
    } else {
        echo "<script>alert('更新失败，请稍后再试。');</script>";
    }
    $update_query->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>卡密管理</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
    <script>
        function toggleCards(type) {
            var unusedCards = document.getElementById('unused-cards');
            var usedCards = document.getElementById('used-cards');
            
            // 简化逻辑
            unusedCards.style.display = type === 'unused' ? 'block' : 'none';
            usedCards.style.display = type === 'used' ? 'block' : 'none';
        }

        function copyCards() {
            var cardsText = document.getElementById('unused-cards').innerText.trim();
            var textarea = document.createElement('textarea');
            textarea.value = cardsText;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert("已复制所有未使用的卡密！");
        }
    </script>
</head>
<body>
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>卡密生成</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="#" role="form">
                        <div class="input-group">
                            <input type="number" id="num" name="num" class="form-control" min="1" placeholder="请输入生成卡密数量" required>
                            <div class="input-group-btn">
                                <input class="btn btn-primary" type="submit" value="生成卡密">
                            </div>
                        </div>
                        <small class="help-block">生成卡密会删除以前的卡密，请慎重操作，性能差的服务器不要生成太多，否则使用时会卡顿。</small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h4>卡密管理</h4>
                </div>
                <div class="card-body">
                    <div class="toolbar-btn-action">
                        <button class="btn btn-info m-r-5" onclick="toggleCards('unused')">
                            <i class="mdi mdi-eye"></i> 查看未使用卡密
                        </button>
                        <button class="btn btn-success m-r-5" onclick="toggleCards('used')">
                            <i class="mdi mdi-check"></i> 查看已使用卡密
                        </button>
                    </div>
                    <div id="unused-cards" style="display: none;">
                        <h4>未使用的卡密</h4>
                        <button class="btn btn-primary" onclick="copyCards()">复制所有未使用卡密</button>
                        <pre><?php $cards = getCardsData(); displayCards($cards, 'unused'); ?></pre>
                    </div>

                    <div id="used-cards" style="display: none;">
                        <h4>已使用的卡密</h4>
                        <pre><?php displayCards($cards, 'used'); ?> </pre>
                    </div>
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
