<?php $codeuse=1; $emailuse=1; $directoryPath = '../../';include("../../core/xiaocore.php"); ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>小猫咪抽奖系统</title>
    <link rel="icon" href="favicon.ico" type="image/ico">
    <meta name="keywords" content="LightYear,光年,后台模板,后台管理系统,光年HTML模板">
    <meta name="description" content="LightYear是一个基于Bootstrap v3.3.7的后台管理系统的HTML模板。">
    <meta name="author" content="yinqi">
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
    return $result->fetch_assoc();
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

// 主逻辑处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['text']);
    $date = date("Y-m-d");
    
    $allowedDomains = explode(',', $info['allowemail']); 
    $emailDomain = substr(strrchr($account, "@"), 1); 

    if (!in_array('*', $allowedDomains) && !in_array($emailDomain, $allowedDomains)) {
        showAlert('暂时不支持该邮箱','仅支持以下邮箱后缀：' . implode(', ', $allowedDomains));
        exit;
    }

    if (!empty($info['emailsend']) && $info['emailsend'] == 1) {
        $code = $_POST['verification-code'];
        if (empty($_SESSION['emailcode']) || $_SESSION['emailcode'] != $code) {
            showAlert('验证码错误', '请输入从邮箱获取的正确验证码');
            exit;
        }
        unset($_SESSION['emailcode']);
    }

    $limit = getLotteryLimits($conn);
    $limit_status = checkLimits($conn, $account, $date, $limit['daily_limit'], $limit['total_limit']);

    if ($limit_status === 1) {
        showAlert('抽奖次数已上限', '今天的抽奖次数已达上限，明天再来吧~');
        exit;
    } elseif ($limit_status === 2) {
        showAlert('抽奖次数已达活动上限', '本次活动的抽奖次数已用完，感谢支持~');
        exit;
    }

    $won_prize = performLottery($conn);

    $stmt = $conn->prepare("INSERT INTO lottery_logs (account, prize_id, message, date) VALUES (?, ?, ?, ?)");
    $prize_id = $won_prize ? $won_prize['id'] : null;
    $stmt->bind_param("siss", $account, $prize_id, $message, $date);
    $stmt->execute();

    $stmt_update_limit = $conn->prepare("UPDATE lottery_limits SET draw_count = draw_count + 1 WHERE id = ?");
    $id = 1;
    $stmt_update_limit->bind_param("i", $id);
    $stmt_update_limit->execute();

    $result = $won_prize ? "恭喜中奖：" . $won_prize['name'] : "很遗憾，未中奖";
    showAlert('抽奖结果', $result, $won_prize ? 'green' : 'red');

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