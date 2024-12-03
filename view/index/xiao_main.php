<?php $codeuse=1; $emailuse=1; $directoryPath = '../../';include("../../core/xiaocore.php"); ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>小猫咪抽奖系统</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="小猫咪抽奖系统,年会抽奖系统,节日抽奖系统,双十一活动,618活动,双十二活动">
    <meta name="description" content="小猫咪抽奖系统，一款开源免费的php抽奖系统，可用于年会抽奖，节日抽奖等等，支持自定义奖品概率和数量，页面简介美观，操作容易">
    <meta name="author" content="云猫">
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <link href="../../css/materialdesignicons.min.css" rel="stylesheet">
    <link href="../../css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../js/jconfirm/jquery-confirm.min.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
  
<body>
<div class="container-fluid p-t-15">
  <div class="row">
    <div class="col-xs-12">
      <div class="card">
        <div class="card-header">
            <h4>公告</h4>
        </div>
        <div class="card-body">
            <?php echo ($info['announcement']); ?>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-xs-12">
      <div class="card">
        <div class="card-header">
            <h4>开始抽奖</h4>
        </div>
        <div class="card-body">
          <form action="" method="post" id="lotteryForm">
              <div class="form-group has-feedback feedback-left">
                  <input type="email" placeholder="请输入邮箱" class="form-control" name="email" id="email" required />
                  <span class="mdi mdi-email form-control-feedback" aria-hidden="true"></span>
              </div>
              <?php if ($info['update1'] == 1): ?>
                  <div class="form-group has-feedback feedback-left">
                  <input type="text" placeholder="兑换码/卡密" class="form-control" name="kami" id="kami" required />
                  <span class="mdi mdi-code-equal form-control-feedback" aria-hidden="true"></span>
              </div>
              <?php endif; ?>
              
              <?php if ($info['emailsend'] == 1): ?>
             <div class="form-group has-feedback feedback-left" id="verification-section">
    <input type="text" placeholder="输入验证码" class="form-control" id="verification-code" name="verification-code" required />
    <span class="mdi mdi-key form-control-feedback" aria-hidden="true"></span>
    <?php if ($info['cfcode'] == 1): ?>
    <div class="cf-turnstile" 
     data-sitekey="<?php echo ($info['sitekey']); ?>" 
     data-callback="turnstileCallback"></div><?php endif; ?>
    <button type="button" class="btn btn-primary" id="send-code">发送验证码</button>
    </div>

              <?php endif; ?>

              <div class="form-group has-feedback feedback-left">
                  <input type="text" placeholder="备注" class="form-control" id="text" name="text" />
                  <span class="mdi mdi-message-reply-text form-control-feedback" aria-hidden="true"></span>
              </div>

              <div class="form-group">
                  <button class="btn btn-block btn-primary" type="submit">抽奖</button>
              </div>
          </form>
        </div>
      </div>
    </div>   
  </div>
</div>

<script type="text/javascript" src="https://cdn.lwcat.cn/jquery/jquery.js"></script>
<script src="../../js/bootstrap.min.js"></script>
<script src="../../js/jconfirm/jquery-confirm.min.js"></script>
<script src="../../js/main.min.js"></script>

<script>
let cfResponse = '';
function turnstileCallback(token) {
    cfResponse = token;
}
$(document).ready(function () {
    $('#send-code').on('click', function () {
        var email = $('#email').val();
        if (!email) {
            $.alert({
                title: '提示',
                content: '请输入邮箱后再发送验证码。',
                type: 'red',
            });
            return;
        }
        <?php if ($info['cfcode'] == 1): ?>
        if (!cfResponse) {
            $.alert({
                title: '提示',
                content: '请完成验证码验证。',
                type: 'red',
            });
            return;
        }
        <?php endif; ?>
        $.ajax({
            url: 'xiao_email.php?act=send',
            type: 'POST',
            data: { email: email, cf: cfResponse },
            dataType: 'json',
            success: function (response) {
                if (response.code === 1) {
                    $.alert({
                        title: '成功',
                        content: '验证码已发送，请检查您的邮箱。',
                        type: 'green',
                    });
                } else {
                    $.alert({
                        title: '失败',
                        content: response.result || '发送失败，请稍后重试。',
                        type: 'red',
                    });
                }
            },
            error: function () {
                $.alert({
                    title: '错误',
                    content: '无法连接到服务器，请稍后重试。',
                    type: 'red',
                });
            }
        });
    });
});
</script>
<?php
// 获取抽奖限制
function getLotteryLimits($conn) {
    $stmt = $conn->prepare("SELECT daily_limit, total_limit, draw_count FROM lottery_limits WHERE id = ?");
    $id = 1;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        return $result->fetch_assoc();
    }
    return null;
}

// 检查抽奖限制
function checkLimits($conn, $account, $date, $daily_limit, $total_limit) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS today_count FROM lottery_logs WHERE account = ? AND date = ?");
    $stmt->bind_param("ss", $account, $date);
    $stmt->execute();
    $today_count = $stmt->get_result()->fetch_assoc()['today_count'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total_count FROM lottery_logs WHERE account = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $total_count = $stmt->get_result()->fetch_assoc()['total_count'];

    if ($daily_limit > 0 && $today_count >= $daily_limit) {
        return 1; 
    }
    if ($total_limit > 0 && $total_count >= $total_limit) {
        return 2; 
    }
    return 0; 
}

// 执行抽奖
function performLottery($conn) {
    $stmt = $conn->prepare("SELECT id, name, probability, remaining FROM prizes WHERE remaining > 0 ORDER BY id ASC");
    $stmt->execute();
    $prizes = $stmt->get_result();
    if (!$prizes) {
        return null;
    }
    
    $random = mt_rand() / mt_getrandmax();
    $cumulative = 0;

    while ($prize = $prizes->fetch_assoc()) {
        $cumulative += $prize['probability'];
        if ($random <= $cumulative) {
            $stmt_update = $conn->prepare("UPDATE prizes SET remaining = remaining - 1 WHERE id = ?");
            $stmt_update->bind_param("i", $prize['id']);
            $stmt_update->execute();
            return $prize;
        }
    }
    return null;
}

// 弹窗，调用了光年的，感觉挺好看的
function showAlert($title, $content, $type = 'red') {
    echo "<script type='text/javascript'>
            $.alert({
                title: '$title',
                content: '$content',
                type: '$type',
                btnClass: 'btn-$type',
            });
          </script>";
}

// 获取当前管理员信息
function getCurrentAdmin($conn) {
    $id = 1;
    $query = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

// 主逻辑处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查$info是否初始化
    if (!isset($info)) {
        $info = []; // 或者从数据库加载
    }

    $account = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['text']);
    $date = date("Y-m-d");
    
    $allowedDomains = explode(',', $info['allowemail'] ?? ''); 
    $emailDomain = substr(strrchr($account, "@"), 1); 

    // 检查邮箱后缀
    if (!in_array('*', $allowedDomains) && !in_array($emailDomain, $allowedDomains)) {
        showAlert('暂时不支持该邮箱', '仅支持以下邮箱后缀：' . implode(', ', $allowedDomains));
        exit;
    }

    // 验证验证码
    if (!empty($info['emailsend']) && $info['emailsend'] == 1) {
        if (!isset($_SESSION['emailcode']) || $_SESSION['emailcode'] != $_POST['verification-code']) {
            showAlert('验证码错误', '请输入从邮箱获取的正确验证码');
            exit;
        }
        unset($_SESSION['emailcode']);
    }

    // 获取抽奖限制并检查
    $limit = getLotteryLimits($conn);
    if (!$limit) {
        showAlert('错误', '无法获取抽奖限制');
        exit;
    }

    $limit_status = checkLimits($conn, $account, $date, $limit['daily_limit'], $limit['total_limit']);
    if ($limit_status === 1) {
        showAlert('抽奖次数已上限', '今天的抽奖次数已达上限，明天再来吧~');
        exit;
    } elseif ($limit_status === 2) {
        showAlert('抽奖次数已达活动上限', '本次活动的抽奖次数已用完，感谢支持~');
        exit;
    }

    //检查是否开启卡密功能
    if (!empty($info['update1']) && $info['update1'] == 1) {
        $current_admin = getCurrentAdmin($conn);
        $kami = trim($_POST['kami']);
        $cards = json_decode(gzuncompress(base64_decode($current_admin['update2'])), true);
        $cardFound = false;

        //查找卡密
        foreach ($cards as &$card) {
            if ($card['code'] === $kami && !$card['used']) {
                $card['used'] = true;
                $card['used_at'] = date('Y-m-d H:i:s');
                $cardFound = true;
                break;
            }
        }

        if ($cardFound) {
            // 更新卡密数据
            $update_query = $conn->prepare("UPDATE admins SET update2 = ? WHERE id = ?");
            $compressed_data = base64_encode(gzcompress(json_encode($cards)));
            $update_query->bind_param("ss", $compressed_data, $id);
            
            if (!$update_query->execute()) {
                showAlert('抽奖失败','未知错误，请截图联系管理员');
                exit;
            }
        } else {
            showAlert('抽奖失败','卡密无效或者已经被使用');
            exit;
        }
    }

    // 执行抽奖
    $won_prize = performLottery($conn);
    if (!$won_prize) {
        showAlert('抽奖失败', '发生了未知错误，请稍后再试');
        exit;
    }

    // 记录抽奖日志
    $stmt = $conn->prepare("INSERT INTO lottery_logs (account, prize_id, message, date) VALUES (?, ?, ?, ?)");
    $prize_id = $won_prize ? $won_prize['id'] : null;
    $stmt->bind_param("siss", $account, $prize_id, $message, $date);
    $stmt->execute();

    // 更新抽奖次数
    $stmt_update_limit = $conn->prepare("UPDATE lottery_limits SET draw_count = draw_count + 1 WHERE id = ?");
    $stmt_update_limit->bind_param("i", $id);
    $stmt_update_limit->execute();

    // 显示抽奖结果
    $result = $won_prize ? "恭喜中奖：" . $won_prize['name'] : "很遗憾，未中奖";
    showAlert('抽奖结果', $result, $won_prize ? 'green' : 'red');

    // 发送邮件（如果启用了）
    if (!empty($info['emailsend']) && $info['emailsend'] == 1) {
        $subject = "抽奖结果";
        $htmlBody = "<p>本次抽奖结果：{$result}</p><p>你的抽奖邮箱：{$account}</p><p>你的留言信息：{$message}</p>";
        $altBody = "本次抽奖结果：{$result}\n你的抽奖邮箱：{$account}\n你的留言信息：{$message}";
        echo send($account, '用户', $subject, $htmlBody, $altBody, $info);
    }

    $conn->close();
}
?>


</body>
</html>